<?php
declare(strict_types = 1);

namespace App\Domain\Stats;

interface StatsRepositoryInterface {
  public function create(
    string $packageName,
    int $githubStars = 0,
    int $githubWatchers = 0,
    int $githubForks = 0,
    int $dependents = 0,
    int $suggesters = 0,
    int $favers = 0,
    int $totalDownloads = 0,
    int $monthlyDownloads = 0,
    int $dailyDownloads = 0
  ): Stats;

  public function all(): StatsCollection;

  public function exists(string $packageName): bool;

  /**
   * @throws \App\Domain\Stats\StatsNotFoundException
   */
  public function get(string $packageName): Stats;

  public function find(array $query): StatsCollection;

  public function save(Stats $stats): Stats;

  public function update(Stats $stats): Stats;
}
