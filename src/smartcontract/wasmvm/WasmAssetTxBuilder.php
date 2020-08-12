<?php

namespace zeepin\smartcontract\wasmvm;

use zeepin\crypto\Address;

use zeepin\core\transaction\Transaction;
use zeepin\core\transaction\TransactionBuilder;
use zeepin\smartcontract\abi\Parameter;
use zeepin\smartcontract\abi\ParameterType;
use zeepin\common\ByteArray;
use zeepin\smartcontract\abi\NativeVmParamsBuilder;

class WasmAssetTxBuilder
{
    public static $fnNames;

    /** @var Address */
    public $contractAddr;

    public function __construct(Address $contractAddr)
    {
        $this->contractAddr = $contractAddr;
    }

    public function makeInitTx(string $gasPrice, string $gasLimit, ? Address $payer = null) : Transaction
    {
        $builder = new TransactionBuilder();
        $fn = self::$fnNames->Init;
        return $builder->makeInvokeTransaction($fn, [], $this->contractAddr, $gasPrice, $gasLimit, $payer);
    }

    public function makeTransferTx(
        string $from,
        string $to,
        string $amount,
        string $gasPrice,
        string $gasLimit,
        ? Address $payer = null
    ) : Transaction {
        $builder = new TransactionBuilder();
        $fn = self::$fnNames->Transfer;
        return $builder->makeInvokeTransaction($fn, [$from, $to, $amount], $this->contractAddr, $gasPrice, $gasLimit, $payer);
    }

    public function makeApproveTx(
        Address $owner,
        Address $spender,
        string $amount,
        string $gasPrice,
        string $gasLimit,
        Address $payer
    ) : Transaction {
        $fn = self::$fnNames->Approve;
        $params = [
            new Parameter('owner', ParameterType::ByteArray, $owner->serialize()),
            new Parameter('spender', ParameterType::ByteArray, $spender->serialize()),
            new Parameter('amount', ParameterType::Long, $amount),
        ];
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, $params, $this->contractAddr, $gasPrice, $gasLimit, $payer);
    }

    public function makeTransferFromTx(
        string $spender,
        string $from,
        string $to,
        string $amount,
        string $gasPrice,
        string $gasLimit,
        Address $payer
    ) : Transaction {
        $fn = self::$fnNames->TransferFromm;
        $params = [$spender, $from, $to, $amount];
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, $params, $this->contractAddr, $gasPrice, $gasLimit, $payer);
    }

    public function makeQueryAllowanceTx(
        string $owner,
        string $spender
    ) : Transaction {
        $fn = self::$fnNames->Allowance;
        $params = [$owner, $spender];
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, $params, $this->contractAddr);
    }

    public function makeQueryBalanceOfTx(string $addr) : Transaction
    {
        $fn = self::$fnNames->BalanceOf;
        $p = $addr;
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, [$p], $this->contractAddr);
    }

    public function makeQueryTotalSupplyTx() : Transaction
    {
        $fn = self::$fnNames->TotalSupply;
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
    }

    public function makeQueryDecimalsTx() : Transaction
    {
        $fn = self::$fnNames->Decimals;
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
    }

    public function makeQuerySymbolTx() : Transaction
    {
        $fn = self::$fnNames->Symbol;
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
    }

    public function makeQueryNameTx() : Transaction
    {
        $fn = self::$fnNames->Name;
        $builder = new TransactionBuilder();
        return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
    }
}

WasmAssetTxBuilder::$fnNames = (object)[
    'Init' => 'init',
    'Transfer' => 'transfer',
    'TransferMulti' => 'transferMulti',
    'Approve' => 'approve',
    'TransferFromm' => 'transferFrom',
    'Allowance' => 'allowance',
    'BalanceOf' => 'balanceOf',
    'TotalSupply' => 'totalSupply',
    'Symbol' => 'symbol',
    'Decimals' => 'decimals',
    'Name' => 'name'
];
