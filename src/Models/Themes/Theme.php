<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Themes;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{
 *   id: string,
 *   font: string,
 *   name: string,
 *   has_transparent_button: bool,
 *   colors: array<"question"|"answer"|"button"|"background", string>,
 *   visibility: string,
 *   screens: array{ form_size: string, alignment: string },
 *   fields: array{ form_size: string, alignment: string },
 * }
 * @extends Model<RawData>
 * @psalm-immutable
 */
final class Theme extends Model
{
  public string $name;
  public string $font;
  public bool $hasTransparentButton;
  public string $visibility;
  /** @psalm-var array<"question"|"answer"|"button"|"background", string> */
  public array $colors;
  /** @var array{ form_size: string, alignment: string } */
  public array $screens;
  /** @var array{ form_size: string, alignment: string } */
  public array $fields;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->font = $data['font'];
    $this->name = $data['name'];
    $this->hasTransparentButton = $data['has_transparent_button'];
    $this->colors = $data['colors'];
    $this->visibility = $data['visibility'];
    $this->screens = $data['screens'];
    $this->fields = $data['fields'];
  }
}
