<?php
declare(strict_types=1);

namespace AdamAveray\Typeform;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ApiClient implements ApiClientInterface
{
  private const URL_BASE = 'https://api.typeform.com';

  public const PAGE_SIZE_MAX = 200;
  private const PAGE_SIZE_MIN = 1;
  private const PAGE_NUMBER_MIN = 1;

  private const QUERY_PARAM_SEARCH = 'search';
  private const QUERY_PARAM_PAGE_NUMBER = 'page';
  private const QUERY_PARAM_PAGE_SIZE = 'page_size';

  /** @readonly  */
  private static array $imageFormats = [
    self::IMAGE_FORMAT_BACKGROUND,
    self::IMAGE_FORMAT_CHOICE,
    self::IMAGE_FORMAT_IMAGE,
  ];
  /** @readonly  */
  private static array $imageSizes = [
    self::IMAGE_FORMAT_BACKGROUND => [
      self::IMAGE_SIZE_BACKGROUND_DEFAULT,
      self::IMAGE_SIZE_BACKGROUND_TABLET,
      self::IMAGE_SIZE_BACKGROUND_MOBILE,
      self::IMAGE_SIZE_BACKGROUND_THUMBNAIL,
    ],
    self::IMAGE_FORMAT_CHOICE => [
      self::IMAGE_SIZE_CHOICE_DEFAULT,
      self::IMAGE_SIZE_CHOICE_THUMBNAIL,
      self::IMAGE_SIZE_CHOICE_SUPERSIZE,
      self::IMAGE_SIZE_CHOICE_SUPERMOBILE,
      self::IMAGE_SIZE_CHOICE_SUPERSIZEFIT,
      self::IMAGE_SIZE_CHOICE_SUPERMOBILEFIT,
    ],
    self::IMAGE_FORMAT_IMAGE => [
      self::IMAGE_SIZE_IMAGE_DEFAULT,
      self::IMAGE_SIZE_IMAGE_MOBILE,
      self::IMAGE_SIZE_IMAGE_THUMBNAIL,
    ],
  ];

  /** @readonly */
  private string $accessToken;
  /** @readonly */
  private HttpClientInterface $httpClient;
  private int $defaultPageSize = 10;

  public function __construct(string $accessToken, ?HttpClientInterface $httpClient = null)
  {
    $this->accessToken = $accessToken;
    $this->httpClient = $httpClient ?? HttpClient::create();
  }

  public function setDefaultPageSize(int $defaultPageSize): void
  {
    self::validatePageSize($defaultPageSize);
    $this->defaultPageSize = $defaultPageSize;
  }

  /**
   * @psalm-param "GET"|"POST"|"DELETE"|"PATCH"|"PUT" $method
   */
  private function makeRequest(
    string $method,
    string $url,
    ?array $query = null,
    ?array $json = null,
    bool $loadJson = true,
  ): ResponseInterface {
    return $this->httpClient->request($method, $url, [
      'auth_bearer' => $this->accessToken,
      'headers' => [
        'Accept' => $loadJson ? 'application/json' : null,
      ],
      'query' => array_filter($query ?? [], static fn($value): bool => $value !== null),
      'json' => $json,
    ]);
  }

  /**
   * @psalm-param array<string,string|int|float|null>|null $queryString
   */
  private function get(string $endpoint, ?array $queryString = null): array
  {
    return $this->makeRequest('GET', self::URL_BASE . $endpoint, $queryString)->toArray();
  }

  private function post(string $endpoint, array $data): array
  {
    return $this->makeRequest('POST', self::URL_BASE . $endpoint, null, $data)->toArray();
  }

  private function delete(string $endpoint, ?array $queryData = null): void
  {
    $this->makeRequest('DELETE', self::URL_BASE . $endpoint, $queryData)->getContent();
  }

  private function patch(string $endpoint, array $data): void
  {
    $this->makeRequest('PATCH', self::URL_BASE . $endpoint, null, $data)->getContent();
  }

