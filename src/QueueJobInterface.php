<?php

namespace AndyTruong\QueuePHP;

use DateTime;

/**
 * Cloned from https://github.com/schmittjoh/JMSJobQueueBundle/blob/master/Entity/Job.php
 */
interface QueueJobInterface
{

    /** State if job is inserted, but not yet ready to be started. */
    const STATE_NEW = 'new';

    /**
     * State if job is inserted, and might be started.
     *
     * It is important to note that this does not automatically mean that all
     * jobs of this state can actually be started, but you have to check
     * isStartable() to be absolutely sure.
     *
     * In contrast to NEW, jobs of this state at least might be started,
     * while jobs of state NEW never are allowed to be started.
     */
    const STATE_PENDING = 'pending';

    /** State if job was never started, and will never be started. */
    const STATE_CANCELED = 'canceled';

    /** State if job was started and has not exited, yet. */
    const STATE_RUNNING = 'running';

    /** State if job exists with a successful exit code. */
    const STATE_FINISHED = 'finished';

    /** State if job exits with a non-successful exit code. */
    const STATE_FAILED = 'failed';

    /** State if job exceeds its configured maximum runtime. */
    const STATE_TERMINATED = 'terminated';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    const STATE_INCOMPLETE = 'incomplete';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    const DEFAULT_QUEUE = 'default';

    /** Low priority */
    const PRIORITY_LOW = -5;

    /** Default priority */
    const PRIORITY_DEFAULT = 0;

    /** Hight priority */
    const PRIORITY_HIGH = 5;

    /**
     * Get job ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Get the state.
     *
     * @return string
     */
    public function getState();

    /**
     * Get job priority.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Is the job startable.
     *
     * @return bool
     */
    public function isStartable();

    /**
     * Set state.
     *
     * @param string $state
     */
    public function setState($state);

    /**
     * Get created time.
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Get closed time.
     *
     * @return DateTime
     */
    public function getClosedAt();

    /**
     * Get startable datetime.
     *
     * @return DateTime
     */
    public function getExecuteAfter();

    /**
     * Set startable datetime.
     *
     * @param DateTime $executeAfter
     */
    public function setExecuteAfter(DateTime $executeAfter);

    /**
     * Get spent time.
     *
     * @return int
     */
    public function getRuntime();

    /**
     * Set spent time.
     *
     * @param int $time
     */
    public function setRuntime($time);

    /**
     * Set output.
     *
     * @param string $output
     */
    public function setOutput($output);

    /**
     * Append output.
     *
     * @param string $output
     */
    public function addOutput($output);

    /**
     * Append error output.
     *
     * @param string $output
     */
    public function setErrorOutput($output);

    /**
     * Append error output.
     *
     * @param string $output
     */
    public function addErrorOutput($output);

    /**
     * Set runtime limitation.
     *
     * @return int
     */
    public function getMaxRuntime();

    /**
     * Get started time.
     *
     * @return DateTime
     */
    public function getStartedAt();

    /**
     * Get max-retries.
     *
     * @return int
     */
    public function getMaxRetries();

    /**
     * Get retried queue-jobs.
     *
     * @rerurn QueueJobInterface[]
     */
    public function getRetryJobs($limit = 50, $offset = 0);

    /**
     * Set original job.
     *
     * @param int|string|QueueJobInterface $job
     */
    public function setOriginalJob($job);

    /**
     * @return QueueJobInterface
     */
    public function getOriginalJob();

    /**
     * Count retried jobs.
     *
     * @return int
     */
    public function countRetryJobs();

    /**
     * Set max-retries.
     *
     * @param int $tries
     */
    public function setMaxRetries($tries);

    /**
     * @return bool
     */
    public function isRetryAllowed();

    /**
     * Get reviewed datetime.
     *
     * @return DateTime
     */
    public function getReviewedAt();

    /**
     * Set reviewed datetime.
     *
     * @param DateTime $reviewedAt
     */
    public function setReviewedAt(DateTime $reviewedAt);

    /**
     * @return QueueInterface
     */
    public function getQueue();

    /**
     * @return bool
     */
    public function isNew();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @return bool
     */
    public function isCanceled();

    /**
     * @return bool
     */
    public function isRunning();

    /**
     * @return bool
     */
    public function isTerminated();

    /**
     * @return bool
     */
    public function isFailed();

    /**
     * @return bool
     */
    public function isFinished();

    /**
     * @return bool
     */
    public function isIncomplete();

    /**
     * @return array
     */
    public function getParams();

    /**
     * Set params.
     *
     * @param array $params
     */
    public function setParams(array $params);

    /**
     * Add params.
     *
     * @param string $name
     * @param mixed $value
     */
    public function addParam($name, $value);

    /**
     * @return string
     */
    public function __toString();
}
