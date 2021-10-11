# Common

The following utility methods are available on ApiClient instances:

- **`setDefaultPageSize(int $pageSize): $this`:** Set the default number of items returned per page of paginated responses. The page size must be positive, and cannot be greater than `$apiClient::PAGE_SIZE_MAX`.

- **`loadRef(SingleRef $ref): Model`**: Load a single referenced item (see the Refs section for more detail). The full referenced item’s Model instance will be returned.

- **`loadCollectionRef(CollectionRef $ref, bool $loadMax = true): Model[]`**: Load a collection of referenced items (see the Refs section for more detail). Pass `false` to `$loadMax` to respect the previously-set default page size rather than attempting to load the maximum items possible. No more than `$apiClient::PAGE_SIZE_MAX` items will be loaded. An array of the full referenced item Model instances will be returned.

## Refs

Certain models provide Ref instances referencing either a single item or collection of items. These Ref instances can be passed to the `loadRef()` or `loadCollectionRef()` methods on ApiClient (documented above) to load them without needing to configure the request manually.

## Pagination

Requests for single items will return single Model instances, however requests for a collection of items will return PaginatedResponse instances, providing pagination details:

- **`Model[] $items`:** The Model instances for each item in the current page.

- **`bool $containsAllItems`:** Whether the instance contains all possible items, suggesting pagination is not applicable.

- **`int $pageItems`:** The number of items within the current page.

- **`int $pageCount`:** The total number of pages available.

- **`int $totalItems`:** The total number of items available across all pages.

All page numbers throughout the API are 1-indexed (i.e. the first page is page `1` – not page `0`). For requests accepting optional pagination parameters, `int $page1` is the page number to load, and `int $pageSize` is the number of items to load per page. The `$pageSize` value must be positive, and cannot be more than `$apiClient::PAGE_SIZE_MAX`. If unset, the default page number (documented above) will be used.

## Operations

Some update requests accept one or more operations to perform on the targeted item. These methods accept either a single instance or array of instances of the Operation class.

- **`Operation::add(string $path, mixed $value): Operation`:** Create an 'add' operation.

- **`Operation::remove(string $path, mixed $value): Operation`:** Create a 'remove' operation.

- **`Operation::replace(string $path, mixed $value): Operation`:** Create a 'replace' operation.
