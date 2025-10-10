<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils\Refs;

use AdamAveray\Typeform\Models\Model;

/**
 * @template T2 of \AdamAveray\Typeform\Models\Model
 * @extends Ref<T2>
 * @psalm-immutable
 */
final readonly class CollectionRef extends Ref
{
  public int $count;

  /**
   * @param class-string $className See https://github.com/vimeo/psalm/issues/7913
   * @param array{ count: int, href: string } $data
   */
  public function __construct(string $className, array $data)
  {
    /*** @var class-string<T2> $className */
    $this->count = $data['count'];
    unset($data['count']);

    parent::__construct($className, $data);
  }

  /**
   * @return list<T2>
   * @mutation-free
   */
  public function instantiateCollection(array $data): array
  {
    /** @psalm-suppress ImpureFunctionCall */
    return array_map(fn(array $item): Model => $this->instantiateOne($item), array_values($data));
  }
}
