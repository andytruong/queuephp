<?php

namespace AndyTruong\QueuePHP\TestCases\Drivers;

use AndyTruong\QueuePHP\Driver\IronMQDriver;
use AndyTruong\QueuePHP\TestCases\QueuePHPTestCase;
use IronMQ;

class IronMQDriverTest extends QueuePHPTestCase
{

    private function getDriver($backend = null)
    {
        if (null == $backend) {
            $backend = new IronMQ(['token' => 'YE6mDv07RMKIb2d5WxJFNYR-nKQ', 'project_id' => '53d6feebaf17fc0008000029']);
            $driver = new IronMQDriver('phpunit');
            $driver->setBackend($backend);
        }
        return $driver;
    }

    public function testInit()
    {
        $driver = $this->getDriver();
        $this->assertInstanceOf('AndyTruong\QueuePHP\QueueDriverInterface', $driver);
        $this->assertInstanceOf('IronMQ', $driver->getBackend());
    }

    private function createMessage()
    {
        $driver = $this->getDriver();
        $job = $this->dummyJob($driver);
        return $driver->push($job);
    }

    public function testPushMessage()
    {
        $msg_id = $this->createMessage();
        $this->assertNotNull($msg_id);
        $this->assertNotEmpty($msg_id);
    }

    /**
     * @dataProvider sourceGetMessage
     */
    public function testGetMessage($msg_id = null)
    {
        $msg_id = $this->createMessage();

        // Action
        $job = $this->getDriver()->get($msg_id);

        // Check
        $this->assertInstanceOf('AndyTruong\QueuePHP\QueueJobInterface', $job);
        $this->assertNotEmpty($job->getId());
        if (null !== $msg_id) {
            $this->assertEquals($msg_id, $job->getId());
        }

        return $job;
    }

    public function sourceGetMessage()
    {
        return [
            [$this->createMessage()],
            [null]
        ];
    }

    public function testDeleteMessage()
    {
        $driver = $this->getDriver();
        $job = $driver->get($this->createMessage());
        $this->assertTrue($this->getDriver()->delete($job));
    }

    /**
     * @group WIP
     */
    public function testRelease()
    {
        $driver = $this->getDriver();
        $job = $driver->get($this->createMessage());
        $response = $driver->release($job);
        print_r([__METHOD__, __LINE__, $response]);
        exit;
    }

    protected function tearDown()
    {
        $driver = $this->getDriver();
        $ironmq = $driver->getBackend();
        $ironmq->clearQueue('phpunit');
    }

}
