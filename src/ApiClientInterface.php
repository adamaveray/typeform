<?php
declare(strict_types=1);

namespace AdamAveray\Typeform;

use AdamAveray\Typeform\Utils\OperationType;
use AdamAveray\Typeform\Values\Images\ImageFormat;
use AdamAveray\Typeform\Values\Images\Sizes;

interface ApiClientInterface
{
  /**
   * @deprecated
   * @see ImageFormat::Background
   */
  public const IMAGE_FORMAT_BACKGROUND = ImageFormat::Background->value;
  /**
   * @deprecated
   * @see ImageFormat::Choice
   */
  public const IMAGE_FORMAT_CHOICE = ImageFormat::Choice->value;
  /**
   * @deprecated
   * @see ImageFormat::Image
   */
  public const IMAGE_FORMAT_IMAGE = ImageFormat::Image->value;

  /**
   * @deprecated
   * @see Sizes\ImageSizeBackground::Default
   */
  public const IMAGE_SIZE_BACKGROUND_DEFAULT = Sizes\ImageSizeBackground::Default->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeBackground::Tablet
   */
  public const IMAGE_SIZE_BACKGROUND_TABLET = Sizes\ImageSizeBackground::Tablet->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeBackground::Mobile
   */
  public const IMAGE_SIZE_BACKGROUND_MOBILE = Sizes\ImageSizeBackground::Mobile->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeBackground::Thumbnail
   */
  public const IMAGE_SIZE_BACKGROUND_THUMBNAIL = Sizes\ImageSizeBackground::Thumbnail->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeChoice::Default
   */
  public const IMAGE_SIZE_CHOICE_DEFAULT = Sizes\ImageSizeChoice::Default->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeChoice::Thumbnail
   */
  public const IMAGE_SIZE_CHOICE_THUMBNAIL = Sizes\ImageSizeChoice::Thumbnail->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeChoice::Supersize
   */
  public const IMAGE_SIZE_CHOICE_SUPERSIZE = Sizes\ImageSizeChoice::Supersize->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeChoice::Supermobile
   */
  public const IMAGE_SIZE_CHOICE_SUPERMOBILE = Sizes\ImageSizeChoice::Supermobile->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeChoice::Supersizefit
   */
  public const IMAGE_SIZE_CHOICE_SUPERSIZEFIT = Sizes\ImageSizeChoice::Supersizefit->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeChoice::Supermobilefit
   */
  public const IMAGE_SIZE_CHOICE_SUPERMOBILEFIT = Sizes\ImageSizeChoice::Supermobilefit->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeImage::Default
   */
  public const IMAGE_SIZE_IMAGE_DEFAULT = Sizes\ImageSizeImage::Default->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeImage::Mobile
   */
  public const IMAGE_SIZE_IMAGE_MOBILE = Sizes\ImageSizeImage::Mobile->value;
  /**
   * @deprecated
   * @see Sizes\ImageSizeImage::Thumbnail
   */
  public const IMAGE_SIZE_IMAGE_THUMBNAIL = Sizes\ImageSizeImage::Thumbnail->value;

  public function setDefaultPageSize(int $defaultPageSize): void;

  /**
   * @template TModel of \AdamAveray\Typeform\Models\Model
   * @param Utils\Refs\SingleRef<TModel> $ref
   * @return TModel
   * @see loadCollectionRef
   */
  public function loadRef(Utils\Refs\SingleRef $ref): Models\Model;

  /**
   * @param bool $loadMax Whether to attempt to load all referenced items, overriding the default page size
   * @template TModel of Models\Model
   * @param Utils\Refs\CollectionRef<TModel> $ref
   * @return list<TModel>
   * @see loadRef
   */
  public function loadCollectionRef(Utils\Refs\CollectionRef $ref, bool $loadMax = true): array;

  /**
   * @link https://developer.typeform.com/create/reference/retrieve-your-own-user/
   */
  public function getCurrentUser(): Models\Users\User;

  /**
   * @return Utils\PaginatedResponse<Models\Workspaces\WorkspaceStub>
   * @link https://developer.typeform.com/create/reference/retrieve-account-workspaces/
   */
  public function getAccountWorkspaces(
    string $accountId,
    ?string $search = null,
    ?int $page1 = null,
    ?int $pageSize = null,
  ): Utils\PaginatedResponse;

  /**
   * @return Utils\PaginatedResponse<Models\Workspaces\WorkspaceStub>
   * @link https://developer.typeform.com/create/reference/retrieve-workspaces/
   */
  public function getWorkspaces(
    ?string $search = null,
    ?int $page1 = null,
    ?int $pageSize = null,
  ): Utils\PaginatedResponse;

