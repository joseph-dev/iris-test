<?php

namespace App\Jobs;

use App\Data\Transaction;
use App\Helpers\Config;
use PDO;

/**
 * Class ImportTransactionsJob
 * @author Yosyp Mykhailiv <y.mykhailiv@bvblogic.com>
 */
class ImportTransactionsJob implements JobInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var array
     */
    private $mapping;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * ValidateFileJob constructor.
     * @param string $filePath
     * @param array $mapping
     */
    public function __construct(string $filePath, array $mapping)
    {
        $this->filePath = $filePath;
        $this->mapping = $mapping;
        $this->pdo = $this->getDatabaseConnection();
    }

    /**
     * Run the job
     */
    public function execute()
    {
        $fileHandler = fopen($this->filePath, 'r');
        $headers = array_flip(fgetcsv($fileHandler));
        $transactionBatch = [];

        while (!feof($fileHandler)) {

            $readTransaction = $this->readTransaction($fileHandler, $headers);
            $lastAddedTransaction = end($transactionBatch);

            if (!$lastAddedTransaction) {
                $transactionBatch[] = $readTransaction;
                continue;
            }

            if ($this->areTransactionsFromTheSameBatch($lastAddedTransaction, $readTransaction)) {
                $transactionBatch[] = $readTransaction;
                continue;
            }

            $this->saveBatchOfTransactions($transactionBatch);
            $transactionBatch = [$readTransaction];
        }

        fclose($fileHandler);
    }

    /**
     * @param $fileHandler
     * @param array $headers
     * @return Transaction
     */
    private function readTransaction($fileHandler, array $headers)
    {
        $data = [];
        $line = fgetcsv($fileHandler);

        foreach ($this->mapping as $property => $columnName) {
            $data[$property] = $line[$headers[$columnName]];
        }

        return new Transaction($data);
    }

    /**
     * @param Transaction $addedTransaction
     * @param Transaction $newTransaction
     * @return bool
     */
    private function areTransactionsFromTheSameBatch(Transaction $addedTransaction, Transaction $newTransaction)
    {
        if (
            $addedTransaction->batchDate == $newTransaction->batchDate &&
            $addedTransaction->batchReferenceNumber == $newTransaction->batchReferenceNumber
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $transactions
     */
    private function saveBatchOfTransactions(array $transactions)
    {
        /** @var Transaction $transaction */
        $transaction = reset($transactions);

        $merchant = $this->getMerchant($transaction);
        $transactionBatch = $this->saveBatch($merchant, $transaction);
        $this->saveTransactions($transactionBatch, $transactions);
    }

    /**
     * @param Transaction $transaction
     * @return mixed
     */
    private function getMerchant(Transaction $transaction)
    {
        $query = $this->pdo->prepare("SELECT * FROM merchants WHERE merchants.mid = ?");
        $query->execute([$transaction->merchantId]);
        $merchant = $query->fetch(PDO::FETCH_OBJ);

        if (!$merchant) {
            $query = $this->pdo->prepare("INSERT INTO merchants(mid, name) VALUES(:mid, :name)");
            $query->execute([
                'mid'  => $transaction->merchantId,
                'name' => $transaction->merchantName,
            ]);

            $query = $this->pdo->prepare("SELECT * FROM merchants WHERE merchants.id = ?");
            $query->execute([$this->pdo->lastInsertId()]);
            $merchant = $query->fetch(PDO::FETCH_OBJ);
        }

        return $merchant;
    }

    /**
     * @param \stdClass $merchant
     * @param Transaction $transaction
     * @return mixed
     */
    private function saveBatch(\stdClass $merchant, Transaction $transaction)
    {
        $query = $this->pdo->prepare("INSERT INTO transaction_batches(merchant_id, date, reference_number) VALUES(:merchant_id, :date, :reference_number)");
        $query->execute([
            'merchant_id'      => $merchant->id,
            'date'             => $transaction->batchDate,
            'reference_number' => $transaction->batchReferenceNumber,
        ]);

        $query = $this->pdo->prepare("SELECT * FROM transaction_batches WHERE transaction_batches.id = ?");
        $query->execute([$this->pdo->lastInsertId()]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @param \stdClass $transactionBatch
     * @param array $transactions
     */
    private function saveTransactions(\stdClass $transactionBatch, array $transactions)
    {
        $query = "INSERT INTO transactions(transaction_batch_id, transaction_type_id, transaction_card_type_id, date, card_number, amount) VALUES";
        $data = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {

            $query .= "(?, ?, ?, ?, ?, ?), ";

            $data[] = $transactionBatch->id;
            $data[] = $this->getTransactionType($transaction)->id;
            $data[] = $this->getTransactionCardType($transaction)->id;
            $data[] = $transaction->transactionDate;
            $data[] = $transaction->transactionCardNumber;
            $data[] = $transaction->transactionAmount;

        }

        $query = rtrim($query, ', ');
        $query = $this->pdo->prepare($query);
        $query->execute($data);
    }

    /**
     * @param Transaction $transaction
     * @return mixed
     */
    private function getTransactionType(Transaction $transaction)
    {
        $query = $this->pdo->prepare("SELECT * FROM transaction_types WHERE transaction_types.name = ?");
        $query->execute([$transaction->transactionType]);
        $transactionType = $query->fetch(PDO::FETCH_OBJ);

        if (!$transactionType) {
            $query = $this->pdo->prepare("INSERT INTO transaction_types(name) VALUES(:name)");
            $query->execute([
                'name' => $transaction->transactionType,
            ]);

            $query = $this->pdo->prepare("SELECT * FROM transaction_types WHERE transaction_types.id = ?");
            $query->execute([$this->pdo->lastInsertId()]);
            $transactionType = $query->fetch(PDO::FETCH_OBJ);
        }

        return $transactionType;
    }

    /**
     * @param Transaction $transaction
     * @return mixed
     */
    private function getTransactionCardType(Transaction $transaction)
    {
        $query = $this->pdo->prepare("SELECT * FROM transaction_card_types WHERE transaction_card_types.name = ?");
        $query->execute([$transaction->transactionCardType]);
        $transactionCardType = $query->fetch(PDO::FETCH_OBJ);

        if (!$transactionCardType) {
            $query = $this->pdo->prepare("INSERT INTO transaction_card_types(name) VALUES(:name)");
            $query->execute([
                'name' => $transaction->transactionCardType,
            ]);

            $query = $this->pdo->prepare("SELECT * FROM transaction_card_types WHERE transaction_card_types.id = ?");
            $query->execute([$this->pdo->lastInsertId()]);
            $transactionCardType = $query->fetch(PDO::FETCH_OBJ);
        }

        return $transactionCardType;
    }

    /**
     * @return PDO
     */
    private function getDatabaseConnection()
    {
        $databaseConfig = Config::get('app.database');

        $dsn = "mysql:host={$databaseConfig['host']};dbname={$databaseConfig['database']};charset={$databaseConfig['charset']}";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $databaseConfig['user'], $databaseConfig['password'], $opt);
    }
}