  /** @return array|string */
  private function put(string $endpoint, array $data, bool $returnResponse = false)
  {
    $response = $this->makeRequest('PUT', self::URL_BASE . $endpoint, null, $data);
    return $returnResponse ? $response->toArray() : $response->getContent();
  }

  /**
   * @psalm-template TModel of \AdamAveray\Typeform\Models\Model
   * @psalm-param Utils\Refs\SingleRef<TModel> $ref
   * @psalm-return TModel
   * @see loadCollectionRef
   */
  public function loadRef(Utils\Refs\SingleRef $ref): Models\Model
  {
    $data = $this->makeRequest('GET', $ref->href)->toArray();
    return $ref->instantiate($data);
  }

  /**
   * @param bool $loadMax Whether to attempt to load all referenced items, overriding the default page size
   * @psalm-template TModel of Models\Model
   * @psalm-param Utils\Refs\CollectionRef<TModel> $ref
   * @psalm-return list<TModel>
   * @see loadRef
   */
  public function loadCollectionRef(Utils\Refs\CollectionRef $ref, bool $loadMax = true): array
  {
    $pageSize = $loadMax ? min($ref->count, self::PAGE_SIZE_MAX) : $this->defaultPageSize;
    $data = $this->makeRequest('GET', $ref->href, [self::QUERY_PARAM_PAGE_SIZE => $pageSize])->toArray();
    return $ref->instantiateCollection($data);
  }

  /**
   * @link https://developer.typeform.com/create/reference/retrieve-your-own-user/
   */
  public function getCurrentUser(): Models\Users\User
  {
    return new Models\Users\User($this->get('/me'));
  }

  /**
   * @psalm-return Utils\PaginatedResponse<Models\Workspaces\WorkspaceStub>
   * @link https://developer.typeform.com/create/reference/retrieve-account-workspaces/
   */
  public function getAccountWorkspaces(
    string $accountId,
    ?string $search = null,
    ?int $page1 = null,
    ?int $pageSize = null,
  ): Utils\PaginatedResponse {
    self::validatePageNumber($page1);
    self::validatePageSize($pageSize);
    return Utils\PaginatedResponse::createForModel(
      Models\Workspaces\WorkspaceStub::class,
      $this->get(self::buildEndpoint('/accounts/%/workspaces', $accountId), [
        self::QUERY_PARAM_SEARCH => $search,
        self::QUERY_PARAM_PAGE_NUMBER => $page1,
        self::QUERY_PARAM_PAGE_SIZE => $pageSize ?? $this->defaultPageSize,
      ]),
    );
  }

  /**
   * @psalm-return Utils\PaginatedResponse<Models\Workspaces\WorkspaceStub>
   * @link https://developer.typeform.com/create/reference/retrieve-workspaces/
   */
  public function getWorkspaces(
    ?string $search = null,
    ?int $page1 = null,
    ?int $pageSize = null,
  ): Utils\PaginatedResponse {
    self::validatePageNumber($page1);
    self::validatePageSize($pageSize);
    return Utils\PaginatedResponse::createForModel(
      Models\Workspaces\WorkspaceStub::class,
      $this->get('/workspaces', [
        self::QUERY_PARAM_SEARCH => $search,
        self::QUERY_PARAM_PAGE_NUMBER => $page1,
        self::QUERY_PARAM_PAGE_SIZE => $pageSize ?? $this->defaultPageSize,
      ]),
    );
  }

  /**
   * @param string|Models\Workspaces\WorkspaceStub $workspace A workspace ID or WorkspaceStub instance
   * @link https://developer.typeform.com/create/reference/retrieve-workspace/
   */
  public function getWorkspace($workspace): Models\Workspaces\Workspace
  {
    $data = $this->get(
      self::buildEndpoint('/workspaces/%', self::getId($workspace, [Models\Workspaces\WorkspaceStub::class])),
    );
    return new Models\Workspaces\Workspace($data);
  }

