<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

/**
 * @psalm-immutable
 */
final class Workspace extends WorkspaceStub
{
  /** @var list<Member> */
  public array $members;

  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->members = array_map(static fn(array $member): Member => new Member($member), $data['members']);
  }
}
