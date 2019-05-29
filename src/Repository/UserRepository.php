<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class UserRepository extends EntityRepository
{
    /**
     * @param string $profileName
     * @return User|null
     */
    public function findByProfileName(string $profileName)
    {
        /** @var User $user */
        $user = $this->findOneBy(['profileName' => $profileName]);
        return $user;
    }

    /**
     * @param string $identity
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findByIdentity(string $identity)
    {
        $user = $this->createQueryBuilder('user')
            ->where('user.profileName = :identity')
            ->orWhere('user.steamId = :identity')
            ->setParameter('identity', $identity)
            ->getQuery()->getOneOrNullResult();

        return $user;
    }
}
