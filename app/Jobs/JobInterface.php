<?php

namespace App\Jobs;

/**
 * Interface JobInterface
 */
interface JobInterface
{
    /**
     * @return mixed
     */
    public function execute();
}