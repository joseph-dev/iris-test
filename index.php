<?php

/**
 * Register autoload
 */
require "autoload.php";

use App\Helpers\Config;
use App\Features\ImportTransactionsFeature;

$filePath = Config::get('app.filePath');
$mapping = Config::get('app.mapping');

(new ImportTransactionsFeature($filePath, $mapping))->execute();
