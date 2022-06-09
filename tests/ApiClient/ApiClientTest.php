<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\ApiClient;

use AdamAveray\Typeform\ApiClient;
use AdamAveray\Typeform\Models;
use AdamAveray\Typeform\Tests\Dummies\DummyModel;
use AdamAveray\Typeform\Tests\Mocks\MockRequest;
use AdamAveray\Typeform\Utils\Refs;

/**
 * @coversDefaultClass ApiClient
 */
class ApiClientTest extends ApiClientTestCase
{
  /**
   * @covers ::__construct
   */
  public function testConstructNoHttpClient(): void
  {
    $this->expectNotToPerformAssertions();
    new ApiClient(self::TEST_ACCESS_TOKEN);
  }

  /**
   * @covers ::__construct
   */
  public function testConstructInjectedHttpClient(): void
  {
    new ApiClient(self::TEST_ACCESS_TOKEN, $this->getMockHttpClient([]));
  }

  /**
   * @covers ::setDefaultPageSize
   * @covers ::<!public>
   * @dataProvider invalidPageSizeDataProvider
   */
  public function testInvalidPageSize(int $pageSize): void
  {
    $this->expectException(\InvalidArgumentException::class);
    (new ApiClient(self::TEST_ACCESS_TOKEN))->setDefaultPageSize($pageSize);
  }

  public function invalidPageSizeDataProvider(): iterable
  {
    yield 'Empty' => [0];
    yield 'Too Low' => [-1];
    yield 'Too High' => [ApiClient::PAGE_SIZE_MAX + 1];
  }

  /**
   * @covers ::loadRef
   * @covers ::<!public>
   * @dataProvider loadRefDataProvider
   */
  public function testLoadRef(Models\Model $expected, Refs\SingleRef $ref, MockRequest $request): void
  {
    $httpClient = $this->buildClient([$request]);
    $result = $httpClient->loadRef($ref);
    $this->assertEquals($expected, $result, 'The correct item should be loaded');
  }

  public function loadRefDataProvider(): iterable
  {
    $modelData = [
      'id' => '123',
      'dummy_value' => 'Dummy Value',
    ];
    $endpoint = '/dummy-route/123';
    yield [
      'expected' => new DummyModel($modelData),
      'ref' => DummyModel::ref([
        'href' => $endpoint,
      ]),
      'request' => MockRequest::get($endpoint)->withResponseJson($modelData),
    ];
  }

  /**
   * @covers ::loadCollectionRef
   * @covers ::setDefaultPageSize
   * @covers ::<!public>
   * @dataProvider loadCollectionRefDataProvider
   */
  public function testLoadCollectionRef(
    array $expected,
    Refs\CollectionRef $ref,
    MockRequest $request,
    bool $loadMax = true,
    ?int $pageSize = null,
  ): void {
    $httpClient = $this->buildClient([$request]);
    if ($pageSize !== null) {
      $httpClient->setDefaultPageSize($pageSize);
    }
    $result = $httpClient->loadCollectionRef($ref, $loadMax);
    $this->assertEquals($expected, $result, 'The correct items should be loaded');
  }

  public function loadCollectionRefDataProvider(): iterable
  {
    $endpoint = '/dummy-route/123';

    yield 'Empty' => [
      'expected' => [],
      'ref' => Models\Forms\FormStub::collectionRef([
        'href' => $endpoint,
        'count' => 0,
      ]),
      'request' => MockRequest::get($endpoint, ['page_size' => 0])->withResponseJson([]),
    ];

    $formsData = [
      ['id' => '123', 'dummy_value' => 'one'],
      ['id' => '456', 'dummy_value' => 'two'],
      ['id' => '789', 'dummy_value' => 'three'],
    ];
    yield 'Some, load all' => [
      'expected' => self::instantiateModels(DummyModel::class, $formsData),
      'ref' => DummyModel::collectionRef([
        'href' => $endpoint,
        'count' => count($formsData),
      ]),
      'request' => MockRequest::get($endpoint, ['page_size' => count($formsData)])->withResponseJson($formsData),
    ];

    $size = 2;
    $formsDataSubset = array_slice($formsData, 0, $size);
    yield 'Some, load less' => [
      'expected' => self::instantiateModels(DummyModel::class, $formsDataSubset),
      'ref' => DummyModel::collectionRef([
        'href' => $endpoint,
        'count' => count($formsData),
      ]),
      'request' => MockRequest::get($endpoint, ['page_size' => $size])->withResponseJson($formsDataSubset),
      'loadMax' => false,
      'pageSize' => $size,
    ];

    $formsData = array_fill(0, ApiClient::PAGE_SIZE_MAX + 10, ['id' => '123', 'dummy_value' => 'one']);
    $formsDataSubset = array_slice($formsData, 0, ApiClient::PAGE_SIZE_MAX);
    yield 'Too many, load limit' => [
      'expected' => self::instantiateModels(DummyModel::class, $formsDataSubset),
      'ref' => DummyModel::collectionRef([
        'href' => $endpoint,
        'count' => count($formsData),
      ]),
      'request' => MockRequest::get($endpoint, ['page_size' => ApiClient::PAGE_SIZE_MAX])->withResponseJson(
        $formsDataSubset,
      ),
    ];
  }
}
