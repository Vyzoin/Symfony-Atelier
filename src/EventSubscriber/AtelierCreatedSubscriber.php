<?php

namespace App\EventSubscriber;

use App\Event\AtelierCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AtelierCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AtelierCreatedEvent::NAME => 'onAtelierCreated',
        ];
    }

    public function onAtelierCreated(AtelierCreatedEvent $event): void
    {
        $atelier = $event->getAtelier();

        $this->logger->info('atelier.created', [
            'atelier_id' => $atelier->getId(),
            'title' => $atelier->getTitle(),
            'capacity' => $atelier->getCapacite(),
        ]);
    }
}
