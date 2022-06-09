<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Mocks;

use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class HttpClientException extends \Exception implements HttpExceptionInterface
{
  /** @readonly */
  private ResponseInterface $response;

  public function __construct(
    ResponseInterface $response,
    string $message = '',
    int $code = 0,
    ?\Throwable $previous = null,
  ) {
    parent::__construct($message, $code, $previous);
    $this->response = $response;
  }

  public function getResponse(): ResponseInterface
  {
    return $this->response;
  }
}
