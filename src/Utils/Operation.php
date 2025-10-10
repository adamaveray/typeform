<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

/**
 * @psalm-template TType of OperationType
 * @psalm-template TPath of string
 * @psalm-template TValue af mixed
 * @psalm-immutable
 */
final readonly class Operation
{
  /**
   * @psalm-var TType
   */
  private OperationType $type;
  /**
   * @psalm-var TPath
   */
  private string $path;
  /**
   * @psalm-var TValue
   */
  private mixed $value;

  /**
   * @psalm-param TType|string $type
   * @psalm-param TPath $path
   * @psalm-param TValue $value
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
   * @psalm-template TPathIn of string
   * @psalm-template TValueIn
   * @psalm-param TPathIn $path
   * @psalm-param TValueIn $value
   * @psalm-return Operation<OperationType::Add, TPathIn, TValueIn>
   * @psalm-pure
   */
  public static function add(string $path, mixed $value): self
  {
    return new self(OperationType::Add, $path, $value);
  }

  /**
   * @psalm-template TPathIn of string
   * @psalm-template TValueIn
   * @psalm-param TPathIn $path
   * @psalm-param TValueIn $value
   * @psalm-return Operation<OperationType::Remove, TPathIn, TValueIn>
   * @psalm-pure
   */
  public static function remove(string $path, mixed $value): self
  {
    return new self(OperationType::Remove, $path, $value);
  }

  /**
   * @psalm-template TPathIn of string
   * @psalm-template TValueIn
   * @psalm-param TPathIn $path
   * @psalm-param TValueIn $value
   * @psalm-return Operation<OperationType::Replace, TPathIn, TValueIn>
   * @psalm-pure
   */
  public static function replace(string $path, mixed $value): self
  {
    return new self(OperationType::Replace, $path, $value);
  }
}
