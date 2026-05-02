<?php

namespace App\Service;

use App\Entity\Atelier;
use App\Repository\AtelierRepository;

class AtelierCatalogService
{
    public function __construct(
        private readonly AtelierRepository $atelierRepository,
    ) {
    }

    /**
     * @return list<Atelier>
     */
    public function listPublishedAteliers(): array
    {
        return $this->atelierRepository->findPublishedOrdered();
    }
}
