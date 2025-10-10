<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{
 *   id: string,
 *   enabled: bool,
 *   form_id: string,
 *   tag: string,
 *   url: string,
 *   verify_ssl: bool,
 *   secret?: string,
 *   created_at: string,
 *   updated_at: string,
 * }
 * @extends Model<RawData>
 * @psalm-immutable
 */
readonly class Webhook extends Model
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

  /**
   * @param RawData $data
   */
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
