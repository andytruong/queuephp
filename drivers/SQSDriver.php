<?php

namespace AndyTruong\QueuePHP\Driver;

use AndyTruong\QueuePHP\QueueDriverInterface;
use AndyTruong\QueuePHP\QueueJob;
use AndyTruong\QueuePHP\QueueJobInterface;
use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;
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
        $ids = $this->pushMultiple([$job]);
        return reset($ids);
    }

    /**
     * {@inheritdoc}
     * @return int
     * @param QueueJobInterface[] $jobs
     */
    public function pushMultiple($jobs)
    {
        $args = ['QueueUrl' => $this->queueUrl, 'Entries' => []];
        foreach ($jobs as $index => $job) {
            $args['Entries'][] = [
                'Id'                => $index,
                'DelaySeconds'      => ($tmp = $job->getExecuteAfter()) ? $tmp->getTimestamp() - time() : 0,
                'MessageBody'       => json_encode([
                    'handler' => $job->getHandler(),
                    'params'  => $job->getParams()
                ]),
                'MessageAttributes' => [
                    'state'       => ['DataType' => 'String', 'StringValue' => $job->getState()],
                    'maxRetries'  => ['DataType' => 'String', 'StringValue' => $job->getMaxRetries()],
                    'createdAt'   => ['DataType' => 'String', 'StringValue' => $job->getCreatedAt()->format(DATE_ISO8601)],
                    'startedAt'   => ['DataType' => 'String', 'StringValue' => $job->getStartedAt()->format(DATE_ISO8601)],
                    'reviewedAt'  => ['DataType' => 'String', 'StringValue' => $job->getReviewedAt()->format(DATE_ISO8601)],
                    'closedAt'    => ['DataType' => 'String', 'StringValue' => $job->getClosedAt()->format(DATE_ISO8601)],
                    'output'      => ['DataType' => 'String', 'StringValue' => '<blank />'],
                    'errorOutput' => ['DataType' => 'String', 'StringValue' => '<blank />'],
                    'runtime'     => ['DataType' => 'String', 'StringValue' => '<blank />'],
                ]
            ];
        }

        /* @var $response \Guzzle\Service\Resource\Model */
        $ids = [];
        $response = $this->getBackend()->sendMessageBatch($args);
        $results = $response->get('Successful');
        if ($results) {
            foreach ($results as $result) {
                $ids[] = $result['MessageId'];
            }
        }
        return $ids;
    }

    public function get($job_id = null)
    {
        // SQS does not allow get message by ID?
        /* @var $response Model */
        $response = $this->getBackend()->receiveMessage([
            'QueueUrl'              => $this->queueUrl,
            'MaxNumberOfMessages'   => 1,
            'AttributeNames'        => ['Sent', 'ReceiveCount', 'MessageAttributeCount'],
            'MessageAttributeNames' => ['state', 'maxRetries', 'createdAt', 'startedAt', 'reviewedAt', 'closedAt', 'output', 'errorOutput', 'runtime']
        ]);

        $message = $response->get('Messages')[0];
        $message['Body'] = json_decode($message['Body'], true);

        foreach ($response['MessageAttributes'] as $pty => $info) {
            $message['Body'][$pty] = $info['StringValue'];
        }

        foreach (['createdAt', 'startedAt', 'reviewedAt', 'closedAt'] as $key) {
            if (empty($message['Body'][$key])) {
                unset($message['Body'][$key]);
            }
            else {
                $message['Body'][$key] = date_create_from_format(DATE_ISO8601, $message['Body'][$key]);
            }
        }

        return QueueJob::fromArray([
                'id'         => $message['MessageId'],
                'attributes' => [
                    'MD5OfBody'     => $message['ReceiptHandle'],
                    'ReceiptHandle' => $message['ReceiptHandle']]
                ] + $message['Body']);
    }

    public function countRetryJobs(QueueJobInterface $job)
    {

    }

    public function release(QueueJobInterface $job)
    {

    }

    /**
     * {@inheritdoc}
     * @param QueueJobInterface $job
     * @return boolean
     */
    public function delete(QueueJobInterface $job)
    {
        return $this->getBackend()->deleteMessage([
                'QueueUrl'      => $this->queueUrl,
                'ReceiptHandle' => $job->getAttribute('ReceiptHandle')
            ]) instanceof Model;
    }

    public function getRetryJobs(QueueJobInterface $job, $limit = 50, $offset = 0)
    {

    }

    public function debug(QueueJobInterface $job)
    {
        $response = $this->getBackend()->sendMessageBatch([
            'QueueUrl' => $this->queueUrl,
            'Entries'  => [
                [
                    'Id'                => 'CanThisBeAnyThing', // $job->getId(),
                    'MessageBody'       => json_encode([
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
                    'MessageAttributes' => [
                        'Foo' => [
                            'DataType'    => 'String',
                            'StringValue' => 'Foo Value…'
                        ]
                    ]
                ]
            ]
        ]);

        print_r([__METHOD__, __LINE__, $response]);
        exit;
    }

}
