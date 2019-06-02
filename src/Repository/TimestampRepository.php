<?php

namespace App\Repository;

use App\Entity\Timestamp;
use App\Enum\TimestampEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class TimestampRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Timestamp::class));
    }

    public function getLastGameListUpdate()
    {
        return $this->findOneByStamp(TimestampEnum::LAST_GAME_LIST_UPDATE);
    }

    /**
     * @param string $stamp
     * @return Timestamp
     */
    public function findOneByStamp(string $stamp) : ?Timestamp
    {
        /** @var Timestamp $timestamp */
        $timestamp = $this->findOneBy([
            'stamp' => $stamp,
        ]);

        return $timestamp;
    }
}
