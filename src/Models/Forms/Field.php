<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{
 *   id: string,
 *   ref: string,
 *   title: string,
 *   type: string,
 *   properties: array,
 *   validations: array,
 * }
 * @extends Model<RawData>
 * @psalm-immutable
 */
final readonly class Field extends Model
{
  public string $ref;
  public string $title;
  public string $type;
  public array $properties;
  public array $validations;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->ref = $data['ref'];
    $this->title = $data['title'];
    $this->type = $data['type'];
    $this->properties = $data['properties'];
    $this->validations = $data['validations'] ?? [];
  }
}
