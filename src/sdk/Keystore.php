<?php

namespace zeepin\sdk;

use zeepin\crypto\ScryptParams;
use zeepin\crypto\KeyParameters;
use zeepin\crypto\Address;
use \JsonSerializable;

/**
 * see https://github.com/zeepin/documentation/blob/d969cf1abe18d17098c2c2db5dfeafaca65228b2/walletDevDocs/Zeepin_wallet_dev_ts_sdk_zh.md#25-%E5%AF%BC%E5%85%A5%E5%AF%BC%E5%87%BA-keystore
 */
class Keystore implements JsonSerializable
{
  /** @var string */
  public $label;

  /** @var string */
  public $algorithm;

  /** @var ScryptParams */
  public $scrypt;

  /** @var string */
  public $key;

  /** @var string */
  public $salt;

  /** @var Address */
  public $address;

  /** @var KeyParameters */
  public $parameters;

  public static function fromJson(string $json) : self
  {
    $obj = json_decode($json);
    $ret = new self();
//    $ret->type = $obj->type;
    $ret->label = $obj->label;
    $ret->algorithm = $obj->algorithm;
    $ret->scrypt = ScryptParams::fromJsonObj($obj->scrypt);
    $ret->key = $obj->key;
    $ret->salt = $obj->salt;
    $ret->address = new Address($obj->address);
    $ret->parameters = KeyParameters::fromJsonObj($obj->parameters);
    return $ret;
  }

  public function jsonSerialize()
  {
    return [
//      'type' => $this->type,
      'label' => $this->label,
      'algorithm' => $this->algorithm,
      'scrypt' => $this->scrypt->jsonSerialize(),
      'key' => $this->key,
      'salt' => $this->salt,
      'address' => $this->address->toBase58(),
      'parameters' => $this->parameters->jsonSerialize()
    ];
  }
}
