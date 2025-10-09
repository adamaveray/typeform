<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Images;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{
 *   id: string,
 *   src: string,
 *   file_name: string,
 *   width: int,
 *   height: int,
 *   media_type: string,
 *   has_alpha: bool,
 *   avg_color: string,
 * }
 * @extends Model<RawData>
 * @psalm-immutable
 */
final class Image extends Model
{
  public string $src;
  public string $fileName;
  public int $width;
  public int $height;
  public string $mediaType;
  public bool $hasAlpha;
  public string $avgColor;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->src = $data['src'];
    $this->fileName = $data['file_name'];
    $this->width = $data['width'];
    $this->height = $data['height'];
    $this->mediaType = $data['media_type'];
    $this->hasAlpha = $data['has_alpha'];
    $this->avgColor = $data['avg_color'];
  }
}
