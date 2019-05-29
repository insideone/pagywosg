<?php

namespace App\DataFixtures;

use App\Command\LoadSteamGamesCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class GameFixtures extends Fixture
{
    /** @var LoadSteamGamesCommand */
    protected $loadSteamGamesCommand;

    public function __construct(LoadSteamGamesCommand $loadSteamGamesCommand)
    {
        $this->loadSteamGamesCommand = $loadSteamGamesCommand;
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->loadSteamGamesCommand->run(new StringInput(''), new ConsoleOutput);
    }
}
