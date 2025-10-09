<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

/**
 * @psalm-type RawData = array{
 *   email: string,
 *   name: string,
 *   role: string,
 *   account_member_id: string,
 * }
 * @psalm-immutable
 */
final class Member
{
  public string $email;
  public string $name;
  public string $role;
  public string $accountMemberId;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    $this->email = $data['email'];
    $this->name = $data['name'];
    $this->role = $data['role'];
    $this->accountMemberId = $data['account_member_id'];
  }
}
