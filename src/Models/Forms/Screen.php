<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-immutable
 */
final class Screen extends Model
{
  public string $ref;
  public string $title;
  public array $properties;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->ref = $data['ref'];
    $this->title = $data['title'];
    $this->properties = $data['properties'];
  }
}
