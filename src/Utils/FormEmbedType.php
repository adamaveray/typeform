<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

enum FormEmbedType: string
{
  case Inline = 'inline';
  case Modal = 'modal';
}
