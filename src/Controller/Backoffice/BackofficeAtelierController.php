<?php

namespace App\Controller\Backoffice;

use App\Entity\Atelier;
use App\Entity\User;
use App\Event\AtelierCreatedEvent;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/ateliers')]
class BackofficeAtelierController extends AbstractController
{
    #[Route('', name: 'backoffice_ateliers_index', methods: ['GET'])]
    public function index(AtelierRepository $atelierRepository): Response
    {
        return $this->render('backoffice/atelier/index.html.twig', [
            'ateliers' => $atelierRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'backoffice_ateliers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        $atelier = new Atelier();
        $user = $this->getUser();
        if ($user instanceof User) {
            $atelier->setOwner($user);
        }
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($atelier);
            $entityManager->flush();
            $dispatcher->dispatch(new AtelierCreatedEvent($atelier), AtelierCreatedEvent::NAME);

            return $this->redirectToRoute('backoffice_ateliers_index');
        }

        return $this->render('backoffice/atelier/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'backoffice_ateliers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_ateliers_index');
        }

        return $this->render('backoffice/atelier/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/delete', name: 'backoffice_ateliers_delete', methods: ['POST'])]
    public function delete(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_atelier_'.$atelier->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($atelier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_ateliers_index');
    }
}
