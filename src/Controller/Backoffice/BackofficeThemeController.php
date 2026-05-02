<?php

namespace App\Controller\Backoffice;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/themes')]
class BackofficeThemeController extends AbstractController
{
    #[Route('', name: 'backoffice_themes_index', methods: ['GET'])]
    public function index(ThemeRepository $repository): Response
    {
        return $this->render('backoffice/theme/index.html.twig', ['themes' => $repository->findAll()]);
    }

    #[Route('/new', name: 'backoffice_themes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_themes_index');
        }

        return $this->render('backoffice/theme/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'backoffice_themes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_themes_index');
        }

        return $this->render('backoffice/theme/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/delete', name: 'backoffice_themes_delete', methods: ['POST'])]
    public function delete(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_theme_'.$theme->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($theme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_themes_index');
    }
}
