<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Dummies;

use AdamAveray\Typeform\Models\Model;

/** @psalm-immutable */
class DummyTimestampModel extends Model
{
  /** @readonly */
  public \DateTimeImmutable $dummyTimestamp;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->dummyTimestamp = self::convertTimestamp($data['dummy_time']);
  }
}
