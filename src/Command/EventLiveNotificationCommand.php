<?php

namespace App\Command;

use App\Event\Command\EventLiveBeginNotificationCommand;
use App\Repository\Event\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:national-events:live-notifications',
    description: 'This command finds upcoming live events and send notifications',
)]
class EventLiveNotificationCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $events = $this->eventRepository->findWithLiveToNotify();

        foreach ($events as $event) {
            $this->bus->dispatch(new EventLiveBeginNotificationCommand($event->getUuid()));
            $event->pushSentAt = new \DateTime();

            $this->entityManager->flush();
        }

        return self::SUCCESS;
    }
}
