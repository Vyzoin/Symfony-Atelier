<?php

namespace App\Command;

use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ateliers:cleanup',
    description: 'Archive les ateliers sans session a venir.',
)]
class CleanupAteliersCommand extends Command
{
    public function __construct(
        private readonly AtelierRepository $atelierRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule sans modifier la base')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Date de reference YYYY-MM-DD')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Confirme l execution hors dry-run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dateOption = $input->getOption('date');
        $referenceDate = $dateOption ? \DateTimeImmutable::createFromFormat('Y-m-d', (string) $dateOption) : new \DateTimeImmutable();
        if (!$referenceDate instanceof \DateTimeImmutable) {
            $io->error('Format de date invalide. Utiliser YYYY-MM-DD.');

            return Command::INVALID;
        }

        $dryRun = (bool) $input->getOption('dry-run');
        $force = (bool) $input->getOption('force');
        if (!$dryRun && !$force) {
            $io->warning('Utiliser --dry-run pour simuler ou --force pour executer.');

            return Command::FAILURE;
        }

        $count = 0;
        $ateliers = $this->atelierRepository->findBy(['archived' => false]);
        foreach ($ateliers as $atelier) {
            $hasUpcomingSession = false;
            foreach ($atelier->getSessions() as $session) {
                if ($session->getDate() !== null && $session->getDate() >= $referenceDate) {
                    $hasUpcomingSession = true;
                    break;
                }
            }

            if (!$hasUpcomingSession) {
                ++$count;
                if (!$dryRun) {
                    $atelier->setArchived(true);
                }
            }
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->success(sprintf(
            '%d atelier(s) cible(s) (%s).',
            $count,
            $dryRun ? 'simulation' : 'execution'
        ));

        return Command::SUCCESS;
    }
}
