<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Values\Images\Sizes;

enum ImageSizeBackground: string
{
  case Default = 'default';
  case Tablet = 'tablet';
  case Mobile = 'mobile';
  case Thumbnail = 'thumbnail';
}