  /**
   * @link https://developer.typeform.com/create/reference/create-workspace/
   */
  public function createWorkspace(string $name): Models\Workspaces\Workspace
  {
    $data = $this->post('/workspaces', ['name' => $name]);
    return new Models\Workspaces\Workspace($data);
  }

  /**
   * @param string|Models\Workspaces\WorkspaceStub $workspace A workspace ID or WorkspaceStub instance
   * @link https://developer.typeform.com/create/reference/delete-workspace/
   */
  public function deleteWorkspace(Models\Workspaces\WorkspaceStub|string $workspace): void
  {
    $this->delete(
      self::buildEndpoint('/workspaces/%', self::getId($workspace, [Models\Workspaces\WorkspaceStub::class])),
    );
  }

  /**
   * @param string|Models\Workspaces\WorkspaceStub $workspace A workspace ID or WorkspaceStub instance
   * @param list<Utils\Operation>|Utils\Operation $operations One or more operations to perform on the workspace
   * @link https://developer.typeform.com/create/reference/update-workspace/
   */
  public function updateWorkspace(
    Models\Workspaces\WorkspaceStub|string $workspace,
    Utils\Operation|array $operations,
  ): void {
    $this->patch(
      self::buildEndpoint('/workspaces/%', self::getId($workspace, [Models\Workspaces\WorkspaceStub::class])),
      self::formatOperations($operations),
    );
  }

  /**
   * @param string|Models\Workspaces\WorkspaceStub|null $workspace A workspace ID or WorkspaceStub instance
   * @psalm-return Utils\PaginatedResponse<Models\Forms\FormStub>
   * @link https://developer.typeform.com/create/reference/retrieve-forms/
   */
  public function getForms(
    Models\Workspaces\WorkspaceStub|string $workspace = null,
    ?string $search = null,
    ?int $page1 = null,
    ?int $pageSize = null,
  ): Utils\PaginatedResponse {
    self::validatePageNumber($page1);
    self::validatePageSize($pageSize);

    return Utils\PaginatedResponse::createForModel(
      Models\Forms\FormStub::class,
      $this->get('/forms', [
        'workspace_id' =>
          $workspace === null ? null : self::getId($workspace, [Models\Workspaces\WorkspaceStub::class]),
        self::QUERY_PARAM_SEARCH => $search,
        self::QUERY_PARAM_PAGE_NUMBER => $page1,
        self::QUERY_PARAM_PAGE_SIZE => $pageSize ?? $this->defaultPageSize,
      ]),
    );
  }

  /**
   * @param string|Models\Forms\FormStub $form A form ID or FormStub instance
   * @link https://developer.typeform.com/create/reference/retrieve-form/
   */
  public function getForm(Models\Forms\FormStub|string $form): Models\Forms\Form
  {
    $data = $this->get(self::buildEndpoint('/forms/%', self::getId($form, [Models\Forms\FormStub::class])));
    return new Models\Forms\Form($data);
  }

