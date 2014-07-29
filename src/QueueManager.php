<?php

namespace AndyTruong\QueuePHP;

class QueueManager
{

    /** @var array */
    private $options = [];

    /** @var DriverInterface */
    private $default_driver = 'default';

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    public function setOptions(array $options)
    {

    }

    /**
     * Get driver.
     *
     * @param string $handler
     * @return QueueDriverInterface
     */
    public function getDriver($handler)
    {

    }

    public function push($handler, array $params = [])
    {
        // create the job
        $job = new QueueJob();
        $job->setParams($params);

        // tell driver to push the job
        return $this->getDriver($handler)->push($job);
    }

    public function getJob($job_id, $handler)
    {
        return $this->getDriver($handler)->get($job_id);
    }

}
