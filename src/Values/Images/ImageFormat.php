<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Values\Images;

enum ImageFormat: string
{
  case Background = 'background';
  case Choice = 'choice';
  case Image = 'image';

  /**
   * @return class-string<Sizes\ImageSizeBackground | Sizes\ImageSizeChoice | Sizes\ImageSizeImage>
   */
  public function getSizeEnum(): string
  {
    switch ($this) {
      case self::Background:
        return Sizes\ImageSizeBackground::class;
      case self::Choice:
        return Sizes\ImageSizeChoice::class;
      case self::Image:
        return Sizes\ImageSizeImage::class;
    }
  }

  /**
   * @return non-empty-list<Sizes\ImageSizeBackground> | non-empty-list<Sizes\ImageSizeChoice> | non-empty-list<Sizes\ImageSizeImage>
   */
  public function getSizes(): array
  {
    switch ($this) {
      case self::Background:
        return Sizes\ImageSizeBackground::cases();
      case self::Choice:
        return Sizes\ImageSizeChoice::cases();
      case self::Image:
        return Sizes\ImageSizeImage::cases();
    }
  }
}
