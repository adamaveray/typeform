<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Models;

use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Tests\Dummies\DummyModel;
use AdamAveray\Typeform\Tests\Dummies\DummyTimestampModel;
use AdamAveray\Typeform\Tests\TestCase;

/**
 * @coversDefaultClass Model
 */
class ModelTest extends TestCase
{
  public function testBasicData(): void
  {
    $id = '123';
    $data = [
      'id' => $id,
      'dummy_value' => 'Dummy value',
    ];
    $model = new DummyModel($data);
    $this->assertEquals($id, $model->id, 'The ID should be stored correctly');
    $this->assertEquals($data, $model->rawData, 'The raw data should be stored correctly');
  }

  /**
   * @covers ::ref
   * @covers ::<!public>
   */
  public function testRef(): void
  {
    $href = 'https://www.example.com/ref/';
    $ref = DummyModel::ref([
      'href' => $href,
    ]);
    $this->assertEquals($href, $ref->href, 'The correct ref href should be set');
    $this->assertInstanceOf(
      DummyModel::class,
      $ref->instantiate(['id' => '123', 'dummy_value' => 'Value']),
      'The ref should instantiate the correct class',
    );
  }

  /**
   * @covers ::collectionRef
   * @covers ::<!public>
   */
  public function testCollectionRef(): void
  {
    $href = 'https://www.example.com/ref/';
    $count = 3;
    $ref = DummyModel::collectionRef([
      'href' => $href,
      'count' => $count,
    ]);
    $this->assertEquals($href, $ref->href, 'The correct ref href should be set');
    $this->assertEquals($count, $ref->count, 'The correct ref count should be set');
    $this->assertContainsOnlyInstancesOf(
      DummyModel::class,
      $ref->instantiateCollection([
        ['id' => '123', 'dummy_value' => 'Value 1'],
        ['id' => '456', 'dummy_value' => 'Value 2'],
      ]),
      'The ref should instantiate the correct class',
    );
  }

  /**
   * @covers ::convertTimestamp
   */
  public function testConvertTimestamps(): void
  {
    $timestamp = '2000-01-30T12:11:10.123456Z';
    $datetime = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
      ->setDate(2000, 1, 30)
      ->setTime(12, 11, 10, 123456);

    $model = new DummyTimestampModel(['id' => '123', 'dummy_time' => $timestamp]);
    $this->assertEquals(
      $datetime->format('c'),
      $model->dummyTimestamp->format('c'),
      'The timestamp should be parsed correctly',
    );
  }

  /**
   * @covers ::convertTimestamp
   */
  public function testConvertInvalidTimestamp(): void
  {
    $this->expectException(\RuntimeException::class);
    new DummyTimestampModel(['id' => '123', 'dummy_time' => 'Not A Timestamp']);
  }
}
