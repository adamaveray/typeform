<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-immutable
 */
final class Field extends Model
{
  public string $ref;
  public string $title;
  public string $type;
  public array $properties;
  public array $validations;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->id = $data['id'];
    $this->ref = $data['ref'];
    $this->title = $data['title'];
    $this->type = $data['type'];
    $this->properties = $data['properties'];
    $this->validations = isset($data['validations']) ? $data['validations'] : [];
  }
}
