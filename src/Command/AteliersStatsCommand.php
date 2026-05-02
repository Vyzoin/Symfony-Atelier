<?php

namespace App\Command;

use App\Repository\AtelierRepository;
use App\Repository\InscriptionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ateliers:stats',
    description: 'Genere un rapport de statistiques ateliers.',
)]
class AteliersStatsCommand extends Command
{
    public function __construct(
        private readonly AtelierRepository $atelierRepository,
        private readonly InscriptionRepository $inscriptionRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'table|json', 'table')
            ->addOption('export', null, InputOption::VALUE_REQUIRED, 'Chemin d export');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $ateliers = $this->atelierRepository->findAll();
        $totalAteliers = count($ateliers);
        $byTheme = [];
        $inscriptionsByAtelier = [];
        $intervenantCount = [];

        foreach ($ateliers as $atelier) {
            $themeName = $atelier->getTheme()?->getName() ?? 'Sans theme';
            $byTheme[$themeName] = ($byTheme[$themeName] ?? 0) + 1;

            $count = 0;
            foreach ($atelier->getSessions() as $session) {
                $sessionCount = count($session->getInscriptions());
                $count += $sessionCount;
                $name = $atelier->getIntervenant()?->getFullName() ?? 'Sans intervenant';
                $intervenantCount[$name] = ($intervenantCount[$name] ?? 0) + $sessionCount;
            }
            $inscriptionsByAtelier[$atelier->getTitle()] = $count;
        }

        arsort($intervenantCount);
        $mostSolicited = array_key_first($intervenantCount) ?? 'Aucun';

        $report = [
            'totalAteliers' => $totalAteliers,
            'parTheme' => $byTheme,
            'inscriptionsParAtelier' => $inscriptionsByAtelier,
            'intervenantPlusSollicite' => $mostSolicited,
            'totalInscriptions' => $this->inscriptionRepository->count([]),
        ];

        if ('json' === $input->getOption('format')) {
            $content = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $io->writeln($content ?: '{}');
        } else {
            $io->title('Rapport ateliers');
            $io->definitionList(
                ['Total ateliers' => (string) $report['totalAteliers']],
                ['Total inscriptions' => (string) $report['totalInscriptions']],
                ['Intervenant le plus sollicite' => $report['intervenantPlusSollicite']]
            );
        }

        $exportPath = $input->getOption('export');
        if (is_string($exportPath) && '' !== $exportPath) {
            file_put_contents($exportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $io->success(sprintf('Rapport exporte vers %s', $exportPath));
        }

        return Command::SUCCESS;
    }
}
