# [Embeds](https://developer.typeform.com/embed/)

The JavaScript Embed SDK is out-of-scope for this library, however utility methods are available to assist with loading it onto a page or displaying a standalone form:

```php
use AdamAveray\Typeform\Utils\FormEmbed;

$form = $apiClient->getForm('form-id');

$embed = $form->getEmbed(FormEmbed::TYPE_INLINE);
$embed
  ->setSeamless()
  ->setOption('disableAutoFocus', true)
  ->setHiddenField('user', $_SESSION['user']);

echo $embed->getHtml();
```

Call `->getEmbed(FormEmbed::TYPE_* $type)` on a `Form` or `FormStub` instance to generate a `FormEmbed` instance.

## Embed Types

The following types should be passed to `->getEmbed()` calls to determine how the form will be rendered:

- **`FormEmbed::TYPE_INLINE`:** Will render the form directly on the page. See [the API docs](https://developer.typeform.com/embed/inline/) for full details.
-
- **`FormEmbed::TYPE_MODAL`:** Will render a button opening the form in a modal when clicked. See [the API docs](https://developer.typeform.com/embed/modal/) for full details.

## Configuration

The following methods are available to customise rendering the embed:

- **`setLoadLib(bool $loadLib, bool $async = true): $this`:** Set whether to also load the JavaScript SDK in the generated HTML. Passing `false` to `$async` will switch from loading the SDK asynchronously (the default) to blocking page rendering until the SDK is loaded.

- _(`TYPE_INLINE` only)_ **`setSeamless(bool $seamless = true): $this`:** Set whether to render the form seamlessly on the page, with no header, footer, or background. See [the API docs](https://developer.typeform.com/embed/inline/#seamless-inline-embed) for more information.

- _(`TYPE_MODAL` only)_ **`setLabel(string $label): $this`:** Set the text within the generated `<button>` element for opening the form modal.

- _(`TYPE_MODAL` only)_ **`setModalType(FormEmbed::MODAL_* $modalType): $this`:** Set the type of modal to display the form in. See [the API docs](https://developer.typeform.com/embed/modal/) for details on each possible modal type.

- **`setOption(string $option, mixed $value): $this`:** Set a single embed options. See [the API docs](https://developer.typeform.com/embed/configuration/#available-options) for all available options.

- **`setOptions(array $options, bool $merge = true): $this`:** Set a series of embed options. See [the API docs](https://developer.typeform.com/embed/configuration/#available-options) for all available options. Passing `false` as `$merge` will remove any previously-set options.

- **`setHiddenField(string|Field $field, string $value): $this`:** Set a single hidden field value. See [the API docs](https://developer.typeform.com/embed/hidden-fields/) for further information.

- **`setHiddenFields(array $fields, string $value, bool $merge = true): $this`:** Set a series of hidden field values. See [the API docs](https://developer.typeform.com/embed/hidden-fields/) for further information. Passing `false` as `$merge` will remove any previously-set hidden field values.

## Rendering

The `->getHtml()` method provides a simple way to render a form suitable for most use cases. If further customisation or direct use of the JavaScript SDK is required additional methods are available.

- **`getHtml(): string`/`__toString(): string`:** Generate the full HTML required to render the form on a page, including loading the JavaScript SDK unless configured otherwise.

- **`getLibHtml(): string`: string`:** Generate the HTML to load only the JavaScript SDK without displaying the form itself, useful for loading the SDK only once if multiple forms will be rendered on the same page.

- **`getFullOptions(): array<string,mixed>`:** Generate the complete set of options able to be used manually in the JavaScript SDK. See [the API docs](https://developer.typeform.com/embed/configuration/#available-options) for the possible options and values.

- **`getHtmlAttrs(): string`:** Generate the HTML attributes automatically interpreted by the JavaScript SDK, to allow assigning separately to a custom HTML element.