  /**
   * @link https://developer.typeform.com/create/reference/create-form/
   */
  public function createForm(array $data): Models\Forms\Form
  {
    $responseData = $this->post('/forms', $data);
    return new Models\Forms\Form($responseData);
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @link https://developer.typeform.com/create/reference/delete-form/
   */
  public function deleteForm(Models\Forms\Form|Models\Forms\FormStub|string $form): void
  {
    $this->delete(
      self::buildEndpoint('/forms/%', self::getId($form, [Models\Forms\FormStub::class, Models\Forms\Form::class])),
    );
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param list<Utils\Operation>|Utils\Operation $operations One or more operations to perform on the form
   * @psalm-type Op = Utils\Operation<Utils\Operation::TYPE_*, Models\Forms\Form::OPERATION_PATH_*, mixed>
   * @psalm-param list<Op>|Op $operations
   * @link https://developer.typeform.com/create/reference/update-form-patch/
   */
  public function updateForm(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Utils\Operation|array $operations,
  ): void {
    $this->patch(
      self::buildEndpoint('/forms/%', self::getId($form, [Models\Forms\FormStub::class, Models\Forms\Form::class])),
      self::formatOperations($operations),
    );
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @link https://developer.typeform.com/create/reference/update-form/
   */
  public function overwriteForm(Models\Forms\Form|Models\Forms\FormStub|string $form, array $data): void
  {
    $this->put(
      self::buildEndpoint('/forms/%', self::getId($form, [Models\Forms\FormStub::class, Models\Forms\Form::class])),
      $data,
    );
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @return list<string>
   * @psalm-return array<string, string|null>
   * @link https://developer.typeform.com/create/reference/retrieve-custom-form-messages/
   */
  public function getFormMessages(Models\Forms\Form|Models\Forms\FormStub|string $form): array
  {
    return $this->get(
      self::buildEndpoint(
        '/forms/%/messages',
        self::getId($form, [Models\Forms\FormStub::class, Models\Forms\Form::class]),
      ),
    );
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param list<string> $messages
   * @psalm-param array<string, string|null> $messages
   * @link https://developer.typeform.com/create/reference/update-custom-messages/
   */
  public function updateFormMessages(Models\Forms\Form|Models\Forms\FormStub|string $form, array $messages): void
  {
    $this->put(
      self::buildEndpoint(
        '/forms/%/messages',
        self::getId($form, [Models\Forms\FormStub::class, Models\Forms\Form::class]),
      ),
      $messages,
    );
  }

  /**
   * @return list<Models\Images\Image>
   * @link https://developer.typeform.com/create/reference/retrieve-images-collection/
   */
  public function getImages(): array
  {
    return array_map(
      static fn(array $image): Models\Images\Image => new Models\Images\Image($image),
      array_values($this->get('/images')),
    );
  }

  /**
   * @param string|Models\Images\Image $image An image ID or Image instance
   * @psalm-param null|self::IMAGE_FORMAT_* $format
   * @psalm-param null|self::IMAGE_SIZE_* $size
   * @link https://developer.typeform.com/create/reference/retrieve-image/
   * @link https://developer.typeform.com/create/reference/retrieve-background-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-choice-image-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-image-by-size/
   */
  public function getImage(
    Models\Images\Image|string $image,
    ?string $format = null,
    ?string $size = null,
  ): Models\Images\Image {
    $endpoint = self::buildImageEndpoint($image, $format, $size);
    $data = $this->get($endpoint);
    return new Models\Images\Image($data);
  }

  /**
   * @param string|Models\Images\Image $image An image ID or Image instance
   * @psalm-param null|self::IMAGE_FORMAT_* $format
   * @psalm-param null|self::IMAGE_SIZE_* $size
   * @link https://developer.typeform.com/create/reference/retrieve-image/
   * @link https://developer.typeform.com/create/reference/retrieve-background-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-choice-image-by-size/
   * @link https://developer.typeform.com/create/reference/retrieve-image-by-size/
   */
  public function getImageSource(
    Models\Images\Image|string $image,
    ?string $format = null,
    ?string $size = null,
  ): string {
    $endpoint = self::buildImageEndpoint($image, $format, $size);
    return $this->makeRequest('GET', self::URL_BASE . $endpoint, null, null, false)->getContent();
  }

  /**
   * @link https://developer.typeform.com/create/reference/create-image/
   */
  public function createImage(string $fileName, string $base64Source, string $url): array
  {
    return $this->post('/images', [
      'file_name' => $fileName,
      'image' => $base64Source,
      'url' => $url,
    ]);
  }

  /**
   * @param string|Models\Images\Image $image An image ID or Image instance
   * @link https://developer.typeform.com/create/reference/delete-image/
   */
  public function deleteImage(Models\Images\Image|string $image): void
  {
    $this->delete(self::buildEndpoint('/images/%', self::getId($image, [Models\Images\Image::class])));
  }

  /**
   * @psalm-return Utils\PaginatedResponse<Models\Themes\Theme>
   * @link https://developer.typeform.com/create/reference/retrieve-themes/
   */
  public function getThemes(?int $page1 = null, ?int $pageSize = null): Utils\PaginatedResponse
  {
    self::validatePageNumber($page1);
    self::validatePageSize($pageSize);

    return Utils\PaginatedResponse::createForModel(
      Models\Themes\Theme::class,
      $this->get('/themes', [
        self::QUERY_PARAM_PAGE_NUMBER => $page1,
        self::QUERY_PARAM_PAGE_SIZE => $pageSize ?? $this->defaultPageSize,
      ]),
    );
  }

  /**
   * @param string|Models\Themes\Theme $theme A theme ID or Theme instance
   * @link https://developer.typeform.com/create/reference/retrieve-theme/
   */
  public function getTheme(Models\Themes\Theme|string $theme): Models\Themes\Theme
  {
    $data = $this->get(self::buildEndpoint('/themes/%', self::getId($theme, [Models\Themes\Theme::class])));
    return new Models\Themes\Theme($data);
  }

  /**
   * @link https://developer.typeform.com/create/reference/create-theme/
   */
  public function createTheme(array $data): Models\Themes\Theme
  {
    $responseData = $this->post('/themes', $data);
    return new Models\Themes\Theme($responseData);
  }

  /**
   * @param string|Models\Themes\Theme $theme A theme ID or Theme instance
   * @link https://developer.typeform.com/create/reference/delete-theme/
   */
  public function deleteTheme(Models\Themes\Theme|string $theme): void
  {
    $this->delete(self::buildEndpoint('/themes/%', self::getId($theme, [Models\Themes\Theme::class])));
  }

  /**
   * @param string|Models\Themes\Theme $theme A theme ID or Theme instance
   * @link https://developer.typeform.com/create/reference/update-theme/
   */
  public function updateTheme(Models\Themes\Theme|string $theme, array $data): Models\Themes\Theme
  {
    /** @var array $responseData */
    $responseData = $this->put(
      self::buildEndpoint('/themes/%', self::getId($theme, [Models\Themes\Theme::class])),
      $data,
      true,
    );
    return new Models\Themes\Theme($responseData);
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @psalm-param array{
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
  ): Utils\PaginatedResponse {
    $query = self::formatQuery(
      $options,
      [
        'page_size' => null,
        'since' => null,
        'until' => null,
        'after' => null,
        'before' => null,
        'included_response_ids' => null,
        'excluded_response_ids' => null,
        'completed' => null,
        'sort' => null,
        'query' => null,
        'fields' => null,
        'answered_fields' => null,
      ],
      ['included_response_ids', 'excluded_response_ids', 'fields', 'answered_fields'],
    );
    $endpoint = self::buildEndpoint(
      '/forms/%/responses',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
    );
    return Utils\PaginatedResponse::createForModel(Models\Forms\Response::class, $this->get($endpoint, $query));
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @psalm-param list<string|Models\Forms\Response> $responses Response IDs or Response instances
   * @link https://developer.typeform.com/responses/reference/delete-responses/
   */
  public function deleteResponses(Models\Forms\Form|Models\Forms\FormStub|string $form, array $responses): void
  {
    $endpoint = self::buildEndpoint(
      '/forms/%/responses',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
    );
    $responses = array_map(
      static fn($response): string => self::getId($response, [Models\Forms\Response::class]),
      $responses,
    );
    $this->delete($endpoint, ['included_response_ids' => implode(',', $responses)]);
  }

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
  ): string {
    $endpoint = self::buildEndpoint(
      '/forms/%/responses/%/fields/%/files/%',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
      self::getId($response, [Models\Forms\Response::class]),
      self::getId($field, [Models\Forms\Field::class]),
      $filename,
    );
    return $this->makeRequest('GET', self::URL_BASE . $endpoint, null, null, false)->getContent();
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @link https://developer.typeform.com/responses/reference/retrieve-form-insights/
   */
  public function getFormInsights(Models\Forms\Form|Models\Forms\FormStub|string $form): Models\Forms\InsightsSummary
  {
    $endpoint = self::buildEndpoint(
      '/insights/%/summary',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
    );
    return new Models\Forms\InsightsSummary($this->get($endpoint));
  }

  /**
   * @param string|Models\Jobs\Job $job A job ID or Job instance
   * @link https://developer.typeform.com/responses/reference/rtbf-retrieve-job-status/
   */
  public function rtbfGetJobStatus(string $accountId, Models\Jobs\Job|string $job): Models\Jobs\Status
  {
    $endpoint = self::buildEndpoint('/rtbf/%/job/%', $accountId, self::getId($job, [Models\Jobs\Job::class]));
    return new Models\Jobs\Status($this->get($endpoint));
  }

  /**
   * @param list<string> $emails
   * @return list<string>
   * @link https://developer.typeform.com/responses/reference/rtbf-delete-responses/
   */
  public function rtbfDeleteResponses(string $accountId, array $emails): array
  {
    $request = $this->makeRequest('DELETE', self::buildEndpoint('/rtbf/%/responses', $accountId), null, $emails);
    /** @var list<string> */
    return $request->toArray();
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @return list<Models\Forms\Webhook>
   * @link https://developer.typeform.com/webhooks/reference/retrieve-webhooks/
   */
  public function getWebhooks(Models\Forms\Form|Models\Forms\FormStub|string $form): array
  {
    $endpoint = self::buildEndpoint(
      '/forms/%/webhooks',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
    );
    return array_map(
      static fn(array $item): Models\Forms\Webhook => new Models\Forms\Webhook($item),
      array_values($this->get($endpoint)['items']), // Docs don't show regular pagination values
    );
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param string|Models\Forms\Webhook $tagOrWebhook A webhook tag or Webhook instance
   * @link https://developer.typeform.com/webhooks/reference/retrieve-single-webhook/
   */
  public function getWebhook(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Models\Forms\Webhook|string $tagOrWebhook,
  ): Models\Forms\Webhook {
    $endpoint = self::buildEndpoint(
      '/forms/%/webhooks/%',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
      $tagOrWebhook instanceof Models\Forms\Webhook ? $tagOrWebhook->tag : $tagOrWebhook,
    );
    return new Models\Forms\Webhook($this->get($endpoint));
  }

  /**
   * @param string|Models\Forms\FormStub|Models\Forms\Form $form A form ID or FormStub|Form instance
   * @param string|Models\Forms\Webhook $tagOrWebhook A webhook tag or Webhook instance
   * @link https://developer.typeform.com/webhooks/reference/delete-webhook/
   */
  public function deleteWebhook(
    Models\Forms\Form|Models\Forms\FormStub|string $form,
    Models\Forms\Webhook|string $tagOrWebhook,
  ): void {
    $endpoint = self::buildEndpoint(
      '/forms/%/webhooks/%',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
      $tagOrWebhook instanceof Models\Forms\Webhook ? $tagOrWebhook->tag : $tagOrWebhook,
    );
    $this->delete($endpoint);
  }

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
  ): Models\Forms\Webhook {
    $endpoint = self::buildEndpoint(
      '/forms/%/webhooks/%',
      self::getId($form, [Models\Forms\Form::class, Models\Forms\FormStub::class]),
      $tagOrWebhook instanceof Models\Forms\Webhook ? $tagOrWebhook->tag : $tagOrWebhook,
    );
    return new Models\Forms\Webhook(
      (array) $this->put(
        $endpoint,
        [
          'enabled' => $enabled,
          'url' => $url,
          'secret' => $secret,
          'verify_ssl' => $verifySsl,
        ],
        true,
      ),
    );
  }

  /**
   * Ensures the given page number is within the valid bounds, throwing an exception if not
   *
   * @throws \InvalidArgumentException The page number is invalid
   */
  private static function validatePageNumber(?int $pageNumber1): void
  {
    if ($pageNumber1 === null) {
      return;
    }

    if ($pageNumber1 < self::PAGE_NUMBER_MIN) {
      throw new \InvalidArgumentException('Page number must be greater than or equal to ' . self::PAGE_NUMBER_MIN);
    }
  }

  /**
   * Ensures the given page size is within the valid bounds, throwing an exception if not
   *
   * @throws \InvalidArgumentException The page size is invalid
   */
  private static function validatePageSize(?int $pageSize): void
  {
    if ($pageSize === null) {
      return;
    }

    if ($pageSize < self::PAGE_SIZE_MIN) {
      throw new \InvalidArgumentException('Page size must be greater than or equal to ' . self::PAGE_SIZE_MIN);
    }
    if ($pageSize > self::PAGE_SIZE_MAX) {
      throw new \InvalidArgumentException('Page size must be less than or equal to ' . self::PAGE_SIZE_MAX);
    }
  }

  /**
   * Replaces the '%' character within a URL path pattern with a number of values, ensuring each value is URL safe
   *
   * @psalm-param string|int|float ...$parts
   */
  private static function buildEndpoint(string $pattern, ...$parts): string
  {
    $parts = array_map(static fn($value): string => urlencode((string) $value), $parts);

    $url = preg_replace_callback('~%~', static fn(): string => array_shift($parts), $pattern, -1, $count);
    if (\count($parts) !== $count) {
      throw new \BadMethodCallException('Invalid number of params provided');
    }
    return $url;
  }

  private static function buildImageEndpoint(
    Models\Images\Image|string $image,
    ?string $format = null,
    ?string $size = null,
  ): string {
    $imageId = self::getId($image, [Models\Images\Image::class]);
    if ($format === null) {
      return self::buildEndpoint('/images/%', $imageId);
    }

    if (!\in_array($format, self::$imageFormats, true)) {
      throw new \OutOfBoundsException('Invalid image format');
    }
    $validSizes = self::$imageSizes[$format];
    if ($size === null) {
      // Use default value
      $size = $validSizes[0];
    } elseif (!\in_array($size, $validSizes, false)) {
      throw new \OutOfBoundsException('Invalid image size');
    }
    return self::buildEndpoint('/images/%/%/%', $imageId, $format, $size);
  }

  /**
   * Converts a model or ID to an ID
   *
   * @psalm-param list<class-string<Models\Model>> $classNames
   */
  private static function getId(mixed $modelOrId, array $classNames): string
  {
    if (\is_string($modelOrId)) {
      return $modelOrId;
    }

    $isValidInstance = false;
    foreach ($classNames as $className) {
      if ($modelOrId instanceof $className) {
        $isValidInstance = true;
        break;
      }
    }
    if (!$isValidInstance) {
      throw new \InvalidArgumentException('Value must be ID or instance of ' . implode('|', $classNames));
    }

    /** @var Models\Model $modelOrId */
    return $modelOrId->id;
  }

  /**
   * @param list<Utils\Operation>|Utils\Operation $operations One or more operations to perform on the form
   * @return list<array{ op: Utils\Operation::TYPE_*, path: string, value: mixed }>
   * @psalm-pure
   */
  private static function formatOperations(Utils\Operation|array $operations): array
  {
    if (!\is_array($operations)) {
      $operations = [$operations];
    }

    return array_map(
      /** @psalm-pure */
      static fn(Utils\Operation $operation): array => $operation->formatForRequest(),
      $operations,
    );
  }

  private static function formatQuery(array $values, array $defaults, array $listKeys = []): array
  {
    $query = array_merge($defaults, $values);
    foreach ($listKeys as $listKey) {
      if (is_array($query[$listKey])) {
        $query[$listKey] = implode(',', $query[$listKey]);
      }
    }
    return $query;
  }
}
