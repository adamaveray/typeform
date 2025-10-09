<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Users;

use AdamAveray\Typeform\Models\Model;

/**
 * @psalm-type RawData = array{
 *   user_id: string,
 *   alias: string,
 *   email: string,
 *   language: string,
 * }
 * @extends Model<RawData & array{ id: string }>
 * @psalm-immutable
 */
final class User extends Model
{
  public string $alias;
  public string $email;
  public string $language;

  /**
   * @param RawData $data
   * @psalm-suppress ImplementedParamTypeMismatch Alternate key ID used.
   */
  public function __construct(array $data)
  {
    $data['id'] = $data['user_id'];
    parent::__construct($data);
    $this->alias = $data['alias'];
    $this->email = $data['email'];
    $this->language = $data['language'];
  }
}
