<?php

namespace App\Framework\Command;

use App\Service\Changelog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseCommand extends Command
{
    use LockableTrait;

    /** @var Changelog */
    protected $changelog;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var SymfonyStyle */
    protected $ss;

    abstract protected function proceed(InputInterface $input, OutputInterface $output);

    /**
     * Will command be locked after run until done?
     * All commands are locked after run by default
     * @return bool
     */
    protected function isLockedAfterRun()
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->isLockedAfterRun() && !$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return -1;
        }

        $this->ss = new SymfonyStyle($input, $output);

        $returnCode = $this->prepare($input, $output);

        if (false !== $returnCode) {
            $returnCode = $this->proceed($input, $output);
        }

        $this->finalize($input, $output);
        return $returnCode;
    }

    protected function prepare(InputInterface $input, OutputInterface $output) : ?bool
    {
        return null;
    }

    protected function finalize(InputInterface $input, OutputInterface $output)
    {
        $this->changelog->save();
    }

    public function setChangelog(Changelog $changelog)
    {
        $this->changelog = $changelog;
    }

    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
        return $this;
    }
}
