<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

/**
 * @psalm-type RawData = array{
 *   id: string,
 *   name: string,
 *   account_id: string,
 *   shared: bool,
 *   default: bool,
 *   forms: array,
 *   self: array,
 *   members: array<array-key, array>,
 * }
 * @extends WorkspaceStub<RawData>
 * @psalm-immutable
 */
final class Workspace extends WorkspaceStub
{
  /** @var list<Member> */
  public array $members;

  /**
   * @param RawData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->members = array_map(static fn(array $member): Member => new Member($member), array_values($data['members']));
  }
}
