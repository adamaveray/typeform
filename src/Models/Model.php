<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models;

use AdamAveray\Typeform\Utils\Refs;

/**
 * @psalm-immutable
 * @psalm-consistent-constructor
 */
abstract class Model
{
  private const TIMESTAMP_FORMAT_SECONDS = 'Y-m-d\\TH:i:s\\Z';
  private const TIMESTAMP_FORMAT_MICROSECONDS = 'Y-m-d\\TH:i:s.u\\Z';

  public string $id;
  public array $rawData;

  public function __construct(array $data)
  {
    $this->id = $data['id'];
    $this->rawData = $data;
  }

  /**
   * @psalm-pure
   */
  protected static function convertTimestamp(string $timestamp): \DateTimeImmutable
  {
    $format = preg_match('~\.\d+Z$~', $timestamp)
      ? self::TIMESTAMP_FORMAT_MICROSECONDS
      : self::TIMESTAMP_FORMAT_SECONDS;
    $datetime = \DateTimeImmutable::createFromFormat($format, $timestamp, new \DateTimeZone('UTC'));
    if ($datetime === false) {
      throw new \RuntimeException('Invalid timestamp');
    }
    return $datetime;
  }

  /**
   * @psalm-return \AdamAveray\Typeform\Utils\Refs\SingleRef<static>
   * @psalm-pure
   */
  public static function ref(array $data): Refs\SingleRef
  {
    /** @psalm-suppress ImpureMethodCall */
    return new Refs\SingleRef(static::class, $data);
  }

  /**
   * @psalm-return \AdamAveray\Typeform\Utils\Refs\CollectionRef<static>
   * @psalm-pure
   */
  public static function collectionRef(array $data): Refs\CollectionRef
  {
    /** @psalm-suppress ImpureMethodCall */
    return new Refs\CollectionRef(static::class, $data);
  }
}
