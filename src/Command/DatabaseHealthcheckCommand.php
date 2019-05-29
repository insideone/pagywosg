<?php

namespace App\Command;

use App\Framework\Command\BaseCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseHealthcheckCommand extends BaseCommand
{
    const RETRIES = 0;
    const INTERVAL = 3;

    protected function configure()
    {
        $this
            ->setName('database:healthcheck')
            ->getDefinition()
            ->addOptions([
                new InputOption('retries', null, InputOption::VALUE_OPTIONAL, '', self::RETRIES),
                new InputOption('interval', null, InputOption::VALUE_OPTIONAL, '', self::INTERVAL),
            ])
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $this->ss->note('Going to check database health');

        $retriesLeft = $input->getOption('retries');
        $interval = $input->getOption('interval');

        $databases = false;
        do {
            try {
                $databases = $this->em->getConnection()->getSchemaManager()->listDatabases();
                if ($databases) {
                    break;
                }
            } catch (Exception $e) {
                $this->ss->warning("Database still not ready. Waiting for {$interval}s. Retries left: {$retriesLeft}");
                sleep($interval);
            }
        } while (--$retriesLeft);

        if (!$databases) {
            $this->ss->error("Database isn't ready. Please try to run database container manually and figire out what's going wrong");
            return -1;
        }

        $this->ss->success('Database is OK. '.count($databases).' databases are found');
        return 0;
    }
}
