<?php

namespace zeepin\core\payload;

use zeepin\core\scripts\ScriptBuilder;
use zeepin\common\ByteArray;
use zeepin\core\scripts\ScriptReader;

class InvokeCode extends Payload
{
  /**
   * Hex encoded string
   *
   * @var string
   */
  public $code;

  public function serialize() : string
  {
    $builder = new ScriptBuilder();
    return $builder->pushVarBytes(ByteArray::fromHex($this->code))->toHex();
  }

  public function deserialize(ScriptReader $r)
  {
    $this->code = $r->readVarBytes()->toHex();
  }
}
