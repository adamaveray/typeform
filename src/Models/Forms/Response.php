<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-immutable
 */
final class Response extends Model
{
  public array $answers;
  public array $calculated;
  public array $hidden;
  public array $metadata;
  public array $variables;
  public \DateTimeImmutable $landedAt;
  public \DateTimeImmutable $submittedAt;
  public string $landingId;
  public string $responseId;

  public function __construct(array $data)
  {
    parent::__construct($data + ['id' => $data['token']]);
    $this->answers = $data['answers'] ?? [];
    $this->calculated = $data['calculated'];
    $this->hidden = $data['hidden'];
    $this->metadata = $data['metadata'];
    $this->variables = $data['variables'] ?? [];
    $this->landedAt = self::convertTimestamp($data['landed_at']);
    $this->submittedAt = self::convertTimestamp($data['submitted_at']);
    $this->landingId = $data['landing_id'];
    $this->responseId = $data['response_id'];
  }
}
