<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Utils\FormEmbed;

/**
 * @psalm-immutable
 */
trait EmbedTrait
{
  /**
   * @psalm-param FormEmbed::TYPE_* $type
   */
  public function getEmbed(string $type): FormEmbed
  {
    return new FormEmbed($this, $type);
  }
}
