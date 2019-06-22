<?php

namespace App\Command;

use App\Api\SteamGifts\ProfileNameProvider;
use App\Entity\User;
use App\Framework\Command\BaseCommand;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadSteamGiftsNamesCommand extends BaseCommand
{
    /** @var ProfileNameProvider */
    protected $sgNameProvider;

    public function __construct(ProfileNameProvider $sgNameProvider)
    {
        parent::__construct();
        $this->sgNameProvider = $sgNameProvider;
    }

    protected function configure()
    {
        $this
            ->setName('steamgifts:names:load')
            ->setDescription('Loads SteamGifts profile names')
            ->getDefinition()
            ->setOptions([
                new InputOption(
                    'force', 'f',
                    InputOption::VALUE_OPTIONAL,
                    'Force updating all user steamgifts names',
                    false
                )
            ])
        ;
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $ss = new SymfonyStyle($input, $output);

        $forceMode = !!$input->getOption('force');

        $users = $this->em->getRepository(User::class)->findBy($forceMode ? [] : ['sgProfileName' => false]);

        if (!$users) {
            $ss->success("All SteamGifts profile names were already loaded");
            return;
        }

        $loadedCnt = 0;
        $namelessCnt = count($users);
        $ss->progressStart($namelessCnt);

        foreach ($users as $user) {
            $ss->progressAdvance();

            $steamId = $user->getSteamId();
            try {
                $sgProfileName = $this->sgNameProvider->provide($steamId);
            } catch (GuzzleException $e) {
                $ss->error("Name provider fails with error message: {$e->getMessage()}");
                continue;
            }

            if (!$sgProfileName) {
                $ss->warning("Can't find SteamGifts profile name for {$steamId}");
                continue;
            }

            $loadedCnt++;
            $user->setSgProfileName($sgProfileName);

            // take a little nap to not bother SG
            usleep(500000);
        }

        $ss->progressFinish();

        $this->em->flush();

        $ss->success("Names loaded: {$loadedCnt}");
    }
}