  /**
   * @param string|Models\Workspaces\WorkspaceStub $workspace A workspace ID or WorkspaceStub instance
   * @link https://developer.typeform.com/create/reference/retrieve-workspace/
   */
  public function getWorkspace($workspace): Models\Workspaces\Workspace;

  /**
   * @link https://developer.typeform.com/create/reference/create-workspace/
   */
  public function createWorkspace(string $name): Models\Workspaces\Workspace;

  /**
   * @param string|Models\Workspaces\WorkspaceStub $workspace A workspace ID or WorkspaceStub instance
   * @link https://developer.typeform.com/create/reference/delete-workspace/
   */
  public function deleteWorkspace(Models\Workspaces\WorkspaceStub|string $workspace): void;

  /**
   * @param string|Models\Workspaces\WorkspaceStub $workspace A workspace ID or WorkspaceStub instance
   * @param list<Utils\Operation>|Utils\Operation $operations One or more operations to perform on the workspace
   * @link https://developer.typeform.com/create/reference/update-workspace/
   */
  public function updateWorkspace(
    Models\Workspaces\WorkspaceStub|string $workspace,
    Utils\Operation|array $operations,
  ): void;

  /**
   * @param string|Models\Workspaces\WorkspaceStub|null $workspace A workspace ID or WorkspaceStub instance
   * @return Utils\PaginatedResponse<Models\Forms\FormStub>
   * @link https://developer.typeform.com/create/reference/retrieve-forms/
   */
  public function getForms(
    Models\Workspaces\WorkspaceStub|string|null $workspace = null,
    ?string $search = null,
    ?int $page1 = null,
    ?int $pageSize = null,
  ): Utils\PaginatedResponse;

  /**
   * @param string|Models\Forms\FormStub $form A form ID or FormStub instance
   * @link https://developer.typeform.com/create/reference/retrieve-form/
   */
  public function getForm(Models\Forms\FormStub|string $form): Models\Forms\Form;
  /**
   * @link https://developer.typeform.com/create/reference/create-form/
   */
  public function createForm(array $data): Models\Forms\Form;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @link https://developer.typeform.com/create/reference/delete-form/
   */
  public function deleteForm(Models\Forms\Form|Models\Forms\FormStub|string $form): void;
  /**
   * @psalm-type Op = Utils\Operation<OperationType, Models\Forms\Form::OPERATION_PATH_*, mixed>
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param list<Op>|Op $operations One or more operations to perform on the form
   * @link https://developer.typeform.com/create/reference/update-form-patch/
   */
  public function updateForm(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Utils\Operation|array $operations,
  ): void;

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @link https://developer.typeform.com/create/reference/update-form/
   */
  public function overwriteForm(Models\Forms\Form|Models\Forms\FormStub|string $form, array $data): void;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @return array<string, string|null>
   * @link https://developer.typeform.com/create/reference/retrieve-custom-form-messages/
   */
  public function getFormMessages(Models\Forms\Form|Models\Forms\FormStub|string $form): array;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param array<string, string|null> $messages
   * @link https://developer.typeform.com/create/reference/update-custom-messages/
   */
  public function updateFormMessages(Models\Forms\Form|Models\Forms\FormStub|string $form, array $messages): void;
  /**
   * @return list<Models\Images\Image>
   * @link https://developer.typeform.com/create/reference/retrieve-images-collection/
   */
  public function getImages(): array;
  /**
   * @param string|Models\Images\Image $image An image ID or Image instance
   * @link https://developer.typeform.com/create/reference/retrieve-image/
   * @link https://developer.typeform.com/create/reference/retrieve-background-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-choice-image-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-image-by-size/
   */
  public function getImage(
    Models\Images\Image|string $image,
    ?string $format = null,
    ?string $size = null,
  ): Models\Images\Image;

  /**
   * @param string|Models\Images\Image $image An image ID or Image instance
   * @link https://developer.typeform.com/create/reference/retrieve-image/
   * @link https://developer.typeform.com/create/reference/retrieve-background-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-choice-image-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-image-by-size/
   */
  public function getImageSource(
    Models\Images\Image|string $image,
    ?string $format = null,
    ?string $size = null,
  ): string;

