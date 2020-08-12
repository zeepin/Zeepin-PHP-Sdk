<?php

namespace zeepin\sdk;

use zeepin\crypto\ScryptParams;
use zeepin\sdk\Account;
use zeepin\common\Util;
use \JsonSerializable;

class Wallet implements JsonSerializable
{
  /** @var string */
  public $name;

  /** @var string */
  public $defaultZptid;

  /** @var string */
  public $defaultAccountAddress;

  /** @var string */
  public $createTime;

  /** @var string */
  public $version;

  /** @var ScryptParams */
  public $scrypt;

  /** @var Identity[] */
  public $identities = [];

  /** @var Account[] */
  public $accounts = [];

  /** @var mixed */
  public $extra;

  /** @var string */
  private $file;

  public function addAccount(Account $acc)
  {
    foreach ($this->accounts as $a) {
      if ($a->address->toBase58() === $acc->address->toBase58()) return;
    }
    $this->accounts[] = $acc;
  }

  public function deleteAccount(Account $acc)
  {
    $as = [];
    foreach ($this->accounts as $a) {
      if ($a->address->toBase58() === $acc->address->toBase58()) continue;
      $as[] = $a;
    }
    $this->accounts = $as;
  }

  public static function create(string $name) : self
  {
    $w = new self();
    $w->name = $name;
    $w->create = (new \DateTime())->format(Util::JS_ISO);
    $w->version = '1.0';
    $w->scrypt = new ScryptParams();
    $w->scrypt->n = Constant::$DEFAULT_SCRYPT->cost;
    $w->scrypt->r = Constant::$DEFAULT_SCRYPT->blockSize;
    $w->scrypt->p = Constant::$DEFAULT_SCRYPT->parallel;
    $w->scrypt->dkLen = Constant::$DEFAULT_SCRYPT->size;
    return $w;
  }

  public static function fromJson(string $json) : self
  {
    return self::fromJsonObj(json_decode($json));
  }

  public static function fromFile(string $file) : self
  {
    $w = self::fromJson(file_get_contents($file));
    $w->file = $file;
    return $w;
  }

  public function save(string $file)
  {
    $this->file = $file;
    file_put_contents($this->file, json_encode($this));
  }

  public static function fromJsonObj($obj) : self
  {
    $w = new self();
    $w->name = $obj->name;
    $w->defaultAccountAddress = $obj->defaultAccountAddress;
    $w->createTime = $obj->createTime;
    $w->version = $obj->version;
    $w->scrypt = ScryptParams::fromJsonObj($obj->scrypt);
    if ($obj->accounts) {
      $w->accounts = array_map(function ($a) {
        return Account::fromJsonObj($a);
      }, $obj->accounts);
    }
    $w->extra = $obj->extra;
    return $w;
  }

  public function jsonSerialize()
  {
    $identities = array_map(function ($id) {
      return $id->jsonSerialize();
    }, $this->identities);

    $accounts = array_map(function ($a) {
      return $a->jsonSerialize();
    }, $this->accounts);

    return [
      'name' => $this->name,
      'defaultAccountAddress' => $this->defaultAccountAddress,
      'createTime' => $this->createTime,
      'version' => $this->version,
      'scrypt' => $this->scrypt,
      'identities' => $identities,
      'accounts' => $accounts,
      'extra' => null
    ];
  }
}
