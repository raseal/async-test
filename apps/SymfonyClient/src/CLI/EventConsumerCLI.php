<?php

declare(strict_types=1);

namespace SymfonyClient\CLI;

use Shared\Infrastructure\Symfony\Bus\Event\DomainEventSubscriberLocator;
use Shared\Infrastructure\Symfony\Bus\Event\RabbitMQ\RabbitMQDomainEventConsumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class EventConsumerCLI extends Command
{
    protected static $defaultName = 'event:consume';

    public function __construct(
        private RabbitMQDomainEventConsumer $consumer,
        private DomainEventSubscriberLocator $locator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Consume domain events')
            ->addArgument('queue', InputArgument::REQUIRED, 'Queue name')
            ->addArgument('chunk', InputArgument::REQUIRED, 'Quantity of events to process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queueName = $input->getArgument('queue');
        $chunk = (int)$input->getArgument('chunk');

        $output->writeln("me lega $queueName hasta $chunk");

        for($i=0; $i<$chunk; $i++) {
            echo "****** ITERATION $i of $chunk \n";
            $this->consumer->consume(
                $this->locator->withRabbitMqQueueNamed($queueName),
                $queueName
            );
        }

        return self::SUCCESS;
    }
}
