<?php

namespace zeepin\smartcontract\abi;

use zeepin\common\ByteArray;

class Parameter
{
  /**
   * Value of ParameterType
   *
   * @var string
   */
  public $type;

  /** @var string|int|ByteArray */
  public $value;

  public function __construct(string $type, $value)
  {
    $this->type = $type;
    $this->value = $value;
  }

  public function getType() : string
  {
    return $this->type;
  }

  /**
   * @return any
   */
  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value) : bool
  {
    if ($value->type === $this->type && $value->value !== null) {
      $this->value = $value->value;
      return true;
    }
    return false;
  }

  public function fromJsonObj($obj) : self
  {
    $p = new self(0, null);
    $p->type = $obj->type;
    $p->value = $obj->value;
    return $p;
  }
}
