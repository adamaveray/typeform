<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Dummies;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{ id: string, dummy_time: string }
 * @psalm-immutable
 * @extends Model<RawData>
 */
class DummyTimestampModel extends Model
{
  public readonly \DateTimeImmutable $dummyTimestamp;

  /** @param RawData $data */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->dummyTimestamp = self::convertTimestamp($data['dummy_time']);
  }
}
