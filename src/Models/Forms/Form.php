<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Models\Themes\Theme;
use AdamAveray\Typeform\Models\Workspaces\Workspace;
use AdamAveray\Typeform\Utils\Refs;

/**
 * @psalm-import-type RawData from Screen as ScreenRawData
 * @psalm-import-type RawData from Field as FieldRawData
 * @psalm-import-type RawData from Refs\Ref as RefRawData
 * @psalm-type RawData = array{
 *   id: string,
 *   title: string,
 *   created_at?: string,
 *   last_updated_at?: string,
 *   type: string,
 *   workspace: RefRawData,
 *   theme: RefRawData,
 *   settings: array,
 *   thankyou_screens: array<array-key, ScreenRawData>,
 *   welcome_screens: array<array-key, ScreenRawData>,
 *   fields: array<array-key, FieldRawData>,
 *   _links: array,
 * }
 * @extends Model<RawData>
 * @psalm-immutable
 */
final readonly class Form extends Model
{
  use EmbedTrait;

  public const EMBED_LIB_URL = 'https://embed.typeform.com/next/embed.js';

  public const OPERATION_PATH_SETTINGS_FACEBOOK_PIXEL = '/settings/facebook_pixel';
  public const OPERATION_PATH_SETTINGS_GOOGLE_ANALYTICS = '/settings/google_analytics';
  public const OPERATION_PATH_SETTINGS_GOOGLE_TAG_MANAGER = '/settings/google_tag_manager';
  public const OPERATION_PATH_SETTINGS_IS_PUBLIC = '/settings/is_public';
  public const OPERATION_PATH_SETTINGS_META = '/settings/meta';
  public const OPERATION_PATH_CUI_SETTINGS = '/cui_settings';
  public const OPERATION_PATH_THEME = '/theme';
  public const OPERATION_PATH_TITLE = '/title';
  public const OPERATION_PATH_WORKSPACE = '/workspace';

  public string $title;
  public ?\DateTimeImmutable $createdAt;
  public ?\DateTimeImmutable $lastUpdatedAt;
  public string $type;
  /** @var Refs\SingleRef<Workspace> */
  public Refs\SingleRef $workspace;
  /** @var Refs\SingleRef<Theme> */
  public Refs\SingleRef $theme;
  public array $settings;
  /** @var list<Screen> */
  public array $thankyouScreens;
  /** @var list<Screen> */
  public array $welcomeScreens;
  /** @var list<Field> */
  public array $fields;
  public array $links;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->title = $data['title'];
    $this->createdAt = isset($data['created_at']) ? self::convertTimestamp($data['created_at']) : null;
    $this->lastUpdatedAt = isset($data['last_updated_at']) ? self::convertTimestamp($data['last_updated_at']) : null;
    $this->type = $data['type'];
    $this->workspace = Workspace::ref($data['workspace']);
    $this->theme = Theme::ref($data['theme']);
    $this->settings = $data['settings'];
    $this->thankyouScreens = isset($data['thankyou_screens'])
      ? array_map(static fn(array $screen): Screen => new Screen($screen), array_values($data['thankyou_screens']))
      : [];
    $this->welcomeScreens = isset($data['welcome_screens'])
      ? array_map(static fn(array $screen): Screen => new Screen($screen), array_values($data['welcome_screens']))
      : [];
    $this->fields = array_map(static fn(array $field): Field => new Field($field), array_values($data['fields']));
    $this->links = $data['_links'];
  }
}
