<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{
 *   id: string,
 *   ref: string,
 *   title: string,
 *   properties: array,
 * }
 * @extends Model<RawData>
 * @psalm-immutable
 */
final readonly class Screen extends Model
{
  public string $ref;
  public string $title;
  public array $properties;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->ref = $data['ref'];
    $this->title = $data['title'];
    $this->properties = $data['properties'];
  }
}
