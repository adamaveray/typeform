<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

/**
 * @psalm-immutable
 */
final class Member
{
  public string $email;
  public string $name;
  public string $role;
  public string $accountMemberId;

  public function __construct(array $data)
  {
    $this->email = $data['email'];
    $this->name = $data['name'];
    $this->role = $data['role'];
    $this->accountMemberId = $data['account_member_id'];
  }
}
