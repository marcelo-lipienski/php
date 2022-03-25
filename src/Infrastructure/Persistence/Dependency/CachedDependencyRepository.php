<?php
declare(strict_types = 1);

namespace App\Infrastructure\Persistence\Dependency;

use App\Domain\Dependency\Dependency;
use App\Domain\Dependency\DependencyCollection;
use App\Domain\Dependency\DependencyRepositoryInterface;
use App\Domain\Dependency\DependencyStatusEnum;
use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;

final class CachedDependencyRepository implements DependencyRepositoryInterface {
  private DependencyRepositoryInterface $dependencyRepository;
  private CacheItemPoolInterface $cacheItemPool;

  public function __construct(
    DependencyRepositoryInterface $dependencyRepository,
    CacheItemPoolInterface $cacheItemPool
  ) {
    $this->dependencyRepository = $dependencyRepository;
    $this->cacheItemPool        = $cacheItemPool;
  }

  public function create(
    int $versionId,
    string $name,
    string $constraint,
    bool $development = false,
    DependencyStatusEnum $status = DependencyStatusEnum::Unknown,
    DateTimeImmutable $createdAt = new DateTimeImmutable()
  ): Dependency {
    return $this->dependencyRepository->create(
      $versionId,
      $name,
      $constraint,
      $development,
      $status,
      $createdAt
    );
  }

  public function all(): DependencyCollection {
    $item = $this->cacheItemPool->getItem('/dependency');
    $dependencyCol = $item->get();
    if ($item->isHit() === false) {
      $dependencyCol = $this->dependencyRepository->all();

      $item->set($dependencyCol);
      $item->expiresAfter(3600);

      $this->cacheItemPool->save($item);
    }

    return $dependencyCol;
  }

  /**
   * @throws \App\Domain\Dependency\DependencyNotFoundException
   */
  public function get(int $id): Dependency {
    $item = $this->cacheItemPool->getItem("/dependency/${id}");
    $dependency = $item->get();
    if ($item->isHit() === false) {
      $dependency = $this->dependencyRepository->get($id);

      $item->set($dependency);
      $item->expiresAfter(3600);

      $this->cacheItemPool->save($item);
    }

    return $dependency;
  }

  public function find(array $query): DependencyCollection {
    $key = http_build_query($query);
    $item = $this->cacheItemPool->getItem("/dependency/find/{$key}");
    $dependencyCol = $item->get();
    if ($item->isHit() === false) {
      $dependencyCol = $this->dependencyRepository->find($query);

      $item->set($dependencyCol);
      $item->expiresAfter(3600);

      $this->cacheItemPool->save($item);
    }

    return $dependencyCol;
  }

  public function save(Dependency $dependency): Dependency {
    $dependency = $this->dependencyRepository->save($dependency);

    $this->cacheItemPool->deleteItem('/dependency/' . $dependency->getId());

    return $dependency;
  }

  public function update(Dependency $dependency): Dependency {
    $dependency = $this->dependencyRepository->update($dependency);

    $this->cacheItemPool->deleteItem('/dependency/' . $dependency->getId());

    return $dependency;
  }
}