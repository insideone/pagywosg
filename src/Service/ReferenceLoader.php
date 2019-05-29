<?php

namespace App\Service;

use App\Entity\User;
use App\Framework\Exceptions\ReferenceLoadException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Knojector\SteamAuthenticationBundle\Security\User\SteamUserProvider;
use ReflectionMethod;
use ReflectionObject;

class ReferenceLoader
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var SteamUserProvider */
    protected $steamUserProvider;

    public function __construct(EntityManagerInterface $em, SteamUserProvider $steamUserProvider)
    {
        $this->em = $em;
        $this->steamUserProvider = $steamUserProvider;
    }

    /**
     * @param object $object
     * @return object
     * @throws ORMException
     * @throws ReferenceLoadException
     */
    public function serve(object $object)
    {
        $reflection = new ReflectionObject($object);
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

            if (!preg_match('~^set(\S+)$~', $method->name, $m)
            ) {
                continue;
            }

            $type = $method->getParameters()[0]->getType();
            if (!$type) {
                continue;
            }

            $inputType = $type->getName();

            if (strpos($inputType, 'App\\Entity') !== 0) {
                continue;
            }

            list (, $propName) = $m;
            $entity = $object->{"get{$propName}"}();

            if (!$entity || $this->em->contains($entity)) {
                continue;
            }

            if ($entity && get_class($entity) === $inputType && method_exists($entity, 'getId')) {
                $dbEntity = null;
                if ($entity->getId()) {
                    $dbEntity = $this->em->getReference($inputType, $entity->getId());
                } elseif ($entity instanceof User && $entity->getSteamId()) {
                    $dbEntity = $this->steamUserProvider->loadUserByUsername($entity->getSteamId());
                }

                if (!$dbEntity) {
                    throw new ReferenceLoadException("Can't load {$inputType}, because id isn't importable");
                }

                $object->{$method->name}($dbEntity);
            }
        }

        return $object;
    }
}
