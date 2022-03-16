<?php
declare(strict_types = 1);

namespace App\Application\Processor\Listener\Package;

use App\Application\Message\Command\PackageDiscoveryCommand;
use Courier\Client\Producer;
use Courier\Message\EventInterface;
use Courier\Processor\Listener\InvokeListenerInterface;
use Psr\Log\LoggerInterface;


class PackageCreatedListener implements InvokeListenerInterface {
  private Producer $producer;
  private LoggerInterface $logger;

  public function __construct(Producer $producer, LoggerInterface $logger) {
    $this->producer = $producer;
    $this->logger   = $logger;
  }

  public function __invoke(EventInterface $event): void {
    $package = $event->getPackage();
    // $this->logger->debug('Package created', [$package]);

    $this->producer->sendCommand(
      new PackageDiscoveryCommand($package)
    );
  }
}
