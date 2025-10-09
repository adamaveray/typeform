<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Values\Images\Sizes;

enum ImageSizeChoice: string
{
  case Default = 'default';
  case Thumbnail = 'thumbnail';
  case Supersize = 'supersize';
  case Supermobile = 'supermobile';
  case Supersizefit = 'supersizefit';
  case Supermobilefit = 'supermobilefit';
}
