<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Utils;

use AdamAveray\Typeform\Tests\TestCase;
use AdamAveray\Typeform\Utils\Operation;
use AdamAveray\Typeform\Utils\OperationType;

/**
 * @coversDefaultClass Operation
 */
class OperationTest extends TestCase
{
  private const TEST_PATH = '/some/path';
  private const TEST_VALUE = 'abc123';

  public function testAdd(): void
  {
    $this->assertOperationFormats(
      OperationType::Add,
      self::TEST_PATH,
      self::TEST_VALUE,
      Operation::add(self::TEST_PATH, self::TEST_VALUE),
    );
  }

  public function testRemove(): void
  {
    $this->assertOperationFormats(
      OperationType::Remove,
      self::TEST_PATH,
      self::TEST_VALUE,
      Operation::remove(self::TEST_PATH, self::TEST_VALUE),
    );
  }

  public function testReplace(): void
  {
    $this->assertOperationFormats(
      OperationType::Replace,
      self::TEST_PATH,
      self::TEST_VALUE,
      Operation::replace(self::TEST_PATH, self::TEST_VALUE),
    );
  }

  private function assertOperationFormats(
    OperationType $expectedType,
    string $expectedPath,
    mixed $expectedValue,
    Operation $operation,
  ): void {
    $this->assertEquals(
      [
        'op' => $expectedType->value,
        'path' => $expectedPath,
        'value' => $expectedValue,
      ],
      $operation->formatForRequest(),
      'The operation should be formatted correctly',
    );
  }
}
