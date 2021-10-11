# Images

The following methods are available on ApiClient instances to manage images. Unless otherwise specified, all received and returned model classes are within the `...\Models\Images` namespace.

- ## `getImages`

  **`getImages(): Image[]`**

  Load all images within the account. [(API docs)](https://developer.typeform.com/create/reference/retrieve-images-collection/)

- ## `getImage`

  **`getImage(string|Image $image, ?ApiClient::IMAGE_FORMAT_* $format = null, ?ApiClient::IMAGE_SIZE_* $size = null): Image`**

  Load a specific image, optionally with a predefined format and size. See the API docs for details on formats and sizes. ([Image API docs](https://developer.typeform.com/create/reference/retrieve-image/); Format API docs: [`background`](https://developer.typeform.com/create/reference/retrieve-background-by-size/), [`choice-image`](https://developer.typeform.com/create/reference/retrieve-choice-image-by-size/), [`image`](https://developer.typeform.com/create/reference/retrieve-image-by-size/))

- ## `getImageSource`

  **`getImageSource(string|Image $image, ?ApiClient::IMAGE_FORMAT_* $format = null, ?ApiClient::IMAGE_SIZE_* $size = null): string`**

  Load the raw source for a specific image, optionally with a predefined format and size. See the API docs for details on formats and sizes. ([Image API docs](https://developer.typeform.com/create/reference/retrieve-image/); Format API docs: [`background`](https://developer.typeform.com/create/reference/retrieve-background-by-size/), [`choice-image`](https://developer.typeform.com/create/reference/retrieve-choice-image-by-size/), [`image`](https://developer.typeform.com/create/reference/retrieve-image-by-size/))

- ## `createImage`

  **`createImage(string $fileName, string $base64Source, string $url): array`**

  Create a new image. The return format is indeterminate – see the API docs. [(API docs)](https://developer.typeform.com/create/reference/create-image/)

- ## `deleteImage`

  **`deleteImage(string $fileName, string $base64Source, string $url): array`**

  Delete an existing image. [(API docs)](https://developer.typeform.com/delete/reference/delete-image/)
