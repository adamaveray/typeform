# Typeform API Client

A strongly-typed interface to the complete [Typeform API](https://developer.typeform.com/get-started/).

```
composer require adamaveray/typeform
```

## Setup

1. Install the library with Composer (`composer require adamaveray/typeform`).
2. [Generate a Typeform personal access token](https://developer.typeform.com/get-started/personal-access-token/).

## Usage

To interact with the Typeform API, create a new instance of the [ApiClient class](src/ApiClient.php) and pass in a personal access token:

```php
$apiClient = new \AdamAveray\Typeform\ApiClient('{access-token}');
```

See the following sections for full documentation:

- [Common](docs/Common.md)
- [Workspaces](docs/Workspaces.md)
- [Forms](docs/Forms.md)
- [Images](docs/Images.md)
- [Themes](docs/Themes.md)
- [Webhooks](docs/Webhooks.md)
- [Misc](docs/Misc.md)
- [Embeds](docs/Embeds.md)

---

[MIT Licence](LICENSE)
