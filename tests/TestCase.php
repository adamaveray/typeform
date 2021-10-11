<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests;

use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Tests\Mocks\MockHttpClientBuilder;
use AdamAveray\Typeform\Tests\Mocks\MockRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TestCase extends BaseTestCase
{
  protected const TEST_ACCESS_TOKEN = 'test-access-token';

  protected $backupStaticAttributes = false;
  protected $runTestInSeparateProcess = false;

  protected function getMockHttpClientBuilder(): MockHttpClientBuilder
  {
    return new MockHttpClientBuilder($this);
  }

  /**
   * @param MockRequest[] $requests
   * @return MockObject&HttpClientInterface
   */
  protected function getMockHttpClient(array $requests, ?string $accessToken = null): MockObject
  {
    return $this->getMockHttpClientBuilder()
      ->addRequests($requests)
      ->getMock($accessToken ?? static::TEST_ACCESS_TOKEN);
  }

  /**
   * @psalm-template T of Model
   * @psalm-param class-string<T> $className
   * @psalm-param list<array> $items
   * @psalm-return list<T>
   */
  protected static function instantiateModels(string $className, array $items): array
  {
    return array_map(static fn(array $item): Model => new $className($item), $items);
  }

  /**
   * @psalm-param class-string $class
   * @return mixed
   */
  protected static function getConst(string $class, string $name)
  {
    $reflectionClass = new \ReflectionClass($class);
    $value = $reflectionClass->getConstant($name);
    if ($value === false) {
      throw new \InvalidArgumentException($class . '::' . $name . ' not found');
    }
    return $value;
  }
}
