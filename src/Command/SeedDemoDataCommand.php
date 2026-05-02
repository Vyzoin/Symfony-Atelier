<?php

namespace App\Command;

use App\Entity\Atelier;
use App\Entity\Intervenant;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-demo-data',
    description: 'Insere un jeu de donnees de demonstration.',
)]
class SeedDemoDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $admin = (new User())
            ->setEmail('admin@example.test')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));

        $user = (new User())
            ->setEmail('user@example.test')
            ->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'userpass'));

        $theme = (new Theme())->setName('Ecologie urbaine');
        $intervenant = (new Intervenant())->setFullName('Camille Durand');

        $atelier = (new Atelier())
            ->setTitle('Jardinage en ville')
            ->setDescription('Atelier pratique pour creer un micro-jardin en milieu urbain.')
            ->setDureeMinutes(90)
            ->setCapacite(20)
            ->setStatus('published')
            ->setTheme($theme)
            ->setIntervenant($intervenant)
            ->setOwner($admin);

        foreach ([$admin, $user, $theme, $intervenant, $atelier] as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $io->success('Donnees de demo inserees.');
        $io->writeln('Admin: admin@example.test / adminpass');
        $io->writeln('User: user@example.test / userpass');

        return Command::SUCCESS;
    }
}
