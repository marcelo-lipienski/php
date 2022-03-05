<?php
declare(strict_types = 1);

namespace App\Domain\Stats;

use App\Domain\Exception\DomainRecordNotFoundException;

class StatsNotFoundException extends DomainRecordNotFoundException {
  protected $message = 'Stats not found';
}