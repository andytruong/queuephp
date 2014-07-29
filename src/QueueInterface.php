<?php

namespace AndyTruong\QueuePHP;

interface QueueInterface
{

    public function countRetryJobs(QueueJobInterface $job);

    public function getRetryJobs(QueueJobInterface $job, $limit = 50, $offset = 0);
}
