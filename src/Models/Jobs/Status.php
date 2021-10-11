<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Models\Jobs;

/**
 * @psalm-immutable
 */
class Status
{
  public string $accountId;
  public string $status;
  public string $token;

  public function __construct(array $data)
  {
    $this->accountId = $data['accountID']; // (Inconsistent casing via docs)
    $this->status = $data['status'];
    $this->token = $data['token'];
  }
}
