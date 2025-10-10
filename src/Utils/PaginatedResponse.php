<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-template T of Model
 * @psalm-type Model = \AdamAveray\Typeform\Models\Model
 * @psalm-type RawData = array{ page_count: int, total_items: int, items: list<mixed> }
 * @psalm-immutable
 */
final readonly class PaginatedResponse
{
  public int $pageCount;
  public int $pageItems;
  public int $totalItems;
  /**
   * @psalm-var list<T>
   */
  public array $items;
  public bool $containsAllItems;

  /**
   * @psalm-param RawData $data
   */
  private function __construct(array $data)
  {
    $this->pageCount = $data['page_count'];
    $this->totalItems = $data['total_items'];
    /** @psalm-var list<T> */
    $this->items = $data['items'];
    $this->pageItems = \count($this->items);
    $this->containsAllItems = $this->pageCount === 1 && $this->pageItems === $this->totalItems;
  }

  /**
   * @psalm-template TStatic of Model
   * @param class-string<TStatic> $modelClass
   * @psalm-param class-string $modelClass See https://github.com/vimeo/psalm/issues/7913
   * @psalm-param RawData $data
   * @psalm-return self<TStatic>
   */
  public static function createForModel(string $modelClass, array $data): self
  {
    /** @psalm-var class-string<TStatic> $modelClass */
    $data['items'] = array_map(static fn(array $item): Model => new $modelClass($item), $data['items']);
    /** @psalm-var self<TStatic> $instance */
    $instance = new self($data);
    return $instance;
  }
}
