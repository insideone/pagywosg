<?php

namespace App\Service;

use App\Entity\User;
use App\Framework\Entity\IdentityProvider;
use App\Security\PermissionsCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PermissionTeller
{
    /** @var AuthorizationCheckerInterface */
    protected $checker;

    /** @var PermissionsCollection */
    protected $setPermissionsCollection;

    /** @var User */
    protected $user;

    /** @var ClassShortNameProvider */
    protected $shortNameProvider;

    public function __construct(AuthorizationCheckerInterface $checker, PermissionsCollection $permissionsCollection, TokenStorageInterface $tokenStorage,
        ClassShortNameProvider $shortNameProvider)
    {
        $this->checker = $checker;
        $this->setPermissionsCollection = $permissionsCollection;
        $this->shortNameProvider = $shortNameProvider;

        $token = $tokenStorage->getToken();
        if ($token && ($user = $token->getUser()) instanceof User) {
            $this->user = $user;
        }
    }

    public function isGranted($operation, $subject = null, $checkSubOperations = true): bool
    {
        // admin's bypass
        if ($this->user && $this->user->isAdmin()) {
            return true;
        }

        if ($checkSubOperations) {
            $permissionHolder = $this->setPermissionsCollection->findFor($subject);
            if ($permissionHolder) {
                $allOperations = $permissionHolder::getOperations();
                if (in_array($operation, $allOperations) && preg_match('~^(.+)_(own|hosted|any)$~', $operation, $m)) {
                    [, $basicOperation, $type] = $m;
                    $operationsToCheck = [];

                    switch ($type) {
                        /** @noinspection PhpMissingBreakStatementInspection */
                        case 'own':
                            $operationsToCheck["{$basicOperation}_own"] = !is_object($subject) || $permissionHolder::getOwner($subject) === $this->user;
                        /** @noinspection PhpMissingBreakStatementInspection */
                        case 'hosted':
                            $operationsToCheck["{$basicOperation}_hosted"] = !is_object($subject) || $permissionHolder::getHost($subject) === $this->user;
                        case 'any':
                            $operationsToCheck["{$basicOperation}_any"] = true;
                    }

                    foreach (array_keys(array_filter($operationsToCheck)) as $operationToCheck) {
                        if ($this->isGranted($operationToCheck, $subject, false)) {
                            return true;
                        }
                    }
                    return false;
                }
            }
        }

        // It's much more comfortable to check `$subject`s as objects, but fxp_security bundle wait from us object with
        // filled id property. In other case it fall down. So new objects aren't accessible
        $subject = is_object($subject) ? get_class($subject) : $subject;

        return $this->checker->isGranted("perm_{$operation}", $subject);
    }

    public function isGrantedMultiple($operations, $subject = null)
    {
        $permissions = [];

        $subjectName = $this->shortNameProvider->get($subject);

        foreach ($operations as $operation => $shortName) {
            if (is_numeric($operation)) {
                $operation = $shortName;
            }

            $permissionName = [$shortName];

            if ($subject) {
                $permissionName[] = lcfirst($subjectName);
            }

            if ($subject instanceof IdentityProvider) {
                $permissionName[] = "#{$subject->getId()}";
            }

            $permissionName = implode(':', $permissionName);

            $permissions[$permissionName] = $this->isGranted($operation, $subject);
        }

        return $permissions;
    }
}
