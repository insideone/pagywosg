<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Framework\Command\BaseCommand;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddUserRoleCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('user:role:add')
            ->getDefinition()
            ->addArguments([
                new InputArgument('userIdentity', InputArgument::REQUIRED),
                new InputArgument('role', InputArgument::REQUIRED)
            ])
        ;
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $ss = new SymfonyStyle($input, $output);

        ['userIdentity' => $userIdentity, 'role' => $role] = $input->getArguments();

        $role = strtoupper($role);
        if (strpos($role, 'ROLE_') === false) {
            $role = "ROLE_{$role}";
        }

        $knownRoles = array_map(
            function (Role $role) {
                return $role->getName();
            },
            $this->em->getRepository(Role::class)->findBy([], ['id' => Criteria::ASC])
        );

        if (!in_array($role, $knownRoles)) {
            $ss->error(array_merge(["Role with name `{$role}` doesn't exist. Known roles: "], $knownRoles));
            return;
        }

        $user = $this->em->getRepository(User::class)->findByIdentity($userIdentity);

        if (!$user) {
            $ss->error("We don't know anything about `{$userIdentity}`");
            return;
        }

        $ss->note("Going to add {$role} role to {$userIdentity}");

        try {
            if (!$this->addRoleToUser($role, $user)) {
                $ss->note('But user already had this role');
            }
        } catch (Exception $e) {
            $ss->error($e->getMessage());
            return;
        }

        $ss->success('Done!');
    }

    protected function addRoleToUser(string $role, User $user)
    {
        if ($user->hasRole($role)) {
            return false;
        }

        $user->addRole($role);
        $this->em->persist($user);
        $this->em->flush();
        return true;
    }
}
