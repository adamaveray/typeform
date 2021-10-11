<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\ApiClient;

use AdamAveray\Typeform\ApiClient;
use AdamAveray\Typeform\Tests\Mocks\MockRequest;
use AdamAveray\Typeform\Tests\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @coversDefaultClass ApiClient
 */
class ApiClientTestCase extends TestCase
{
  protected const DEFAULT_PAGE_SIZE = 123;

  /**
   * @param MockRequest[] $requests
   */
  protected function buildClient(array $requests): ApiClient
  {
    $httpClient = $this->getMockHttpClient($requests);
    $client = new ApiClient(self::TEST_ACCESS_TOKEN, $httpClient);
    $client->setDefaultPageSize(self::DEFAULT_PAGE_SIZE);
    return $client;
  }
}
