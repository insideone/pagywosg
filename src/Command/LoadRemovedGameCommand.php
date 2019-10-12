<?php

namespace App\Command;


use App\Api\SteamSpy\AppDetailsProvider;
use App\Entity\Game;
use App\Framework\Command\BaseCommand;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadRemovedGameCommand extends BaseCommand
{
    /** @var AppDetailsProvider */
    protected $appDetailsProvider;

    public function __construct(AppDetailsProvider $appDetailsProvider)
    {
        parent::__construct();
        $this->appDetailsProvider = $appDetailsProvider;
    }

    protected function configure()
    {
        $this
            ->setName('steam:removedgame:add')
            ->setDescription('Loads a removed game to database with a provided IP')
            ->getDefinition()
            ->setOptions([
                new InputOption(
                    'gameid', 'i',
                    InputOption::VALUE_REQUIRED,
                    'The id of the removed game that we need to import',
                    false
                )
            ])
        ;
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $ss = new SymfonyStyle($input, $output);

        $gameId = $input->getOption('gameid');

        $ss->text('Trying to fetch the game from SteamSpy API');

        try {
            $appDetails = $this->appDetailsProvider->getDetails($gameId);
        } catch (Exception $e) {
            $ss->error($e->getMessage());
            return;
        }

        $game = null;

        try {
            $game = $this->em->createQueryBuilder()
                ->from(Game::class, 'game')
                ->select('game')
                ->where('game.id = :gameId')
                ->setParameter('gameId', $gameId)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }

        if ($game)
            $ss->note('This game already exists, setting status to available.');
        else
        {
            $game = new Game();
            $game->setId($appDetails->id);
        }

        $game->setName($appDetails->name)
            ->setStandalone(true);

        $this->em->persist($game);
        $this->em->flush();

        $name = $game->getName();

        $ss->success("Game loaded: {$gameId} - {$name}");
    }
}