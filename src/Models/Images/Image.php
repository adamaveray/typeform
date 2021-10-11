<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Images;

use AdamAveray\Typeform\Models\Model;

/**
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
