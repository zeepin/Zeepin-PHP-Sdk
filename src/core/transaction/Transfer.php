<?php

namespace zeepin\core\transaction;

use zeepin\crypto\Address;

class Transfer extends Transaction
{
  /** @var int|string */
  public $amount;

  /** @var string */
  public $tokenType;

  /** @var Address */
  public $from;

  /** @var Address */
  public $to;

  /** @var string */
  public $method;
}
