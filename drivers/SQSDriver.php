<?php

namespace AndyTruong\QueuePHP\Driver;

use AndyTruong\QueuePHP\QueueDriverInterface;
use AndyTruong\QueuePHP\QueueJobInterface;

class SQSDriver implements QueueDriverInterface
{

    public function push(QueueJobInterface $job)
    {

    }

    public function pushMultiple($jobs)
    {

    }

    public function get($job_id = null)
    {

    }

    public function countRetryJobs(QueueJobInterface $job)
    {

    }

    public function release(QueueJobInterface $job)
    {

    }

    public function delete(QueueJobInterface $job)
    {

    }

    public function getRetryJobs(QueueJobInterface $job, $limit = 50, $offset = 0)
    {

    }

}