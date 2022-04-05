<?php
declare(strict_types = 1);

namespace App\Application\Processor\Listener\Dependency;

use App\Application\Message\Command\UpdateVersionStatusCommand;
use Courier\Client\Producer\ProducerInterface;
use Courier\Message\EventInterface;
use Courier\Processor\Listener\InvokeListenerInterface;
use Psr\Log\LoggerInterface;

class DependencyUpdatedListener implements InvokeListenerInterface {
  private ProducerInterface $producer;
  private LoggerInterface $logger;

  public function __construct(ProducerInterface $producer, LoggerInterface $logger) {
    $this->producer = $producer;
    $this->logger   = $logger;
  }

  /**
   * update the version status that requires $dependency
   */
  public function __invoke(EventInterface $event, array $attributes = []): void {
    $dependency = $event->getDependency();
    $this->logger->debug(
      'Dependency updated',
      ['dependency' => $dependency->getName()]
    );

    $this->producer->sendCommand(
      new UpdateVersionStatusCommand($dependency)
    );
  }
}
