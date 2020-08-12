<?php

namespace zeepin\core\transaction;

use zeepin\crypto\PublicKey;
use zeepin\crypto\PrivateKey;
use zeepin\crypto\SignatureScheme;
use zeepin\common\ByteArray;
use zeepin\smartcontract\abi\NativeVmParamsBuilder;
use zeepin\core\Signable;
use zeepin\core\program\ProgramBuilder;
use zeepin\core\program\ProgramReader;

class TxSignature
{
  /** @var PublicKey[] */
  public $pubKeys = [];

  /** @var int */
  public $M;

  /** @var string[] */
  public $sigData = [];

  public static function create(Signable $data, PrivateKey $pri, ? SignatureScheme $scheme = null) : self
  {
    $sig = new self();
    $sig->pubKeys = [$pri->getPublicKey()];
    $sig->sigData = [$pri->sign(ByteArray::fromHex($data->getSignContent()), $scheme)->toHex()];
    return $sig;
  }

  public function serialize() : string
  {
    $invocationScript = ProgramBuilder::programFromParams($this->sigData);
    $pubKeysCnt = count($this->pubKeys);
    if ($pubKeysCnt === 0) {
      throw new \InvalidArgumentException('No pubKeys in sig');
    }

    $verificationScript;
    if ($pubKeysCnt === 1) {
      $verificationScript = ProgramBuilder::programFromPubKey($this->pubKeys[0]);
    } else {
      $verificationScript = ProgramBuilder::programFromMultiPubKey($this->pubKeys, $this->M);
    }
    $b = new NativeVmParamsBuilder();
    $b->pushVarBytes($invocationScript);
    $b->pushVarBytes($verificationScript);
    return $b->toHex();
  }

  public static function deserialize(ProgramReader $reader) : self
  {
    $sig = new self();
    $sig->sigData = $reader->readParams();

    $info = $reader->readInfo();
    $sig->M = $info->M;
    $sig->pubKeys = $info->pubKeys;

    return $sig;
  }
}
