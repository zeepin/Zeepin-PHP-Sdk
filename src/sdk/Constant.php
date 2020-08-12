<?php

namespace zeepin\sdk;

use \Adbar\Dot;

class Constant
{
  public static $DEFAULT_SCRYPT;

  /** @var Dot */
  public static $DEFAULT_ALGORITHM;

  public static $DEFAULT_SM2_ID = '1234567812345678';

  public static $ZPT_BIP44_PATH = "m/44'/1024'/0'/0/0";

  public static $ADDR_VERSION = '50';

  public static $TEST_NODE = 'test1.zeepin.net';

  public static $HTTP_JSON_PORT = '20336';
  public static $HTTP_WS_PORT = '20335';

  public static $NATIVE_INVOKE_NAME = 'ZeepinChain.Native.Invoke';

  /** @var Dot */
  public static $TEST_ZPT_URL;

  /** @var Dot */
  public static $TOKEN_TYPE;

  public static function _init()
  {
    self::$DEFAULT_SCRYPT = (object)[
      'cost' => 4096,
      'blockSize' => 8,
      'parallel' => 8,
      'size' => 64
    ];

    self::$DEFAULT_ALGORITHM = new Dot([
      'algorithm' => 'ECDSA',
      'parameters' => [
        'curve' => 'P-256'
      ]
    ]);

    $TEST_NODE = self::$TEST_NODE;
    $HTTP_JSON_PORT = self::$HTTP_JSON_PORT;
    $HTTP_WS_PORT = self::$HTTP_WS_PORT;
    self::$TEST_ZPT_URL = new Dot([
      'RPC_URL' => "http://{$TEST_NODE}:{$HTTP_JSON_PORT}",
      'SOCKET_URL' => "ws://{$TEST_NODE}:{$HTTP_WS_PORT}"
    ]);

    self::$TOKEN_TYPE = new Dot([
      'ZPT' => 'ZPT',
      'GALA' => 'GALA'
    ]);
  }
}

Constant::_init();
