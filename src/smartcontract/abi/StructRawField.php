<?php

namespace zeepin\smartcontract\abi;

use zeepin\smartcontract\abi\ParameterTypeVal;
use zeepin\common\ByteArray;

class StructRawField
{
  /** @var ParameterTypeVal */
  public $type;

  /** @var ByteArray */
  public $bytes;

  public function __construct(int $type, ByteArray $bytes)
  {
    $this->type = $type;
    $this->bytes = $bytes;
  }
}
