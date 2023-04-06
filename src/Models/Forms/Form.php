<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Models\Themes\Theme;
use AdamAveray\Typeform\Models\Workspaces\Workspace;
use AdamAveray\Typeform\Utils\Refs;

/**
 * @psalm-immutable
 */
final class Form extends Model
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
  public string $type;
  /** @psalm-var Refs\SingleRef<Workspace> */
  public Refs\SingleRef $workspace;
  /** @psalm-var Refs\SingleRef<Theme> */
  public Refs\SingleRef $theme;
  public array $settings;
  /** @var list<Screen> */
  public array $thankyouScreens;
  /** @var array<empty,Screen> */
  public array $welcomeScreens;
  /** @var list<Field> */
  public array $fields;
  public array $links;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->title = $data['title'];
    $this->type = $data['type'];
    $this->workspace = Workspace::ref($data['workspace']);
    $this->theme = Theme::ref($data['theme']);
    $this->settings = $data['settings'];
    $this->thankyouScreens = array_map(
      static fn(array $screen): Screen => new Screen($screen),
      $data['thankyou_screens'],
    );
    $this->welcomeScreens = array_map(
      static fn(array $screen): Screen => new Screen($screen),
      $data['welcome_screens'] ?? [],
    );
    $this->fields = array_map(static fn(array $field): Field => new Field($field), $data['fields']);
    $this->links = $data['_links'];
  }
}
