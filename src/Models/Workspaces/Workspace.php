<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

use AdamAveray\Typeform\Utils\Refs;

/**
 * @psalm-import-type RawData from Member as MemberRawData
 * @psalm-import-type RawData from Refs\Ref as RefRawData
 * @psalm-import-type RawData from Refs\CollectionRef as CollectionRefRawData
 * @psalm-type RawData = array{
 *   id: string,
 *   name: string,
 *   account_id: string,
 *   shared: bool,
 *   default: bool,
 *   forms: CollectionRefRawData,
 *   self: RefRawData,
 *   members: array<array-key, MemberRawData>,
 * }
 * @extends WorkspaceStub<RawData>
 * @psalm-immutable
 */
final readonly class Workspace extends WorkspaceStub
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
