<?php

namespace App\Event;

use App\Entity\Atelier;

final class AtelierCreatedEvent
{
    public const NAME = 'atelier.created';

    public function __construct(
        private readonly Atelier $atelier,
    ) {
    }

    public function getAtelier(): Atelier
    {
        return $this->atelier;
    }
}
