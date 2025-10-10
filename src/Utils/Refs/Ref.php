<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils\Refs;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-template T of \AdamAveray\Typeform\Models\Model
 * @psalm-immutable
 */
abstract readonly class Ref
{
  /**
   * @var class-string<T>
   */
  private string $className;
  public string $href;

  /**
   * @param class-string<T> $className
   * @psalm-param class-string $className See https://github.com/vimeo/psalm/issues/7913
   * @param array{ href: string } $data
   */
  public function __construct(string $className, array $data)
  {
    /** @psalm-var class-string<T> $className */
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
