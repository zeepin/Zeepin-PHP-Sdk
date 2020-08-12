<h1 align="center">PHP SDK For Zeepin blockchain </h1>

## Overview

This project is the PHP library for the Zeepin blockchain.

## Requirements

You'd use [phpbrew](https://github.com/phpbrew/phpbrew) to smoothly switch between different PHP versions.

- PHP >= 7.0.0
- [scrypt](https://github.com/DomBlack/php-scrypt)
- [libsodium-php](https://github.com/jedisct1/libsodium-php)
- Build PHP with openssl-1.1.1 which has native support for SM2 and SM3
- Build PHP with gmp, you can refer [doc/build-php.md](doc/build-php.md) to make custom build configuration
- [php-sm](https://github.com/hsiaosiyuan0/php-sm)

## Install

```bash
composer require zeepin/Zeepin-Php-Sdk
```

## Documents

[中文](doc/zh-CN/概述.md)
