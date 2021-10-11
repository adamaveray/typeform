<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Forms;

use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Models\Themes\Theme;
use AdamAveray\Typeform\Utils\Refs;

/**
 * @psalm-immutable
 */
final class FormStub extends Model
{
  use EmbedTrait;

  public string $title;
  public \DateTimeImmutable $createdAt;
  public \DateTimeImmutable $lastUpdatedAt;
  /** @psalm-var Refs\SingleRef<Form> */
  public Refs\SingleRef $self;
  /** @psalm-var Refs\SingleRef<Theme> */
  public Refs\SingleRef $theme;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->title = $data['title'];
    $this->createdAt = self::convertTimestamp($data['created_at']);
    $this->lastUpdatedAt = self::convertTimestamp($data['last_updated_at']);
    $this->self = Form::ref($data['self']);
    $this->theme = Theme::ref($data['theme']);
  }
}
