<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-immutable
 */
class Webhook extends Model
{
  public bool $enabled;
  public string $formId;
  public string $id;
  public string $tag;
  public string $url;
  public bool $verifySsl;
  public ?string $secret;
  public \DateTimeImmutable $createdAt;
  public \DateTimeImmutable $updatedAt;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->enabled = $data['enabled'];
    $this->formId = $data['form_id'];
    $this->id = $data['id'];
    $this->tag = $data['tag'];
    $this->url = $data['url'];
    $this->verifySsl = $data['verify_ssl'];
    $this->secret = $data['secret'] ?? null;
    $this->createdAt = self::convertTimestamp($data['created_at']);
    $this->updatedAt = self::convertTimestamp($data['updated_at']);
  }
}
