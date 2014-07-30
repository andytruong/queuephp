<?php

namespace AndyTruong\QueuePHP\Driver;

use AndyTruong\QueuePHP\QueueDriverInterface;
use AndyTruong\QueuePHP\QueueJobInterface;
use Aws\Sqs\SqsClient;
use RuntimeException;

/**
 * @link https://github.com/aws/aws-sdk-php/blob/master/docs/service-sqs.rst
 */
class SQSDriver implements QueueDriverInterface
{

    /** @var SqsClient */
    private $backend;

    /** @var string */
    private $queueUrl;

    /**
     * Constructor.
     *
     * @param string $queueUrl
     */
    public function __construct($queueUrl)
    {
        $this->queueUrl = $queueUrl;
    }

    /**
     * Inject SQS backend library.
     *
     * @param SqsClient $backend
     */
    public function setBackend(SqsClient $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Get backend library.
     *
     * @return SqsClient
     * @throws RuntimeException
     */
    public function getBackend()
    {
        if (null === $this->backend) {
            throw new RuntimeException('AmazonSQS client was not injected to ' . __CLASS__);
        }
        return $this->backend;
    }

    /**
     * {@inheritdoc}
     * @link http://docs.aws.amazon.com/AWSSimpleQueueService/latest/APIReference/API_SendMessage.html
     * @param QueueJobInterface $job
     * @return int
     */
    public function push(QueueJobInterface $job)
    {
        $args = [
            'QueueUrl'     => $this->queueUrl,
            'DelaySeconds' => ($tmp = $job->getExecuteAfter()) ? $tmp->getTimestamp() - time() : 0,
            'MessageBody'  => json_encode([
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
            ]),
        ];

        // \Guzzle\Service\Resource\Model->get($key)
        return $this->getBackend()->sendMessage($args)->get('MessageId');
    }

    /**
     * {@inheritdoc}
     * @return int
     * @param QueueJobInterface[] $jobs
     */
    public function pushMultiple($jobs)
    {
        $ids = [];

        foreach ($jobs as $job) {
            // @WHY $this->getBackend()->sendMessageBatch() requires message ID
            // for each item, even when they are not created :/
            $ids[] = $this->push($job);
        }

        return $ids;
    }

    public function get($job_id = null)
    {
        // return $this->getBackend()->
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
