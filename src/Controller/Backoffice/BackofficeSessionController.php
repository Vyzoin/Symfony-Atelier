<?php

namespace App\Controller\Backoffice;

use App\Entity\SessionAtelier;
use App\Form\SessionAtelierType;
use App\Repository\SessionAtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/sessions')]
class BackofficeSessionController extends AbstractController
{
    #[Route('', name: 'backoffice_sessions_index', methods: ['GET'])]
    public function index(SessionAtelierRepository $repository): Response
    {
        return $this->render('backoffice/session/index.html.twig', ['sessions' => $repository->findBy([], ['date' => 'ASC'])]);
    }

    #[Route('/new', name: 'backoffice_sessions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new SessionAtelier();
        $form = $this->createForm(SessionAtelierType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_sessions_index');
        }

        return $this->render('backoffice/session/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'backoffice_sessions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SessionAtelier $session, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionAtelierType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_sessions_index');
        }

        return $this->render('backoffice/session/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/delete', name: 'backoffice_sessions_delete', methods: ['POST'])]
    public function delete(Request $request, SessionAtelier $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_session_'.$session->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_sessions_index');
    }
}