  /**
   * @link https://developer.typeform.com/create/reference/create-image/
   */
  public function createImage(string $fileName, string $base64Source, string $url): array;
  /**
   * @param string|Models\Images\Image $image An image ID or Image instance
   * @link https://developer.typeform.com/create/reference/delete-image/
   */
  public function deleteImage(Models\Images\Image|string $image): void;
  /**
   * @return Utils\PaginatedResponse<Models\Themes\Theme>
   * @link https://developer.typeform.com/create/reference/retrieve-themes/
   */
  public function getThemes(?int $page1 = null, ?int $pageSize = null): Utils\PaginatedResponse;
  /**
   * @param string|Models\Themes\Theme $theme A theme ID or Theme instance
   * @link https://developer.typeform.com/create/reference/retrieve-theme/
   */
  public function getTheme(Models\Themes\Theme|string $theme): Models\Themes\Theme;
  /**
   * @link https://developer.typeform.com/create/reference/create-theme/
   */
  public function createTheme(array $data): Models\Themes\Theme;
  /**
   * @param string|Models\Themes\Theme $theme A theme ID or Theme instance
   * @link https://developer.typeform.com/create/reference/delete-theme/
   */
  public function deleteTheme(Models\Themes\Theme|string $theme): void;
  /**
   * @param string|Models\Themes\Theme $theme A theme ID or Theme instance
   * @link https://developer.typeform.com/create/reference/update-theme/
   */
  public function updateTheme(Models\Themes\Theme|string $theme, array $data): Models\Themes\Theme;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param array{
   *   page_size?: int,
   *   since?: string,
   *   until?: string,
   *   after?: string,
   *   before?: string,
   *   included_response_ids?: string|list<string>,
   *   excluded_response_ids?: string|list<string>,
   *   completed?: bool,
   *   sort?: string,
   *   query?: string,
   *   fields?: string|list<string>,
   *   answered_fields?: string|list<string>,
   * } $options
   * @link https://developer.typeform.com/responses/reference/retrieve-responses/
   */
  public function getResponses(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    array $options,
  ): Utils\PaginatedResponse;

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param list<string|Models\Forms\Response> $responses Response IDs or Response instances
   * @link https://developer.typeform.com/responses/reference/delete-responses/
   */
  public function deleteResponses(Models\Forms\Form|Models\Forms\FormStub|string $form, array $responses): void;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param string|Models\Forms\Response $response A response ID or Response instance
   * @param string|Models\Forms\Field $field A field ID or Field instance
   * @link https://developer.typeform.com/responses/reference/retrieve-response-file/
   */
  public function getResponseFile(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Models\Forms\Response|string $response,
    Models\Forms\Field|string $field,
    string $filename,
  ): string;

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @link https://developer.typeform.com/responses/reference/retrieve-form-insights/
   */
  public function getFormInsights(Models\Forms\Form|Models\Forms\FormStub|string $form): Models\Forms\InsightsSummary;
  /**
   * @param string|Models\Jobs\Job $job A job ID or Job instance
   * @link https://developer.typeform.com/responses/reference/rtbf-retrieve-job-status/
   */
  public function rtbfGetJobStatus(string $accountId, Models\Jobs\Job|string $job): Models\Jobs\Status;
  /**
   * @param list<string> $emails
   * @return list<string>
   * @link https://developer.typeform.com/responses/reference/rtbf-delete-responses/
   */
  public function rtbfDeleteResponses(string $accountId, array $emails): array;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @return list<Models\Forms\Webhook>
   * @link https://developer.typeform.com/webhooks/reference/retrieve-webhooks/
   */
  public function getWebhooks(Models\Forms\Form|Models\Forms\FormStub|string $form): array;
  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param string|Models\Forms\Webhook $tagOrWebhook A webhook tag or Webhook instance
   * @link https://developer.typeform.com/webhooks/reference/retrieve-single-webhook/
   */
  public function getWebhook(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Models\Forms\Webhook|string $tagOrWebhook,
  ): Models\Forms\Webhook;

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param string|Models\Forms\Webhook $tagOrWebhook A webhook tag or Webhook instance
   * @link https://developer.typeform.com/webhooks/reference/delete-webhook/
   */
  public function deleteWebhook(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Models\Forms\Webhook|string $tagOrWebhook,
  ): void;

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param string|Models\Forms\Webhook $tagOrWebhook A webhook tag or Webhook instance
   * @link https://developer.typeform.com/webhooks/reference/create-or-update-webhook/
   */
  public function createUpdateWebhook(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Models\Forms\Webhook|string $tagOrWebhook,
    bool $enabled,
    string $url,
    ?string $secret = null,
    bool $verifySsl = true,
  ): Models\Forms\Webhook;
}
