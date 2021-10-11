# Themes

The following methods are available on ApiClient instances to manage themes. Unless otherwise specified, all received and returned model classes are within the `...\Models\Themes` namespace.

- ## `getThemes`

  **`getThemes(?int $page1 = null, ?int $pageSize = null): PaginatedResponse<Theme>`**

  Load all themes. [(API docs)](https://developer.typeform.com/create/reference/retrieve-themes/)

- ## `getTheme`

  **`getTheme(string|Theme $theme): Theme`**

  Load a specific theme. [(API docs)](https://developer.typeform.com/create/reference/retrieve-theme/)

- ## `createTheme`

  **`createTheme(array $data): Theme`**

  Create a new theme. See the API docs for the possible values within `$data`. [(API docs)](https://developer.typeform.com/create/reference/create-theme/)

- ## `deleteTheme`

  **`deleteTheme(string|Theme $theme): void`**

  Delete an existing theme. [(API docs)](https://developer.typeform.com/create/reference/delete-theme/)

- ## `updateTheme`

  **`updateTheme(string|Theme $theme, array $data): Theme`**

  Update an existing theme. See the API docs for the possible values within `$data`. [(API docs)](https://developer.typeform.com/create/reference/update-theme/)
