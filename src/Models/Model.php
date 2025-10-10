<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models;

use AdamAveray\Typeform\Utils\Refs;

/**
 * @template TRawData of (array{ id: string } & array<string, mixed>)
 * @psalm-consistent-constructor
 * @psalm-immutable
 * @psalm-import-type RawData from \AdamAveray\Typeform\Utils\Refs\CollectionRef as CollectionRefRawData
 * @psalm-import-type RawData from \AdamAveray\Typeform\Utils\Refs\SingleRef as SingleRefRawData
 */
abstract readonly class Model
{
  private const TIMESTAMP_FORMAT_SECONDS = 'Y-m-d\\TH:i:s\\Z';
  private const TIMESTAMP_FORMAT_MICROSECONDS = 'Y-m-d\\TH:i:s.u\\Z';

  public string $id;
  /** @var TRawData $rawData */
  public array $rawData;

  /**
   * @param TRawData $data
   */
  public function __construct(array $data)
  {
    $this->id = $data['id'];
    $this->rawData = $data;
  }

  /**
   * @pure
   */
  protected static function convertTimestamp(string $timestamp): \DateTimeImmutable
  {
    $timestamp = preg_replace('~[+-]00:00$~', 'Z', $timestamp);
    $format = preg_match('~\.\d+Z$~', $timestamp)
      ? self::TIMESTAMP_FORMAT_MICROSECONDS
      : self::TIMESTAMP_FORMAT_SECONDS;
    /** @psalm-suppress ImpureMethodCall */
    $datetime = \DateTimeImmutable::createFromFormat($format, $timestamp, new \DateTimeZone('UTC'));
    if ($datetime === false) {
      throw new \RuntimeException('Invalid timestamp');
    }
    return $datetime;
  }

  /**
   * @param SingleRefRawData $data
   * @return \AdamAveray\Typeform\Utils\Refs\SingleRef<static>
   * @pure
   */
  public static function ref(array $data): Refs\SingleRef
  {
    /**
     * @var Refs\SingleRef<static> See https://github.com/vimeo/psalm/issues/7913
     */
    return new Refs\SingleRef(static::class, $data);
  }

  /**
   * @param CollectionRefRawData $data
   * @return \AdamAveray\Typeform\Utils\Refs\CollectionRef<static>
   * @pure
   */
  public static function collectionRef(array $data): Refs\CollectionRef
  {
    /**
     * @var Refs\CollectionRef<static> See https://github.com/vimeo/psalm/issues/7913
     */
    return new Refs\CollectionRef(static::class, $data);
  }
}
