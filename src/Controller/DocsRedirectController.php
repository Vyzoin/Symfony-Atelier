<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class DocsRedirectController extends AbstractController
{
    #[Route('/docs', name: 'app_docs_redirect', methods: ['GET'])]
    public function __invoke(): RedirectResponse
    {
        return $this->redirect('/api/docs');
    }
}
