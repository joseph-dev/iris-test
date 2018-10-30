<?php

namespace App\Jobs;

/**
 * Class ValidateFileJob
 * @author Yosyp Mykhailiv <y.mykhailiv@bvblogic.com>
 */
class ValidateFileJob implements JobInterface
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
     * ValidateFileJob constructor.
     * @param string $filePath
     * @param array $mapping
     */
    public function __construct(string $filePath, array $mapping)
    {
        $this->filePath = $filePath;
        $this->mapping = $mapping;
    }

    /**
     * Run the job
     */
    public function execute()
    {
        $this->checkIfFileExists();
        $this->checkIfAllColumnsArePresent();
    }

    /**
     * @throws \Exception
     */
    private function checkIfFileExists()
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("The file with path '{$this->filePath}' doesn't exist!");
        }
    }

    /**
     * @throws \Exception
     */
    private function checkIfAllColumnsArePresent()
    {
        $fileHandler = fopen($this->filePath, 'r');
        if (!$fileHandler) {
            throw new \Exception("Can't open the file with path: '{$this->filePath}'");
        }

        $headers = fgetcsv($fileHandler);
        fclose($fileHandler);

        foreach ($this->mapping as $column => $columnName) {
            if (!in_array($columnName, $headers)) {
                throw new \Exception("The column '{$columnName}' is not present in the file");
            }
        }
    }
}