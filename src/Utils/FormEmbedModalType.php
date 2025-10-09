<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

enum FormEmbedModalType: string
{
  case Popup = 'popup';
  case Slider = 'slider';
  case Sidetab = 'sidetab';
  case Popover = 'popover';
}
