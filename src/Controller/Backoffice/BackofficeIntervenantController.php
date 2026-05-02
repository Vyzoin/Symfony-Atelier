<?php

namespace App\Controller\Backoffice;

use App\Entity\Intervenant;
use App\Form\IntervenantType;
use App\Repository\IntervenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/intervenants')]
class BackofficeIntervenantController extends AbstractController
{
    #[Route('', name: 'backoffice_intervenants_index', methods: ['GET'])]
    public function index(IntervenantRepository $repository): Response
    {
        return $this->render('backoffice/intervenant/index.html.twig', ['intervenants' => $repository->findAll()]);
    }

    #[Route('/new', name: 'backoffice_intervenants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $intervenant = new Intervenant();
        $form = $this->createForm(IntervenantType::class, $intervenant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($intervenant);
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_intervenants_index');
        }

        return $this->render('backoffice/intervenant/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'backoffice_intervenants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Intervenant $intervenant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IntervenantType::class, $intervenant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_intervenants_index');
        }

        return $this->render('backoffice/intervenant/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/delete', name: 'backoffice_intervenants_delete', methods: ['POST'])]
    public function delete(Request $request, Intervenant $intervenant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_intervenant_'.$intervenant->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($intervenant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_intervenants_index');
    }
}
