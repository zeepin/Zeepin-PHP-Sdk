<?php

namespace zeepin\core\program;

use zeepin\crypto\PublicKey;

class ProgramInfo
{
  /** @var int */
  public $M;

  /** @var PublicKey[] */
  public $pubKeys;
}
