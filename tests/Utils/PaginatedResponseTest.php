<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Utils;

use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Tests\Dummies\DummyModel;
use AdamAveray\Typeform\Tests\TestCase;
use AdamAveray\Typeform\Utils\PaginatedResponse;

/**
 * @coversDefaultClass PaginatedResponse
 */
class PaginatedResponseTest extends TestCase
{
  public function testCreation(): void
  {
    $totalItems = 321;
    $pageCount = 5;
    $itemsData = [
      ['id' => '123', 'dummy_value' => 'One'],
      ['id' => '456', 'dummy_value' => 'Two'],
      ['id' => '789', 'dummy_value' => 'Three'],
    ];
    $instances = self::instantiateModels(DummyModel::class, $itemsData);

    $response = PaginatedResponse::createForModel(DummyModel::class, [
      'page_count' => $pageCount,
      'total_items' => $totalItems,
      'items' => $itemsData,
    ]);
    $this->assertEquals($instances, $response->items, 'The models should be instantiated correctly');
    $this->assertEquals($totalItems, $response->totalItems, 'The total items count should be set correctly');
    $this->assertEquals($pageCount, $response->pageCount, 'The page count should be set correctly');
    $this->assertEquals(\count($itemsData), $response->pageItems, 'The page items should be calculated correctly');
  }

  /**
   * @dataProvider containsAllItemsDataProvider
   * @psalm-param class-string<Model> $modelClass
   * @psalm-param list<mixed> $itemsData
   */
  public function testContainsAllItems(
    bool $expected,
    string $modelClass,
    array $itemsData,
    int $pageCount,
    int $totalItems,
  ): void {
    $response = PaginatedResponse::createForModel($modelClass, [
      'page_count' => $pageCount,
      'total_items' => $totalItems,
      'items' => $itemsData,
    ]);
    $this->assertEquals(
      $expected,
      $response->containsAllItems,
      'Whether the response contains all items should be determined correctly',
    );
  }

  public function containsAllItemsDataProvider(): iterable
  {
    $itemsData = [
      ['id' => '123', 'dummy_value' => 'One'],
      ['id' => '456', 'dummy_value' => 'Two'],
      ['id' => '789', 'dummy_value' => 'Three'],
    ];
    yield 'Complete' => [
      'expected' => true,
      'modelClass' => DummyModel::class,
      'itemsData' => $itemsData,
      'pageCount' => 1,
      'totalItems' => count($itemsData),
    ];
    yield 'Incomplete, first page' => [
      'expected' => false,
      'modelClass' => DummyModel::class,
      'itemsData' => $itemsData,
      'pageCount' => 1,
      'totalItems' => count($itemsData) + 1,
    ];
    yield 'Incomplete, other page' => [
      'expected' => false,
      'modelClass' => DummyModel::class,
      'itemsData' => $itemsData,
      'pageCount' => 2,
      'totalItems' => count($itemsData),
    ];
  }
}
