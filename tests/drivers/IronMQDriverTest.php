<?php

namespace AndyTruong\QueuePHP\TestCases\Drivers;

use AndyTruong\QueuePHP\Driver\IronMQDriver;
use IronMQ;

class IronMQDriverTest extends DriverTestCase
{

    protected $backend_class = 'IronMQ';

    protected function getDriver($backend = null)
    {
        if (null == $backend) {
            $backend = new IronMQ(['token' => 'YE6mDv07RMKIb2d5WxJFNYR-nKQ', 'project_id' => '53d6feebaf17fc0008000029']);
            $driver = new IronMQDriver('queuephp');
            $driver->setBackend($backend);
        }
        return $driver;
    }

    protected function tearDown()
    {
        $driver = $this->getDriver();
        $ironmq = $driver->getBackend();
        $ironmq->clearQueue('queuephp');
    }

}
