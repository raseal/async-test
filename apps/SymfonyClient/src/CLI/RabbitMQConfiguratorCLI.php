<?php

declare(strict_types=1);

namespace SymfonyClient\CLI;

use Shared\Infrastructure\Symfony\Bus\Event\RabbitMQ\RabbitMQConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Traversable;

final class RabbitMQConfiguratorCLI extends Command
{
    protected static $defaultName = 'rabbitmq:configure';

    public function __construct(
        private RabbitMQConfigurator $configurator,
        private string $exchange_name,
        private Traversable $subscribers
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Configure the RabbitMQ for publishing & consuming domain events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configurator->configure($this->exchange_name, ...iterator_to_array($this->subscribers));

        return self::SUCCESS;
    }
}
