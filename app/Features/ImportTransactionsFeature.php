<?php

namespace App\Features;

use App\Jobs\ImportTransactionsJob;
use App\Jobs\ValidateFileJob;

/**
 * Class ImportTransactionsFeature
 * @author Yosyp Mykhailiv <y.mykhailiv@bvblogic.com>
 */
class ImportTransactionsFeature
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var array
     */
    protected $mapping;

    /**
     * ImportTransactionsFeature constructor.
     * @param string $fileName
     * @param array $mapping
     */
    public function __construct(string $fileName, array $mapping)
    {
        $this->fileName = $fileName;
        $this->mapping = $mapping;
    }

    /**
     * Run the feature
     */
    public function execute()
    {
        (new ValidateFileJob($this->fileName, $this->mapping))->execute();

        (new ImportTransactionsJob($this->fileName, $this->mapping))->execute();
    }
}