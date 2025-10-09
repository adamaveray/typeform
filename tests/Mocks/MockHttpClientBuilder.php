<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Mocks;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @psalm-suppress InternalClass,InternalMethod
 */
class MockHttpClientBuilder
{
  private readonly TestCase $testCase;
  private readonly MockBuilder $mockBuilder;
  /** @var list<MockRequest> */
  private array $requests = [];

  public function __construct(TestCase $testCase)
  {
    $this->testCase = $testCase;
    $this->mockBuilder = (new MockBuilder($testCase, HttpClientInterface::class))->onlyMethods(['request', 'stream']);
  }

  /** @return $this */
  public function addRequest(MockRequest $request): self
  {
    $this->requests[] = $request;
    return $this;
  }

  /**
   * @param list<MockRequest> $requests
   * @return $this
   */
  public function addRequests(array $requests): self
  {
    foreach ($requests as $request) {
      $this->addRequest($request);
    }
    return $this;
  }

  /**
   * @return MockObject&HttpClientInterface
   */
  public function getMock(string $accessToken): MockObject
  {
    /** @var MockObject&HttpClientInterface $mock */
    $mock = $this->mockBuilder->getMockForAbstractClass();

    $mock->method('request')->willReturnCallback($this->getHandler($accessToken));

    // Streaming should never be used
    $mock->expects(new InvokedCount(0))->method('stream');

    return $mock;
  }

  private function getHandler(string $accessToken): callable
  {
    return function (string $method, string $url, array $options = []) use ($accessToken): ResponseInterface {
      foreach ($this->requests as $request) {
        if ($request->matches($method, $url, $options, $accessToken)) {
          return $request->getResponse($this->testCase);
        }
      }

      throw new ExpectationFailedException('Request [' . $method . '] ' . $url . ' was not defined');
    };
  }
}
