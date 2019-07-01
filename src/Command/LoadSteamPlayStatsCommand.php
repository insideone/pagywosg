<?php

namespace App\Command;

use App\Api\Steam\OwnedGamesApiProvider;
use App\Api\Steam\PlayerAchievementsApiProvider;
use App\Api\Steam\RecentlyPlayedApiProvider;
use App\Api\Steam\Schema\Achievement;
use App\Entity\Change;
use App\Entity\EventEntry;
use App\Entity\Game;
use App\Entity\User;
use App\Framework\Command\BaseCommand;
use App\Framework\Exceptions\UnexpectedResponseException;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadSteamPlayStatsCommand extends BaseCommand
{
    const DEFAULT_UPDATE_DELAY = '1 hour';

    /** @var OwnedGamesApiProvider */
    protected $ownedGamesProvider;

    /** @var PlayerAchievementsApiProvider */
    protected $playerAchievementsProvider;

    /** @var RecentlyPlayedApiProvider */
    protected $recentlyPlayedApiProvider;

    public function __construct(
        OwnedGamesApiProvider $ownedGamesProvider,
        PlayerAchievementsApiProvider $playerAchievementsProvider,
        RecentlyPlayedApiProvider $recentlyPlayedApiProvider
    ) {
        parent::__construct();

        $this->ownedGamesProvider = $ownedGamesProvider;
        $this->playerAchievementsProvider = $playerAchievementsProvider;
        $this->recentlyPlayedApiProvider = $recentlyPlayedApiProvider;
    }

    protected function configure()
    {
        $this->setName('steam:play-stats:load')
            ->getDefinition()
            ->setOptions([
                new InputOption(
                    'all', null, InputOption::VALUE_OPTIONAL,
                    "Update all entries despite the fact of how long they haven't been updated",
                    false
                ),
                new InputOption(
                    'delay', null, InputOption::VALUE_OPTIONAL,
                    "How much time must pass before entry will need to be updated again",
                    self::DEFAULT_UPDATE_DELAY
                )
            ])
        ;
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('entry')
            ->from(EventEntry::class, 'entry')
            ->leftJoin('entry.event', 'event')
            ->where('event.endedAt > :now')
            //->andWhere('entry.verified = true')
            ->setParameter('now', new DateTime);

        if ($input->getOption('all')) {
            $this->ss->note('Fetching all entries');
        } else {
            $delay = $input->getOption('delay');
            if ($delay) {
                $this->ss->note("Fetching expired entries (delay: {$delay})");
                $qb->andWhere($qb->expr()->orX(
                    'entry.refreshedAt is null',
                    'entry.refreshedAt <= :yesterday'
                ))
                    ->setParameter('yesterday', new DateTime("-{$delay}"));
            }
        }

        /** @var EventEntry[] $entries */
        $entries = $qb->getQuery()->getResult();

        $this->ss->note('Entries to update: '.count($entries));

        if (!$entries) {
            return;
        }

        // mark as refreshed instantly
        $now = new DateTime;
        foreach ($entries as $entry) {
            $entry->setRefreshedAt($now);
        }
        $this->em->flush();

        $entryKeys = [];
        $playerIds = [];
        $gameIds = [];

        foreach ($entries as $entry) {
            $entryKeys[] = $this->getEntryKey($entry->getPlayer()->getSteamId(), $entry->getGame()->getId());
            $playerId[] = $entry->getPlayer()->getId();
            $gameIds[] = $entry->getGame()->getId();
        }

        $entries = array_combine($entryKeys, $entries);

        // prevent lazy-load
        $this->em->getRepository(User::class)->findBy(['id' => $playerIds]);
        $this->em->getRepository(Game::class)->findBy(['id' => $gameIds]);

        $playerGames = array_fill_keys($playerIds, []);
        foreach ($entries as $entry) {
            $playerId = $entry->getPlayer()->getSteamId();

            $playerGames[$playerId][] = $entry->getGame()->getId();
        }
        $playerGames = array_filter($playerGames);

        $this
            ->updatePlaytime($playerGames, $entries)
            ->updateAchievementsCount($entries);

        $this->ss->note('Flushing all to database');
        $this->em->flush();
    }

    protected function getEntryKey($playerSteamId, $gameId)
    {
        return "{$playerSteamId}:{$gameId}";
    }

    /**
     * @param array $playerGames
     * @param EventEntry[] $entries
     * @return self
     */
    protected function updatePlaytime(array $playerGames, array $entries): self
    {
        if (!$playerGames) {
            return $this;
        }

        $this->ss->note('Updating playtime');
        $this->ss->progressStart(count($playerGames));

        foreach ($playerGames as $playerSteamId => $playerGameIds) {
            try {
                $ownedGames = $this->ownedGamesProvider->fetch($playerSteamId, $playerGameIds);

                $restGames = $playerGameIds;

                foreach ($ownedGames as $ownedGame) {
                    $gameId = $ownedGame->getAppId();
                    $entryKey = $this->getEntryKey($playerSteamId, $gameId);

                    $restGames = array_diff($restGames, [$gameId]);

                    if (!isset($entries[$entryKey])) {
                        continue;
                    }

                    $this->changelog->add(
                        (new Change)
                            ->setObject($entries[$entryKey])
                            ->set('playTime', $entries[$entryKey]->getPlayTime(), $ownedGame->getPlaytimeForever())
                    );

                    $entries[$entryKey]->setPlayTime($ownedGame->getPlaytimeForever());
                }

                if ($restGames) {
                    $this->updateFromRecentlyPlayed($playerSteamId, $entries, $restGames);
                }

                if ($restGames) {
                    $this->updateFromProfile($playerSteamId, $entries, $restGames);
                }
            } catch (GuzzleException|Exception $e) {
                $this->ss->error($e->getMessage());
            } finally {
                $this->ss->progressAdvance();
            }
        }

        $this->ss->progressFinish();
        return $this;
    }

    /**
     * @param array $entries
     * @param string $playerSteamId
     * @param array $restGames
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    protected function updateFromRecentlyPlayed($playerSteamId, array &$entries, array &$restGames)
    {
        foreach ($this->recentlyPlayedApiProvider->getList($playerSteamId) as $recentlyPlayed) {
            $gameId = $recentlyPlayed->getAppid();
            if (!in_array($gameId, $restGames)) {
                continue;
            }

            $restGames = array_diff($restGames, [$gameId]);

            $entryKey = $this->getEntryKey($playerSteamId, $gameId);
            if (!isset($entries[$entryKey])) {
                continue;
            }

            $playtimeForever = $recentlyPlayed->getPlaytimeForever();

            $this->changelog->add(
                (new Change)
                    ->setObject($entries[$entryKey])
                    ->set('playTime', $entries[$entryKey]->getPlayTime(), $playtimeForever)
            );

            $entries[$entryKey]->setPlayTime($playtimeForever);
        }
    }

    protected function updateFromProfile($playerSteamId, array &$entries, array &$restGames)
    {
        // TODO: Fallback to parsing?
    }

    /**
     * @param EventEntry[] $entries
     * @return self
     */
    protected function updateAchievementsCount(array $entries): self
    {
        if (!$entries) {
            return $this;
        }

        $this->ss->note('Updating achievements count');
        $this->ss->progressStart(count($entries));

        foreach ($entries as $entry) {
            try {
                $achievements = $this->playerAchievementsProvider->fetch(
                    $entry->getPlayer()->getSteamId(),
                    $entry->getGame()->getId()
                );

                $game = $entry->getGame();
                $gameAchievementsCnt = count($achievements);

                $this->changelog->add(
                    (new Change)
                        ->setObject($game)
                        ->set('achievementsCnt', $game->getAchievementsCnt(), $gameAchievementsCnt)
                );

                // Updating total achievement count of the game on the go
                $game->setAchievementsCnt(count($achievements));

                $achievementsCnt = $achievements ? array_reduce(
                    $achievements,
                    function ($achievementsCnt, Achievement $achievement) {
                        return $achievementsCnt + ($achievement->isAchieved() ? 1 : 0);
                    }
                ) : 0;

                $this->changelog->add(
                    (new Change)
                        ->setObject($entry)
                        ->set('achievementsCnt', $entry->getAchievementsCnt(), $achievementsCnt)
                );

                $entry->setAchievementsCnt($achievementsCnt);
            } catch (GuzzleException|Exception $e) {
                $this->ss->error($e->getMessage());
            } finally {
                $this->ss->progressAdvance();
            }
        }

        $this->ss->progressFinish();
        return $this;
    }
}
