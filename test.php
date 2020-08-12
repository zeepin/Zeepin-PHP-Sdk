<?php

require_once __DIR__ . '/vendor/autoload.php';

use zeepin\network\JsonRpcResult;
use zeepin\sdk\Account;
use zeepin\sdk\Keystore;
use zeepin\crypto\KeyType;
use zeepin\crypto\KeyParameters;
use zeepin\crypto\PrivateKey;
use zeepin\crypto\CurveLabel;
use zeepin\common\ByteArray;
use zeepin\crypto\Address;
use zeepin\smartcontract\wasmvm\WasmAssetTxBuilder;
use zeepin\network\JsonRpc;
use zeepin\core\transaction\TransactionBuilder;
use zeepin\smartcontract\nativevm\ZptAssetTxBuilder;


// 用keystore密码创建账户
//$acc = Account::create('123456');
//var_dump($acc);
//$encPriKey = $acc->encryptedKey;
//$accDataStr = Keystore::fromJson($acc);
//echo('   ===============     ');

// 导出私钥
//var_dump($acc->exportPrivateKey('123456')->key->toHex());
//var_dump($acc->exportKeystore()->jsonSerialize());
//$accDataStr = json_encode($acc);
//var_dump($accDataStr);
//
//var_dump($acc->exportKeystore());

//// 使用默认的参数创建私钥
//$prikey = PrivateKey::random();
//var_dump($prikey->);


//
//// 使用指定的参数创建私钥
//$key = PrivateKey::random(KeyType::$Sm2, new KeyParameters(CurveLabel::$Sm2P256v1));
//$accDataStr = json_encode($key);
//var_dump($accDataStr);



// 直接导入私钥
//$prikey = new PrivateKey(
//    ByteArray::fromHex('2cf804f021d94c33a3a288d6fc0d74f19854f6ef01de20f3ad8b19166b221d90')
//);
//var_dump($prikey);
//$acc1 = Account::create('11', $prikey);
//var_dump($acc1->address->toBase58());




// 由 wif 导入
//$wif = 'L4shZ7B4NFQw2eqKncuUViJdFRq6uk1QUb6HjiuedxN4Q2CaRQKW';
//$prikey = PrivateKey::fromWif($wif);
//$pubkey = $prikey->getPublicKey();
//var_dump($pubkey->toHex());
////
//$addr = Address::fromPubKey($pubkey)->toBase58();
//var_dump($addr);
//var_dump("nihao");

// 由keystore导入
//$data = '{"address":"ZJjS4Jj7fafZEfGUsWE85QMamfxbZwMtAs","label":"2680e0ee","scrypt":{"dkLen":64,"n":4096,"p":8,"r":8},"lock":false,"enc-alg":"aes-256-gcm","hash":null,"salt":"2pbNWspjK4+GNGXJ3g7Bqw==","isDefault":false,"publicKey":"0278bd014a8b28bc286f11240753b45a85c4f1827eca15608878c79fbf88f791f3","signatureScheme":"SHA256withECDSA","algorithm":"ECDSA","parameters":{"curve":"P-256"},"key":"hIUU9yM5IDqI4mYwP6QwLYVLzXomu\/9L8U\/1TWVogza1yAqfSTDgHyUPr2p+SEUB"}';
//$keystore = Keystore::fromJson($data);
//$acc = Account::importFromKeystore($keystore, '123456');
//var_dump($acc->address->toBase58());
//var_dump($acc->exportKeystore()->jsonSerialize());

//==========================zpt,gala交易==========================//
//$fromPrikey = new PrivateKey(
//    ByteArray::fromHex('2cf804f021d94c33a3a288d6fc0d74f19854f6ef01de20f3ad8b19166b221d90')
//);
//var_dump($fromPrikey);
//$acc1 = Account::create('11', $fromPrikey);
//$fromAddress = $acc1->address;
//var_dump($acc1->address->toBase58());
//
//// 交易费用参数
//$gasLimit = '20000';
//$gasPrice = '1';
//
//// JsonRpc 客户端实例
//$rpc = new JsonRpc('http://test1.zeepin.net:20336');
//
//// 资产流向方
//$to = new Address('ZJjS4Jj7fafZEfGUsWE85QMamfxbZwMtAs');
//
//// 构造交易数据
//$zptBuilder = new ZptAssetTxBuilder();
//$tx = $zptBuilder->makeTransferTx('GALA', $fromAddress, $to, 10, $gasPrice, $gasLimit);
//var_dump($tx);
//var_dump($tx->serialize());
//
//// 对交易进行签名
//$txBuilder = new TransactionBuilder();
//$txBuilder->signTransaction($tx, $fromPrikey);
//
//// 发送交易
////$JsonRpcResult = $rpc->sendRawTransaction($tx->serialize());
////var_dump($JsonRpcResult);
//
//
//// 查询余额
//$balance = $rpc->getBalance($fromAddress);
//var_dump($balance->result);

//==========================合约交易==========================//
$contractAddr = 'e5c0c001a4a76dfa1ceae2ded6fd634c3a2ff572';
$fromPrikey = new PrivateKey(
    ByteArray::fromHex('2cf804f021d94c33a3a288d6fc0d74f19854f6ef01de20f3ad8b19166b221d90')
);
$acc1 = Account::create('11', $fromPrikey);
$fromAddress = $acc1->address;

$to = new Address('ZJjS4Jj7fafZEfGUsWE85QMamfxbZwMtAs');
$to = 'ZJjS4Jj7fafZEfGUsWE85QMamfxbZwMtAs';

$builder = new WasmAssetTxBuilder(new Address($contractAddr));
$gasLimit = '20000';
$gasPrice = '1';
$tx = $builder->makeTransferTx($fromAddress->toBase58(), $to, '12', $gasPrice, $gasLimit, $fromAddress);

print_r($tx);
var_dump($tx->serialize());
// 对交易进行签名
$txBuilder = new TransactionBuilder();
$txBuilder->signTransaction($tx, $fromPrikey);
print_r($tx->serialize());
////
$rpc = new JsonRpc('http://test1.zeepin.net:20336');
//
//
//// 发送交易
//$JsonRpcResult = $rpc->sendRawTransaction($tx->serialize());
//var_dump($JsonRpcResult);


//查询合约余额
//$address = new Address($fromAddress->toBase58());
//$balance = $rpc ->getBalance($address) ->result;
//var_dump($balance);
