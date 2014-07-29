<?php

namespace AndyTruong\QueuePHP\Driver;

use AndyTruong\QueuePHP\QueueDriverInterface;
use AndyTruong\QueuePHP\QueueJob;
use AndyTruong\QueuePHP\QueueJobInterface;
use IronMQ;
use RuntimeException;

/**
 * @link http://dev.iron.io/mq/reference/api/
 *
 * FIFO, no prioritied messages.
 */
class IronMQDriver implements QueueDriverInterface
{

    /** @var IronMQ */
    private $backend;

    /** @var string */
    private $queue_name;

    /**
     * Constructor.
     *
     * @param string $queue_name
     */
    public function __construct($queue_name)
    {
        $this->queue_name = $queue_name;
    }

    /**
     * Inject backend.
     *
     * @param IronMQ $backend
     */
    public function setBackend(IronMQ $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Get IronMQ backend.
     *
     * @return IronMQ
     */
    public function getBackend()
    {
        return $this->backend;
    }

    public function push(QueueJobInterface $job)
    {
        $message_properties = [
            'expires_in' => 3600 * 365, // 1 year
            'timeout'    => $job->getMaxRuntime(),
            'delay'      => ($future = $job->getExecuteAfter()) ? $future->getTimestamp() - time() : 0,
        ];

        $message_body = json_encode([
            'handler'     => $job->getHandler(),
            'params'      => $job->getParams(),
            'state'       => $job->getState(),
            'createdAt'   => $job->getCreatedAt()->format(DATE_ISO8601),
            'startedAt'   => ($tmp = $job->getStartedAt()) ? $tmp->format(DATE_ISO8601) : '',
            'reviewedAt'  => ($tmp = $job->getReviewedAt()) ? $tmp->format(DATE_ISO8601) : '',
            'closedAt'    => ($tmp = $job->getClosedAt()) ? $tmp->format(DATE_ISO8601) : '',
            'output'      => null,
            'errorOutput' => null,
            'maxRetries'  => $job->getMaxRetries(),
            'runtime'     => null,
        ]);

        return $this
                ->getBackend()
                ->postMessage($this->queue_name, $message_body, $message_properties)->id;
    }

    public function pushMultiple($jobs)
    {

    }

    /**
     * {@inheritdoc}
     * @param int|string|null $job_id
     * @return boolean|QueueJobInterface
     */
    public function get($job_id = null)
    {
        if (null === $job_id) {
            $message = $this->getBackend()->getMessage($this->queue_name);
        }
        else {
            $message = $this->getBackend()->getMessageById($this->queue_name, $job_id);
        }

        if (!$message) {
            return false;
        }

        $message->body = json_decode($message->body, true);

        foreach (['createdAt', 'startedAt', 'reviewedAt', 'closedAt'] as $pty) {
            if (!empty($message->body[$pty])) {
                $message->body[$pty] = date_create_from_format(DATE_ISO8601, $message->body[$pty]);
            }
            else {
                unset($message->body[$pty]);
            }
        }

        return QueueJob::fromArray([
                'id'     => $message->id,
                'driver' => $this] + $message->body);
    }

    /**
     * {@inheritdoc}
     * @param QueueJobInterface $job
     */
    public function release(QueueJobInterface $job)
    {
        if ($response = $this->getBackend()->releaseMessage($this->queue_name, $job->getId())) {
            $response = json_decode($response, true);
            if (isset($response['msg'])) {
                return 'Released' === $response['msg'];
            }
        }

        throw new RuntimeException('Unexpected response.');
    }

    public function delete(QueueJobInterface $job)
    {
        if ($response = $this->getBackend()->deleteMessage($this->queue_name, $job->getId())) {
            $response = json_decode($response, true);
            if (isset($response['msg'])) {
                return 'Deleted' === $response['msg'];
            }
        }

        throw new RuntimeException('Unexpected response.');
    }

    public function getRetryJobs(QueueJobInterface $job, $limit = 50, $offset = 0)
    {

    }

    public function countRetryJobs(QueueJobInterface $job)
    {

    }

}
