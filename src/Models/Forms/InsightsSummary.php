<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

/**
 * @psalm-type RawData = array{
 *   fields: array,
 *   form: array,
 * }
 * @psalm-immutable
 */
final readonly class InsightsSummary
{
  public array $fields;
  public array $form;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    $this->fields = $data['fields'];
    $this->form = $data['form'];
  }
}
