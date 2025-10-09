<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Dummies;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{ id: string, dummy_value: string }
 * @psalm-immutable
 * @extends Model<RawData>
 */
class DummyModel extends Model
{
  public readonly string $dummyValue;

  /** @param RawData $data */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->dummyValue = $data['dummy_value'];
  }
}
