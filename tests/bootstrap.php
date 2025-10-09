<?php
declare(strict_types=1);

// Convert all errors to exceptions
error_reporting(E_ALL);
set_error_handler(static function (
  int $errorNo,
  string $errorString,
  string $errorFile = '',
  int $errorLine = 0,
): never {
  throw new \ErrorException($errorString, $errorNo, 1, $errorFile, $errorLine);
});
