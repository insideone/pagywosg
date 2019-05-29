<?php

namespace App\Command;

use App\Entity\ArchivedChange;
use App\Entity\Change;
use App\Framework\Command\BaseCommand;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveChangelogCommand extends BaseCommand
{
    const DEFAULT_QUOTA = 30000;

    protected function configure()
    {
        $this->setName('changelog:archive')
            ->getDefinition()
            ->addOptions([
                new InputOption('quota', null, InputOption::VALUE_OPTIONAL, '', self::DEFAULT_QUOTA)
            ]);
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $quota = $input->getOption('quota');

        $qb = $this->em->createQueryBuilder();

        /** @noinspection PhpUnhandledExceptionInspection */
        $count = $qb
            ->from(Change::class, 'change')
            ->select($qb->expr()->count('change.id'))
            ->setMaxResults(1)
            ->getQuery()->getSingleScalarResult();

        if ($quota >= $count) {
            $this->ss->note('Nothing to archive according used quota');
            return;
        }

        $toArchiveCnt = $count - $quota;

        $this->ss->note("Going to move some entries to archive: {$toArchiveCnt}");

        $this->ss->progressStart($toArchiveCnt);

        /** @var Change[][] $changes */
        $changes = $this->em->createQueryBuilder()
            ->from(Change::class, 'change')
            ->select('change')
            ->setMaxResults($toArchiveCnt)
            ->orderBy('change.id', Criteria::ASC)
            ->getQuery()->iterate();

        foreach ($changes as $change) {
            $change = $change[0];

            $this->em->persist(
                (new ArchivedChange)
                    ->setId($change->getId())
                    ->setCreatedAt($change->getCreatedAt())
                    ->setActor($change->getActor())
                    ->setObjectType($change->getObjectType())
                    ->setObjectId($change->getObjectId())
                    ->setProperty($change->getProperty())
                    ->setOld($change->getOld())
                    ->setNew($change->getNew())
            );
            $this->em->remove($change);
            $this->ss->progressAdvance();
        }

        $this->ss->progressFinish();

        $this->ss->note('Flushing all to database');

        $this->em->flush();
    }
}
