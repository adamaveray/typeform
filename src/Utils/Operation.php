<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

/**
 * @psalm-template TType of Operation::TYPE_*
 * @psalm-template TPath of string
 * @psalm-template TValue af mixed
 * @psalm-immutable
 */
final class Operation
{
  private const TYPE_ADD = 'add';
  private const TYPE_REMOVE = 'remove';
  private const TYPE_REPLACE = 'replace';

  /**
   * @psalm-var TType
   */
  private readonly string $type;
  /**
   * @psalm-var TPath
   */
  private readonly string $path;
  /**
   * @psalm-var TValue
   */
  private readonly mixed $value;

  /**
   * @psalm-param TType $type
   * @psalm-param TPath $path
   * @psalm-param TValue $value
   */
  private function __construct(string $type, string $path, mixed $value)
  {
    $this->type = $type;
    $this->path = $path;
    $this->value = $value;
  }

  /**
   * @return array{ op: TType, path: TPath, value: TValue }
   */
  public function formatForRequest(): array
  {
    return [
      'op' => $this->type,
      'path' => $this->path,
      'value' => $this->value,
    ];
  }

  /**
   * @psalm-template TPathIn of string
   * @psalm-template TValueIn
   * @psalm-param TPathIn $path
   * @psalm-param TValueIn $value
   * @psalm-return Operation<self::TYPE_ADD, TPathIn, TValueIn>
   * @psalm-pure
   */
  public static function add(string $path, mixed $value): self
  {
    return new self(self::TYPE_ADD, $path, $value);
  }

  /**
   * @psalm-template TPathIn of string
   * @psalm-template TValueIn
   * @psalm-param TPathIn $path
   * @psalm-param TValueIn $value
   * @psalm-return Operation<self::TYPE_REMOVE, TPathIn, TValueIn>
   * @psalm-pure
   */
  public static function remove(string $path, mixed $value): self
  {
    return new self(self::TYPE_REMOVE, $path, $value);
  }

  /**
   * @psalm-template TPathIn of string
   * @psalm-template TValueIn
   * @psalm-param TPathIn $path
   * @psalm-param TValueIn $value
   * @psalm-return Operation<self::TYPE_REPLACE, TPathIn, TValueIn>
   * @psalm-pure
   */
  public static function replace(string $path, mixed $value): self
  {
    return new self(self::TYPE_REPLACE, $path, $value);
  }
}
