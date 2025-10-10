# Forms

The following methods are available on ApiClient instances to manage forms. Unless otherwise specified, all received and returned model classes are within the `...\Models\Forms` namespace.

- ## `getForms`

  **`getForms(string|Models\Workspaces\WorkspaceStub|null $workspace = null, ?string $search = null, ?int $page1 = null, ?int $pageSize = null): PaginatedResponse<FormStub>`**

  Load all forms, optionally limited to a specific workspace. [(API docs)](https://www.typeform.com/developers/create/reference/retrieve-forms/)

- ## `getForm`

  **`getForm(string|Form|FormStub $form): Form`**

  Load a single form. [(API docs)](https://www.typeform.com/developers/create/reference/retrieve-form/)

- ## `createForm`

  **`createForm(array $data): Form`**

  Create a new form. See the API docs for the possible values within `$data`. [(API docs)](https://www.typeform.com/developers/create/reference/create-form/)

- ## `deleteForm`

  **`deleteForm(string|Form|FormStub $form): void`**

  Delete an existing form. [(API docs)](https://www.typeform.com/developers/create/reference/delete-form/)

- ## `updateForm`

  **`updateForm(string|Form|FormStub $form, list<Operation>|Operation $operations): void`**

  Update an existing form with one or more operations. [(API docs)](https://www.typeform.com/developers/create/reference/update-form-patch/)

- ## `overwriteForm`

  **`overwriteForm(string|Form|FormStub $form, array $data): void`**

  Replace all settings of an existing form. See the API docs for the possible values within `$data`. [(API docs)](https://www.typeform.com/developers/create/reference/update-form/)

- ## `getFormMessages`

  **`getFormMessages(string|Form|FormStub $form): array<string, string>`**

  Load all form messages for a specific form. See the API docs for the possible message keys. [(API docs)](https://www.typeform.com/developers/create/reference/retrieve-custom-form-messages/)

- ## `updateFormMessages`

  **`updateFormMessages(string|Form|FormStub $form, array<string, string> $messages): void`**

  Update the form messages for a specific form. See the API docs for the possible message keys. [(API docs)](https://www.typeform.com/developers/create/reference/update-custom-messages/)

- ## `getResponses`

  **`getResponses(string|Form $form, array $options): PaginatedResponse<Response>`**

  Load responses for a specific form. See the API docs for the possible `$options` values. [(API docs)](https://www.typeform.com/developers/responses/reference/retrieve-responses/)

- ## `deleteResponses`

  **`deleteResponses(string|Form|FormStub $form, list<string|Response> $responses): void`**

  Delete specific responses from a specified form. [(API docs)](https://www.typeform.com/developers/responses/reference/delete-responses/)

- ## `getResponseFile`

  **`getResponseFile(string|Form|FormStub $form, string|Response $response, string|Field $field, string $filename): string`**

  Load the raw source for a specific form response file. [(API docs)](https://www.typeform.com/developers/responses/reference/retrieve-response-file/)

- ## `getFormInsights`

  **`getFormInsights(string|Form $form): InsightsSummary`**

  Load the insights summary a specific form. [(API docs)](https://www.typeform.com/developers/responses/reference/retrieve-form-insights/)

All Form and FormStub instances also provide a `->getEmbed()` method to allow generating HTML embeds. See the [Embeds docs](Embeds.md) for more details.
