<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Inscription;
use App\Entity\SessionAtelier;
use App\Form\InscriptionType;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontAtelierController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[Route('/ateliers', name: 'front_ateliers_index', methods: ['GET'])]
    public function index(AtelierRepository $atelierRepository): Response
    {
        return $this->render('front/atelier/index.html.twig', [
            'ateliers' => $atelierRepository->findPublishedOrdered(),
        ]);
    }

    #[Route('/ateliers/{id}', name: 'front_ateliers_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {
        return $this->render('front/atelier/show.html.twig', [
            'atelier' => $atelier,
        ]);
    }

    #[Route('/sessions/{id}/inscription', name: 'front_inscription_new', methods: ['GET', 'POST'])]
    public function inscription(Request $request, SessionAtelier $session, EntityManagerInterface $entityManager): Response
    {
        $inscription = (new Inscription())->setSession($session);
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription enregistree.');

            return $this->redirectToRoute('front_ateliers_show', ['id' => $session->getAtelier()?->getId()]);
        }

        return $this->render('front/inscription/form.html.twig', [
            'form' => $form->createView(),
            'session' => $session,
        ]);
    }
}
