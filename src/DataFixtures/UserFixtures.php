<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const NORMAL_USER_REFERENCE_PREFIX = 'normal-user-';

    public const NUMBER_OF_NORMAL_USERS = 5;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }


    public function load(ObjectManager $manager): void
    {
        $this->createAdmin($manager);
        $this->createNormalUsers($manager);

        $manager->flush();
    }

    public function createAdmin(ObjectManager $manager): void
    {
        $admin = new User();
        $admin
            ->setUsername('admin')
            ->setEmail('admin@domain.com')
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $admin,
                    'admin'
                )
            )
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($admin);
        $this->setReference(self::ADMIN_USER_REFERENCE, $admin);
    }

    public function createNormalUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::NUMBER_OF_NORMAL_USERS; $i++) {
            $user = new User();
            $user
                ->setUsername('user' . $i)
                ->setEmail('user' . $i . '@domain.com')
                ->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $user,
                        'user' . $i
                    )
                )
            ;

            $manager->persist($user);
            $this->addReference(self::NORMAL_USER_REFERENCE_PREFIX . $i, $user);
        }
    }
}