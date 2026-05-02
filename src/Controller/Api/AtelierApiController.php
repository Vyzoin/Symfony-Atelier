<?php

namespace App\Controller\Api;

use App\Entity\Atelier;
use App\Entity\Inscription;
use App\Repository\SessionAtelierRepository;
use App\Service\AtelierCatalogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AtelierApiController extends AbstractController
{
    #[Route('/api/ateliers', name: 'api_ateliers_index', methods: ['GET'])]
    public function index(AtelierCatalogService $catalog): JsonResponse
    {
        $ateliers = array_map(static fn ($atelier): array => [
            'id' => $atelier->getId(),
            'title' => $atelier->getTitle(),
            'description' => $atelier->getDescription(),
            'dureeMinutes' => $atelier->getDureeMinutes(),
            'capacite' => $atelier->getCapacite(),
            'status' => $atelier->getStatus(),
        ], $catalog->listPublishedAteliers());

        return $this->json([
            'items' => $ateliers,
            'count' => count($ateliers),
        ]);
    }

    #[Route('/api/ateliers/{id}/detail', name: 'api_ateliers_detail', methods: ['GET'])]
    public function detail(Atelier $atelier): JsonResponse
    {
        return $this->json([
            'id' => $atelier->getId(),
            'title' => $atelier->getTitle(),
            'description' => $atelier->getDescription(),
            'theme' => $atelier->getTheme()?->getName(),
            'intervenant' => $atelier->getIntervenant()?->getFullName(),
            'sessions' => count($atelier->getSessions()),
        ]);
    }

    #[Route('/api/ateliers/{id}/sessions', name: 'api_ateliers_sessions', methods: ['GET'])]
    public function sessions(Atelier $atelier, Request $request, SessionAtelierRepository $repository): JsonResponse
    {
        $period = $request->query->get('period', 'upcoming');
        $now = new \DateTimeImmutable();

        $qb = $repository->createQueryBuilder('s')
            ->andWhere('s.atelier = :atelier')
            ->setParameter('atelier', $atelier)
            ->orderBy('s.date', 'ASC');

        if ('past' === $period) {
            $qb->andWhere('s.date < :now');
        } else {
            $qb->andWhere('s.date >= :now');
        }
        $qb->setParameter('now', $now);

        $items = array_map(static fn ($session) => [
            'id' => $session->getId(),
            'date' => $session->getDate()?->format(\DateTimeInterface::ATOM),
            'capacite' => $session->getCapacite(),
        ], $qb->getQuery()->getResult());

        return $this->json([
            'atelierId' => $atelier->getId(),
            'period' => $period,
            'items' => $items,
        ]);
    }

    #[Route('/api/inscriptions', name: 'api_inscriptions_create', methods: ['POST'])]
    public function createInscription(
        Request $request,
        SessionAtelierRepository $sessionAtelierRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        $session = isset($payload['sessionId']) ? $sessionAtelierRepository->find((int) $payload['sessionId']) : null;
        if (null === $session) {
            return $this->json(['error' => 'Session not found.'], 404);
        }

        $inscription = (new Inscription())
            ->setNom((string) ($payload['nom'] ?? ''))
            ->setPrenom((string) ($payload['prenom'] ?? ''))
            ->setEmail((string) ($payload['email'] ?? ''))
            ->setSession($session);

        $errors = $validator->validate($inscription);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 422);
        }

        $entityManager->persist($inscription);
        $entityManager->flush();

        return $this->json([
            'id' => $inscription->getId(),
            'status' => 'created',
        ], 201);
    }
}
