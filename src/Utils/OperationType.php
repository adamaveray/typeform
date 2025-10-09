<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

/** @internal */
enum OperationType: string
{
  case Add = 'add';
  case Remove = 'remove';
  case Replace = 'replace';
}
