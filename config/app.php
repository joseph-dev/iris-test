<?php

use App\Data\Transaction;

return [
    'database' => [
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => 'qwertyuiop',
        'database' => 'iris-test',
        'charset'  => 'utf8',
    ],

    'mapping' => [
        Transaction::TRANSACTION_DATE        => 'Transaction Date',
        Transaction::TRANSACTION_TYPE        => 'Transaction Type',
        Transaction::TRANSACTION_CARD_TYPE   => 'Transaction Card Type',
        Transaction::TRANSACTION_CARD_NUMBER => 'Transaction Card Number',
        Transaction::TRANSACTION_AMOUNT      => 'Transaction Amount',
        Transaction::BATCH_DATE              => 'Batch Date',
        Transaction::BATCH_REF_NUM           => 'Batch Reference Number',
        Transaction::MERCHANT_ID             => 'Merchant ID',
        Transaction::MERCHANT_NAME           => 'Merchant Name',
    ],

    'filePath' => realpath(__DIR__ . '/../storage/report.csv'),
];