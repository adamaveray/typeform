<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Mocks;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockRequest
{
  /** @readonly */
  public string $method;
  /** @readonly */
  public string $endpoint;
  /** @readonly */
  private array $options;
  private bool $isSealed = false;
  private ?int $statusCode = null;
  private ?array $responseJson = null;
  private ?string $responseText = null;

  private function __construct(string $method, string $endpoint, array $options = [])
  {
    $this->method = $method;
    $this->endpoint = $endpoint;
    $this->options = array_merge(HttpClientInterface::OPTIONS_DEFAULTS, $options);
    $this->options['headers'] = array_merge(['Accept' => 'application/json'], $this->options['headers']);
  }

  public function withResponseJson(array $responseJson): self
  {
    $this->assertUnsealed();
    $this->responseJson = $responseJson;
    $this->responseText = \json_encode($responseJson, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT);
    $this->statusCode = 200;
    return $this;
  }

  public function withResponseText(string $responseText): self
  {
    $this->assertUnsealed();
    $this->responseText = $responseText;
    $this->statusCode = 200;
    return $this;
  }

  public function withEmptyResponse(int $statusCode): self
  {
    $this->assertUnsealed();
    $this->statusCode = $statusCode;
    return $this;
  }

  public function withError(int $statusCode): self
  {
    $this->assertUnsealed();
    $this->statusCode = $statusCode;
    return $this;
  }

  public function matches(string $method, string $endpoint, array $options, string $accessToken): bool
  {
    if ($this->method !== $method || $this->endpoint !== $endpoint) {
      return false;
    }

    foreach ($options as $key => $value) {
      if ($key === 'auth_bearer') {
        // Special access token handler
        if ($value !== $accessToken) {
          return false;
        }
        continue;
      }

      if (($this->options[$key] ?? null) !== $value) {
        return false;
      }
    }

    return true;
  }

  /**
   * @param TestCase $testCase
   * @return MockObject&ResponseInterface
   */
  public function getResponse(TestCase $testCase): MockObject
  {
    $mock = (new MockBuilder($testCase, ResponseInterface::class))
      ->onlyMethods(['toArray', 'getContent'])
      ->getMockForAbstractClass();

    $exception = $this->getException($mock);
    if ($exception === null) {
      $mock->method('toArray')->willReturnCallback(fn(): array => $this->handleToArray());
      $mock->method('getContent')->willReturnCallback(fn(): string => $this->handleGetContent());
    } else {
      $mock->method('toArray')->willThrowException($exception);
      $mock->method('getContent')->willThrowException($exception);
    }

    return $mock;
  }

  private function handleToArray(): array
  {
    return $this->responseJson ?? [];
  }

  private function handleGetContent(): string
  {
    return $this->responseText ?? '';
  }

  private function getException(ResponseInterface $response): ?\Throwable
  {
    $isBetween = fn(int $min, int $max): bool => $this->statusCode >= $min && $this->statusCode < $max;

    if ($isBetween(300, 400)) {
      return new class ($response) extends HttpClientException implements RedirectionExceptionInterface {};
    }

    if ($isBetween(400, 500)) {
      return new class ($response) extends HttpClientException implements ClientExceptionInterface {};
    }

    if ($isBetween(500, 600)) {
      return new class ($response) extends HttpClientException implements ServerExceptionInterface {};
    }

    return null;
  }

  private function assertUnsealed(): void
  {
    if ($this->isSealed) {
      throw new \BadMethodCallException('Response already configured');
    }
  }

  public static function delete(string $endpoint): self
  {
    return new self('DELETE', $endpoint, ['json' => null]);
  }

  public static function get(string $endpoint, array $queryString = []): self
  {
    return new self('GET', $endpoint, ['query' => $queryString]);
  }

  public static function patch(string $endpoint, array $data): self
  {
    return new self('PATCH', $endpoint, ['json' => $data]);
  }

  public static function post(string $endpoint, array $data): self
  {
    return new self('POST', $endpoint, ['json' => $data]);
  }

  public static function put(string $endpoint, array $data): self
  {
    return new self('PUT', $endpoint, ['json' => $data]);
  }
}
