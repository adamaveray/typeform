<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Utils;

use AdamAveray\Typeform\Tests\TestCase;
use AdamAveray\Typeform\Utils\Operation;

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
      self::getConst(Operation::class, 'TYPE_ADD'),
      self::TEST_PATH,
      self::TEST_VALUE,
      Operation::add(self::TEST_PATH, self::TEST_VALUE),
    );
  }

  public function testRemove(): void
  {
    $this->assertOperationFormats(
      self::getConst(Operation::class, 'TYPE_REMOVE'),
      self::TEST_PATH,
      self::TEST_VALUE,
      Operation::remove(self::TEST_PATH, self::TEST_VALUE),
    );
  }

  public function testReplace(): void
  {
    $this->assertOperationFormats(
      self::getConst(Operation::class, 'TYPE_REPLACE'),
      self::TEST_PATH,
      self::TEST_VALUE,
      Operation::replace(self::TEST_PATH, self::TEST_VALUE),
    );
  }

  /**
   * @param mixed $expectedValue
   */
  private function assertOperationFormats(
    string $expectedType,
    string $expectedPath,
    $expectedValue,
    Operation $operation
  ): void {
    $this->assertEquals(
      [
        'op' => $expectedType,
        'path' => $expectedPath,
        'value' => $expectedValue,
      ],
      $operation->formatForRequest(),
      'The operation should be formatted correctly',
    );
  }
}
