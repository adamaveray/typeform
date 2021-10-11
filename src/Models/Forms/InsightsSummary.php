<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

/**
 * @psalm-immutable
 */
class InsightsSummary
{
  public array $fields;
  public array $form;

  public function __construct(array $data)
  {
    $this->fields = $data['fields'];
    $this->form = $data['form'];
  }
}
