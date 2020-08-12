<?php

namespace zeepin\core\payload;

use zeepin\core\scripts\ScriptReader;

abstract class Payload
{
  abstract function serialize() : string;
  abstract function deserialize(ScriptReader $ss);
}
