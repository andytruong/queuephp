<?php

namespace AndyTruong\QueuePHP;

use DateTime;

/**
 * @Entity()
 * @ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class QueueJob implements QueueJobInterface
{

    /**
     * @Field(type="bigint", options = {"unsigned": true})
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=15)
     * @var string
     */
    private $state;

    /**
     * @Column(type="string", length=256)
     * @var QueueInterface
     */
    private $queue;

    /**
     * @Column(type="smallint")
     * @var int
     */
    private $priority = QueueJobInterface::PRIORITY_DEFAULT;

    /**
     * @Column(type="datetime", name="createdAt")
     * @var DateTime
     */
    private $createdAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $startedAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $reviewedAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $executeAfter;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $closedAt;

    /**
     * @Column(type="json_array")
     * @var array
     */
    private $params;

    /**
     * @Column(type="text", nullable=true)
     * @var string
     */
    private $output;

    /**
     * @Column(type="text", nullable=true)
     * @var string
     */
    private $errorOutput;

    /**
     * @Column(type="smallint", options = {"unsigned": true})
     * @var int
     */
    private $maxRuntime = 0;

    /**
     * @Column(type="smallint", options = {"unsigned": true})
     * @var int
     */
    private $maxRetries = 0;

    /**
     * @ManyToOne(targetEntity="AndyTruong\QueuePHP\QueueJob", inversedBy = "retryJobs")
     * @var QueueJobInterface
     */
    private $originalJob;

    /**
     * @OneToMany(targetEntity="AndyTruong\QueuePHP\QueueJob", mappedBy="originalJob", cascade = {"persist", "remove", "detach"})
     * @var QueueJobInterface[]
     */
    private $retryJobs;

    /** @var int */
    private $runtime;

    /**
     * {@inheritdoc}
     * @param int|string $id
     * @return QueueJob
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     * @param string $state
     * @return QueueJob
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * {@inheritdoc}
     * @param QueueInterface $queue
     * @return QueueJob
     */
    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     * @param int $priority
     * @return QueueJob
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     * @return DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * {@inheritdoc}
     * @return DateTime
     */
    public function getReviewedAt()
    {
        return $this->reviewedAt;
    }

    /**
     * {@inheritdoc}
     * @return DateTime
     */
    public function getExecuteAfter()
    {
        return $this->executeAfter;
    }

    /**
     * {@inheritdoc}
     * @return DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * {@inheritdoc}
     * @return int
     */
    public function getMaxRuntime()
    {
        return $this->maxRuntime;
    }

    /**
     * {@inheritdoc}
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * {@inheritdoc}
     * @return QueueJobInterface
     */
    public function getOriginalJob()
    {
        return $this->originalJob;
    }

    public function getRuntime()
    {
        return $this->runtime;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setStartedAt(DateTime $startedAt)
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function setReviewedAt(DateTime $reviewedAt)
    {
        $this->reviewedAt = $reviewedAt;
        return $this;
    }

    public function setExecuteAfter(DateTime $executeAfter)
    {
        $this->executeAfter = $executeAfter;
        return $this;
    }

    public function setClosedAt(DateTime $closedAt)
    {
        $this->closedAt = $closedAt;
        return $this;
    }

    public function setMaxRuntime($maxRuntime)
    {
        $this->maxRuntime = $maxRuntime;
        return $this;
    }

    public function setMaxRetries($maxRetries)
    {
        $this->maxRetries = $maxRetries;
        return $this;
    }

    public function setOriginalJob(QueueJobInterface $originalJob)
    {
        $this->originalJob = $originalJob;
        return $this;
    }

    public function setRetryJobs(QueueJobInterface $retryJobs)
    {
        $this->retryJobs = $retryJobs;
        return $this;
    }

    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
        return $this;
    }

    public function setErrorOutput($errorOutput)
    {
        $this->errorOutput = $errorOutput;
        return $this;
    }

    public function addErrorOutput($output)
    {
        $this->errorOutput = null === $this->errorOutput ? '' : $this->errorOutput;
        $this->errorOutput .= $output;
        return $this;
    }

    public function getErrorOutput()
    {
        return $this->errorOutput;
    }

    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    public function addOutput($output)
    {
        $this->output = null === $this->output ? '' : $this->output;
        $this->output .= $output;
        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function addParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function countRetryJobs()
    {
        return $this->getQueue()->countRetryJobs($this);
    }

    public function getRetryJobs($limit = 50, $offset = 0)
    {
        if (null === $this->retryJobs) {
            $this->retryJobs = $this->getQueue()->getRetryJobs($this, $limit, $offset);
        }
        return $this->retryJobs;
    }

    public function isCanceled()
    {
        return QueueJobInterface::STATE_CANCELED === $this->state;
    }

    public function isFailed()
    {
        return QueueJobInterface::STATE_FAILED === $this->state;
    }

    public function isFinished()
    {
        return QueueJobInterface::STATE_FINISHED === $this->state;
    }

    public function isIncomplete()
    {
        return QueueJobInterface::STATE_INCOMPLETE === $this->state;
    }

    public function isNew()
    {
        return QueueJobInterface::STATE_NEW === $this->state;
    }

    public function isPending()
    {
        return QueueJobInterface::STATE_PENDING === $this->state;
    }

    public function isRunning()
    {
        return QueueJobInterface::STATE_RUNNING === $this->state;
    }

    public function isTerminated()
    {
        return QueueJobInterface::STATE_TERMINATED === $this->state;
    }

    public function isStartable()
    {
        $startable = false;

        return $startable;
    }

    public function isRetryAllowed()
    {
        return $this->getMaxRetries() > 0;
    }

    public function __toString()
    {

    }

}
