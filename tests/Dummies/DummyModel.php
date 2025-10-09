<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Dummies;

use AdamAveray\Typeform\Models\Model;

/** @psalm-immutable */
class DummyModel extends Model
{
  public readonly string $dummyValue;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->dummyValue = $data['dummy_value'];
  }
}
