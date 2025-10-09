<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils\Refs;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-template T of \AdamAveray\Typeform\Models\Model
 */
abstract class Ref
{
  /**
   * @psalm-var class-string<T>
   */
  private readonly string $className;
  public readonly string $href;

  /**
   * @psalm-param class-string<T> $className
   * @param array{ href: string } $data
   */
  public function __construct(string $className, array $data)
  {
    $this->className = $className;
    $this->href = $data['href'];
  }

  /**
   * @psalm-return T
   * @psalm-mutation-free
   */
  protected function instantiateOne(array $data): Model
  {
    $className = $this->className;
    return new $className($data);
  }
}
