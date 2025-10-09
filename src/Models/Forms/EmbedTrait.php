<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Utils\FormEmbed;
use AdamAveray\Typeform\Utils\FormEmbedType;

/**
 * @psalm-immutable
 */
trait EmbedTrait
{
  public function getEmbed(FormEmbedType|string $type): FormEmbed
  {
    return new FormEmbed($this, $type);
  }
}
