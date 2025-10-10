# Webhooks

The following methods are available on ApiClient instances to manage webhooks. Unless otherwise specified, all received and returned model classes are within the `...\Models\Webhooks` namespace.

- ## `getWebhooks`

  **`getWebhooks(string|Models\Forms\Form $form): list<Webhook>`**

  Load all webhooks for a specific form. [(API docs)](https://www.typeform.com/developers/webhooks/reference/retrieve-webhooks/)

- ## `getWebhook`

  **`getWebhook(string|Models\Forms\Form $form, string|Webhook $tagOrWebhook): Webhook`**

  Load a specific webhook by tag for a specific form. `$tagOrWebhook` should be a webhook tag or Webhook instance. [(API docs)](https://www.typeform.com/developers/webhooks/reference/retrieve-single-webhook/)

- ## `deleteWebhook`

  **`deleteWebhook(string|Models\Forms\Form $form, string|Webhook $tagOrWebhook): void`**

  Delete an existing webhook by tag from a specific form. `$tagOrWebhook` should be a webhook tag or Webhook instance. [(API docs)](https://www.typeform.com/developers/webhooks/reference/delete-webhook/)

- ## `createUpdateWebhook`

  **`createUpdateWebhook(string|Models\Forms\Form $form, string|Webhook $tagOrWebhook, bool $enabled, string $url, ?string $secret = null, bool $verifySsl = true): Webhook`**

  Create a new webhook or update an existing webhook for a specific form. `$tagOrWebhook` should be a webhook tag or Webhook instance. See the API docs for details on the additional parameters. [(API docs)](https://www.typeform.com/developers/webhooks/reference/create-or-update-webhook/)
