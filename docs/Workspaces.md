# Workspaces

The following methods are available on ApiClient instances to manage workspaces. Unless otherwise specified, all received and returned model classes are within the `...\Models\Workspaces` namespace.

- ## `getAccountWorkspaces`

  **`getAccountWorkspaces(string $accountId, ?string $search = null, ?int $page1 = null, ?int $pageSize = null): PaginatedResponse<WorkspaceStub>`**

  Load the workspaces for the given account. [(API Docs)](https://www.typeform.com/developers/create/reference/retrieve-account-workspaces/)

- ## `getWorkspaces`

  **`getWorkspaces(?string $search = null, ?int $page1 = null, ?int $pageSize = null): PaginatedResponse<WorkspaceStub>`**

  Load all workspaces the access token has access to. [(API docs)](https://www.typeform.com/developers/create/reference/retrieve-workspaces/)

- ## `getWorkspace`

  **`getWorkspace(string|Workspace|WorkspaceStub $workspace): Models\Workspaces\Workspace`**

  Load a single workspace. [(API docs)](https://www.typeform.com/developers/create/reference/retrieve-workspace/)

- ## `createWorkspace`

  **`createWorkspace(string $name): Models\Workspaces\Workspace`**

  Create a new workspace. [(API docs)](https://www.typeform.com/developers/create/reference/create-workspace/)

- ## `deleteWorkspace`

  **`deleteWorkspace(string|Workspace|WorkspaceStub $workspace): void`**

  Delete an existing workspace. [(API docs)](https://www.typeform.com/developers/create/reference/delete-workspace/)

- ## `updateWorkspace`

  **`updateWorkspace(string|Workspace|WorkspaceStub $workspace, list<Operation>|Operation $operation): Models\Workspaces\Workspace`**

  Update an existing workspace with one or more operations. [(API docs)](https://www.typeform.com/developers/create/reference/update-workspace/)
