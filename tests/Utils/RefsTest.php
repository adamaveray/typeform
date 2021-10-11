<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Utils;

use AdamAveray\Typeform\Tests\Dummies\DummyModel;
use AdamAveray\Typeform\Tests\TestCase;
use AdamAveray\Typeform\Utils\Refs;

class RefsTest extends TestCase
{
  private const TEST_HREF = 'https://www.example.com/ref';

  /**
   * @covers Refs\SingleRef
   * @covers Refs\Ref
   */
  public function testSingle(): void
  {
    $itemData = [
      'id' => '123',
      'dummy_value' => 'Dummy value',
    ];

    $ref = new Refs\SingleRef(DummyModel::class, ['href' => self::TEST_HREF]);
    $this->assertEquals(self::TEST_HREF, $ref->href, 'The href should be set correctly');
    $this->assertEquals(
      new DummyModel($itemData),
      $ref->instantiate($itemData),
      'Instances should be instantiated correctly',
    );
  }

  /**
   * @covers Refs\CollectionRef
   * @covers Refs\Ref
   */
  public function testCollection(): void
  {
    $itemsData = [
      ['id' => '123', 'dummy_value' => 'One'],
      ['id' => '456', 'dummy_value' => 'Two'],
      ['id' => '789', 'dummy_value' => 'Three'],
    ];

    $ref = new Refs\CollectionRef(DummyModel::class, [
      'href' => self::TEST_HREF,
      'count' => \count($itemsData),
    ]);
    $this->assertEquals(self::TEST_HREF, $ref->href, 'The href should be set correctly');
    $this->assertEquals(
      self::instantiateModels(DummyModel::class, $itemsData),
      $ref->instantiateCollection($itemsData),
      'Instances should be instantiated correctly',
    );
  }
}
