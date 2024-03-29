<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Workspaces;

use AdamAveray\Typeform\Models\Forms\FormStub;
use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Utils\Refs;

/**
 * @psalm-immutable
 */
class WorkspaceStub extends Model
{
  public string $name;
  public string $accountId;
  public bool $shared;
  public bool $default;
  /** @psalm-var Refs\CollectionRef<FormStub> */
  public Refs\CollectionRef $forms;
  /** @psalm-var Refs\SingleRef<Workspace> */
  public Refs\SingleRef $self;

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
