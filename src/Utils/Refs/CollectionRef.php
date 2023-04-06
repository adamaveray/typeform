<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils\Refs;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-template T2 of \AdamAveray\Typeform\Models\Model
 * @extends Ref<T2>
 */
final class CollectionRef extends Ref
{
  /** @readonly */
  public int $count;

  /**
   * @psalm-param class-string<T2> $className
   * @param array{ count: int, href: string } $data
   */
  public function __construct(string $className, array $data)
  {
    $this->count = $data['count'];
    unset($data['count']);

    parent::__construct($className, $data);
  }

  /**
   * @return list<Model>
   * @psalm-return list<T2>
   * @psalm-mutation-free
   */
  public function instantiateCollection(array $data): array
  {
    /** @psalm-suppress ImpureFunctionCall */
    return array_map(fn(array $item): Model => $this->instantiateOne($item), array_values($data));
  }
}
