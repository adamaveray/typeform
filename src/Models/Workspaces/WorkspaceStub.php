<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

use AdamAveray\Typeform\Models\Forms\FormStub;
use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Utils\Refs;

/**
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
 * } & array<string, mixed>
 * @template TWorkplaceData of RawData
 * @extends Model<TWorkplaceData>
 * @psalm-immutable
 */
readonly class WorkspaceStub extends Model
{
  public string $name;
  public string $accountId;
  public bool $shared;
  public bool $default;
  /** @var Refs\CollectionRef<FormStub> */
  public Refs\CollectionRef $forms;
  /** @var Refs\SingleRef<Workspace> */
  public Refs\SingleRef $self;

  /**
   * @param TWorkplaceData $data
   */
  public function __construct(array $data)
  {
    parent::__construct($data);
    $this->name = $data['name'];
    $this->accountId = $data['account_id'];
    $this->shared = $data['shared'];
    $this->default = $data['default'];
    $this->forms = FormStub::collectionRef($data['forms']);
    $this->self = Workspace::ref($data['self']);
  }
}
