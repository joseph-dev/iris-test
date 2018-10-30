<?php

namespace App\Data;

/**
 * Class Transaction
 * @author Yosyp Mykhailiv <y.mykhailiv@bvblogic.com>
 */
class Transaction
{
    const MERCHANT_ID = 'mid'; // digits only, up to 18 digits
    const MERCHANT_NAME = 'dba'; // string, max length - 100
    const BATCH_DATE = 'batch_date'; // YYYY-MM-DD
    const BATCH_REF_NUM = 'batch_ref_num'; // digits only, up to 24 digits
    const TRANSACTION_DATE = 'trans_date'; // YYYY-MM-DD
    const TRANSACTION_TYPE = 'trans_type'; // string, max length - 20
    const TRANSACTION_CARD_TYPE = 'trans_card_type'; // string, max length - 2, possible values - VI/MC/AX and so on
    const TRANSACTION_CARD_NUMBER = 'trans_card_num'; // string, max length - 20
    const TRANSACTION_AMOUNT = 'trans_amount'; // amount, negative values are possible

    /**
     * @var string
     */
    public $merchantId;

    /**
     * @var string
     */
    public $merchantName;

    /**
     * @var string
     */
    public $batchDate;

    /**
     * @var string
     */
    public $batchReferenceNumber;

    /**
     * @var string
     */
    public $transactionDate;

    /**
     * @var string
     */
    public $transactionType;

    /**
     * @var string
     */
    public $transactionCardType;

    /**
     * @var string
     */
    public $transactionCardNumber;

    /**
     * @var string
     */
    public $transactionAmount;

    /**
     * Transaction constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->merchantId = $data[self::MERCHANT_ID];
        $this->merchantName = $data[self::MERCHANT_NAME];
        $this->batchDate = $data[self::BATCH_DATE];
        $this->batchReferenceNumber = $data[self::BATCH_REF_NUM];
        $this->transactionDate = $data[self::TRANSACTION_DATE];
        $this->transactionType = $data[self::TRANSACTION_TYPE];
        $this->transactionCardType = $data[self::TRANSACTION_CARD_TYPE];
        $this->transactionCardNumber = $data[self::TRANSACTION_CARD_NUMBER];
        $this->transactionAmount = $data[self::TRANSACTION_AMOUNT];
    }
}