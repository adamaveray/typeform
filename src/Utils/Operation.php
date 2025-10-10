<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

/**
 * @template TType of OperationType
 * @template TPath of string
 * @template TValue af mixed
 * @psalm-immutable
 */
final readonly class Operation
{
  /**
   * @var TType
   */
  private OperationType $type;
  /**
   * @var TPath
   */
  private string $path;
  /**
   * @var TValue
   */
  private mixed $value;

  /**
   * @param TType|string $type
   * @param TPath $path
   * @param TValue $value
   */
  private function __construct(string|OperationType $type, string $path, mixed $value)
  {
    // Convert legacy string to enum case
    if (\is_string($type)) {
      /** @var TType $type */
      $type = OperationType::from($type);
    }

    $this->type = $type;
    $this->path = $path;
    $this->value = $value;
  }

  /**
   * @return array{ op: string, path: TPath, value: TValue }
   * @mutation-free
   */
  public function formatForRequest(): array
  {
    return [
      'op' => $this->type->value,
      'path' => $this->path,
      'value' => $this->value,
    ];
  }

  /**
   * @template TPathIn of string
   * @template TValueIn
   * @param TPathIn $path
   * @param TValueIn $value
   * @return Operation<OperationType::Add, TPathIn, TValueIn>
   * @pure
   */
  public static function add(string $path, mixed $value): self
  {
    return new self(OperationType::Add, $path, $value);
  }

  /**
   * @template TPathIn of string
   * @template TValueIn
   * @param TPathIn $path
   * @param TValueIn $value
   * @return Operation<OperationType::Remove, TPathIn, TValueIn>
   * @pure
   */
  public static function remove(string $path, mixed $value): self
  {
    return new self(OperationType::Remove, $path, $value);
  }

  /**
   * @template TPathIn of string
   * @template TValueIn
   * @param TPathIn $path
   * @param TValueIn $value
   * @return Operation<OperationType::Replace, TPathIn, TValueIn>
   * @pure
   */
  public static function replace(string $path, mixed $value): self
  {
    return new self(OperationType::Replace, $path, $value);
  }
}
