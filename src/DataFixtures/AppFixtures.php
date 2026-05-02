<?php

namespace App\DataFixtures;

use App\Entity\Atelier;
use App\Entity\Intervenant;
use App\Entity\SessionAtelier;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new User())->setEmail('admin')->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));

        $user = (new User())->setEmail('participant')->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'participant'));

        $theme1 = (new Theme())->setName('Ecologie');
        $theme2 = (new Theme())->setName('Numerique responsable');

        $intervenant1 = (new Intervenant())->setFullName('Alice Martin');
        $intervenant2 = (new Intervenant())->setFullName('Bilal Haddad');

        $atelier1 = (new Atelier())
            ->setTitle('Compostage en ville')
            ->setDescription('Atelier pratique autour du compost domestique.')
            ->setDureeMinutes(90)
            ->setCapacite(18)
            ->setStatus('published')
            ->setTheme($theme1)
            ->setIntervenant($intervenant1)
            ->setOwner($admin);

        $atelier2 = (new Atelier())
            ->setTitle('Protection des donnees')
            ->setDescription('Bonnes pratiques pour proteger ses donnees personnelles.')
            ->setDureeMinutes(60)
            ->setCapacite(20)
            ->setStatus('published')
            ->setTheme($theme2)
            ->setIntervenant($intervenant2)
            ->setOwner($admin);

        $atelier3 = (new Atelier())
            ->setTitle('Repair cafe quartier')
            ->setDescription('Reparer des objets du quotidien en groupe.')
            ->setDureeMinutes(120)
            ->setCapacite(12)
            ->setStatus('draft')
            ->setTheme($theme1)
            ->setIntervenant($intervenant1)
            ->setOwner($admin);

        $pastSession = (new SessionAtelier())
            ->setAtelier($atelier1)
            ->setDate(new \DateTimeImmutable('-10 days'))
            ->setCapacite(12);

        $futureSession = (new SessionAtelier())
            ->setAtelier($atelier1)
            ->setDate(new \DateTimeImmutable('+10 days'))
            ->setCapacite(14);

        foreach ([
            $admin, $user, $theme1, $theme2, $intervenant1, $intervenant2,
            $atelier1, $atelier2, $atelier3, $pastSession, $futureSession,
        ] as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
