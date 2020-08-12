<?php

namespace zeepin\core\transaction;

use function Webmozart\Assert\Tests\StaticAnalysis\length;
use zeepin\crypto\Address;
use zeepin\common\ByteArray;
use zeepin\sdk\Constant;
use zeepin\core\payload\InvokeCode;
use zeepin\common\Fixed64;
use zeepin\crypto\PrivateKey;
use zeepin\crypto\SignatureScheme;
use zeepin\core\scripts\ScriptBuilder;
use zeepin\core\scripts\Opcode;
use zeepin\core\payload\DeployCode;
use zeepin\smartcontract\abi\Parameter;
use zeepin\smartcontract\abi\AbiFunction;
use zeepin\smartcontract\abi\NativeVmParamsBuilder;

class TransactionBuilder
{

  public function signTransaction(Transaction $tx, PrivateKey $prikey, ? SignatureScheme $scheme = null)
  {
    $sig = TxSignature::create($tx, $prikey, $scheme);
    $tx->sigs = [$sig];
  }

  public function addSig(Transaction $tx, PrivateKey $prikey, ? SignatureScheme $scheme = null)
  {
    $sig = TxSignature::create($tx, $prikey, $scheme);
    $tx->sigs[] = $sig;
  }

  /**
   * Creates transaction to invoke native contract
   *
   * @param string $fnName Function name of contract to call
   * @param string $params Parameters serialized in hex string
   * @param Address $contractAddr Address of contract
   * @param string $gasPrice Gas price
   * @param string $gasLimit Gas limit
   * @param Address $payer Address to pay for transaction gas
   * @return Transaction|Transfer
   */
  public function makeNativeContractTx(
    string $fnName,
    string $params,
    Address $contractAddr,
    string $gasPrice = '',
    string $gasLimit = '',
    ? Address $payer = null
  ) : Transaction {
    $builder = new ScriptBuilder();
    $builder->pushArray(ByteArray::fromHex($params));
    $builder->pushHexString(ByteArray::fromBinary($fnName)->toHex());
    $builder->pushHexString($contractAddr->serialize());
    $builder->pushNum(0);
    $builder->pushInt(Opcode::SYSCALL);
    $builder->pushHexString(ByteArray::fromBinary(Constant::$NATIVE_INVOKE_NAME)->toHex());
    $payload = new InvokeCode();
    $payload->code = $builder->toHex();

    $tx;
    if ($fnName === 'transfer' || $fnName === 'transferFrom') {
      $tx = new Transfer();
    } else {
      $tx = new Transaction();
    }

    $tx->type = TxType::Invoke;
    $tx->payload = $payload;
    if ($gasLimit !== '') {
      $tx->gasLimit = new Fixed64((int)$gasLimit);
    }
    if ($gasPrice !== '') {
      $tx->gasPrice = new Fixed64((int)$gasPrice);
    }
    if ($payer) {
      $tx->payer = $payer;
    }
    return $tx;
  }

  public function makeDeployCodeTransaction(
    string $code,
    string $name,
    string $codeVersion,
    string $author,
    string $email,
    string $desc,
    bool $needStorage,
    string $gasPrice,
    string $gasLimit,
    ? Address $payer
  ) : Transaction {
    $dc = new DeployCode();
    $dc->author = $author;
    $dc->code = $code;
    $dc->name = $name;
    $dc->version = $codeVersion;
    $dc->email = $email;
    $dc->needStorage = $needStorage;
    $dc->description = $desc;

    $tx = new Transaction();
    $tx->version = 0x00;
    $tx->payload = $dc;
    $tx->type = TxType::Deploy;

    $tx->gasLimit = new Fixed64($gasLimit);
    $tx->gasPrice = new Fixed64($gasPrice);
    if ($payer) {
      $tx->payer = $payer;
    }

    return $tx;
  }

  /**
   * Creates transaction to invoke smart contract
   *
   * @param string $fnName
   * @param Parameter[]|string $params
   * @param Address $contractAddr
   * @param string $gasPrice
   * @param string $gasLimit
   * @param Address $payer
   * @return void
   */
  public function makeInvokeTransaction(
    string $fnName,
    $params,
    Address $contractAddr,
    string $gasPrice = '1',
    string $gasLimit = '20000',
    ? Address $payer = null
  ) : Transaction {


    $invokeCode = self::BuildWasmVMInvokeCode($fnName, $contractAddr, $params);
    $tx = new Transaction();
    $tx->type = TxType::Invoke;

    $code = new ByteArray();
    $code->pushArray(ByteArray::fromBytes($invokeCode));

    $payload = new InvokeCode();
    $payload->code =$code->toHex();


    $tx->payload = $payload;
    $tx->txAttributes = 0x01;

    if ($gasLimit) {
      $tx->gasLimit = new Fixed64($gasLimit);
    }

    if ($gasPrice) {
      $tx->gasPrice = new Fixed64($gasPrice);
    }

    if ($payer) {
      $tx->payer = $payer;
    }

    return $tx;
  }

  public function BuildWasmVMInvokeCode($fnName,  Address $contractAddr,$params){

      $conaddr = self::BuildWasmVMContractAddrParam($contractAddr->value);
      $args = self::BuildWasmVMParam($params);
      $bytes1[] = ord('1');
      $bytes2[] =  array((strlen($fnName)));
      $bytes3 = self::getBytes($fnName);
      $bytes4[] = array(strlen($args));
      $bytes5 = self::getBytes($args);
      $bytes = array_merge($bytes1, $conaddr, $bytes2[0], $bytes3, $bytes4[0], $bytes5);
      return $bytes;
  }

  public function BuildWasmVMParam( $params) : string{
    $paramsLen = count($params);
    $paramsArray=[];
    for($x = 0; $x < $paramsLen; $x++){
        if(is_string($params[$x])){
            $paramsMapArray=["type"=>"string", "value"=>$params[$x]];
            array_push($paramsArray, $paramsMapArray);
        }else if(is_int($params[$x])){
            $paramsMapArray=["type"=>"int", "value"=>$params[$x]];
            array_push($paramsArray, $paramsMapArray);
        }else {
            return '';
        }
    }
    $paramsMap = ["Params"=>$paramsArray];
    $paramsJson = json_encode($paramsMap);
    var_dump($paramsJson);
    return $paramsJson;
  }

  public function BuildWasmVMContractAddrParam(string $contractAddr){
      $hexStr = self::hexToStr($contractAddr);
      $conaddr = self::getBytes($hexStr);
      $conaddr = array_reverse($conaddr);
      return $conaddr;
  }

    public static function getBytes($string) {
        $bytes = array();
        var_dump($string);
        for($i = 0; $i < strlen($string); $i++){
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    function hexToStr($hex){
        $str="";
        for($i=0;$i<strlen($hex)-1;$i+=2)
            $str.=chr(hexdec($hex[$i].$hex[$i+1]));
        return $str;
    }

    public static function toStr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }

        return $str;
    }

}
