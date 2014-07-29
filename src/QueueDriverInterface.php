<?php

namespace AndyTruong\QueuePHP;

interface QueueDriverInterface
{

    /**
     * Push message to queue service.
     *
     * @param QueueJobInterface $job
     */
    public function push(QueueJobInterface $job);

    /**
     * Get job using ID.
     *
     * @param int $job_id
     * @return QueueJobInterface
     */
    public function get($job_id);

    /**
     * Remove a job queue from service.
     *
     * @param QueueJobInterface $job
     */
    public function delete(QueueJobInterface $job);

    /**
     * Release a job.
     * 
     * @param QueueJobInterface $job
     */
    public function release(QueueJobInterface $job);

    /**
     * Push multiple messages to queue service.
     *
     * @param QueueJobInterface[] $jobs
     */
    public function pushMultiple($jobs);

    /**
     * Count retry-jobs.
     *
     * @param QueueJobInterface $job
     */
    public function countRetryJobs(QueueJobInterface $job);

    /**
     * Get retry-jobs.
     *
     * @param QueueJobInterface $job
     * @param int $limit
     * @param int $offset
     * @return QueueJobInterface[]
     */
    public function getRetryJobs(QueueJobInterface $job, $limit = 50, $offset = 0);
}
