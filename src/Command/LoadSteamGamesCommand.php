<?php

namespace App\Command;

use App\Api\Steam\GameNamesInnerProvider;
use App\Api\Steam\Schema\GetAppListResultApp;
use App\Entity\Change;
use App\Entity\Game;
use App\Framework\Command\BaseCommand;
use App\Framework\Exceptions\UnexpectedResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadSteamGamesCommand extends BaseCommand
{
    const BATCH_SIZE = 10000;

    /** @var GameNamesInnerProvider */
    protected $gameNamesProvider;
    
    public function __construct(GameNamesInnerProvider $gameNamesProvider)
    {
        parent::__construct();
        
        $this->gameNamesProvider = $gameNamesProvider;
    }

    protected function configure()
    {
        $this->setName('steam:games:load')
            ->setDescription('Loads steam games list and saves it in database');
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $this->ss->note('Trying to fetch games from Steam API');

        try {
            $apps = $this->gameNamesProvider->fetch();

            $counters = [
                'created' => 0,
                'updated' => 0,
            ];

            // all games ends with 0, but sometimes DLCs too
            #$apps = array_filter($apps, function ($app) {
            #    /** @var GetAppListResultApp $app */
            #    return ($app->appid % 10) === 0;
            #});

            $actualIds = array_map(
                function ($app) {
                    /** @var GetAppListResultApp $app */
                    return $app->appid;
                },
                $apps
            );

            $apps = array_combine($actualIds, $apps);

            $this->ss->note('Preparing saved games list');

            $savedIds = array_map(
                'reset',
                $this->em->createQueryBuilder()
                    ->from(Game::class, 'game')
                    ->select('game.id')
                    ->getQuery()->getResult()
            );

            $newIds = array_diff($actualIds, $savedIds);
            $newIdsCnt = count($newIds);
            if ($newIdsCnt) {
                $this->ss->note('Preparing creating');

                $this->ss->progressStart(count($newIds));
                foreach ($newIds as $newId) {
                    $game = (new Game)
                        ->setId($newId)
                        ->setName($apps[$newId]->name)
                    ;

                    $this->changelog->add((new Change)->setObject($game, $newId)->set('name', null, $game->getName()));

                    $counters['created']++;
                    $this->em->persist($game);

                    if (($counters['created'] % self::BATCH_SIZE) === 0) {
                        $this->ss->note('Flushing batch');
                        $this->em->flush();
                        $this->em->clear();
                    }

                    $this->ss->progressAdvance();
                }
                $this->ss->progressFinish();
            }

            $updateIds = array_diff($actualIds, $newIds);
            $updateIdsCnt = count($updateIds);
            if ($updateIdsCnt > 0) {
                $this->ss->note("Checking updates for {$updateIdsCnt} title".($updateIdsCnt > 1 ? 's' : ''));

                $this->ss->progressStart($updateIdsCnt);

                $repo = $this->em->getRepository(Game::class);
                foreach ($updateIds as $gameId) {
                    $updatedGame = $repo->find($gameId);
                    $newGame = $apps[$gameId];
                    if ($updatedGame->getName() !== $newGame->name) {
                        $this->ss->note("Found a different game name for {$gameId}: {$updatedGame->getName()} -> {$newGame->name}");

                        $this->changelog->add(
                            (new Change)
                                ->setObject($updatedGame, $gameId)
                                ->set('name', $updatedGame->getName(), $newGame->name)
                        );

                        $updatedGame->setName($newGame->name);

                        $counters['updated']++;
                        if (($counters['updated'] % self::BATCH_SIZE) === 0) {
                            $this->ss->note('Flushing batch');
                            $this->em->flush();
                            $this->em->clear();
                        }
                    }
                    $this->em->detach($updatedGame);
                    $this->ss->progressAdvance();
                }
                $this->ss->progressFinish();
            }

            if (array_sum($counters)) {
                $this->ss->note('Flushing rests');
                $this->em->flush();

                foreach ($counters as $counter => $value) {
                    $this->ss->note("{$value} {$counter}");
                }
            } else {
                $this->ss->note('We have no changes');
            }
        } catch (GuzzleException|UnexpectedResponseException $e) {
            $this->ss->error($e->getMessage());
        }
    }
}
