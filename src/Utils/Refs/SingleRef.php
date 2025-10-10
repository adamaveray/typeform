<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils\Refs;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-template T2 of \AdamAveray\Typeform\Models\Model
 * @extends Ref<T2>
 * @psalm-immutable
 */
final readonly class SingleRef extends Ref
{
  /**
   * @psalm-return T2
   * @psalm-mutation-free
   */
  public function instantiate(array $data): Model
  {
    return $this->instantiateOne($data);
  }
}
