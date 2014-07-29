<?php

namespace AndyTruong\QueuePHP\TestCases\Drivers;

use AndyTruong\QueuePHP\TestCases\QueuePHPTestCase;

abstract class DriverTestCase extends QueuePHPTestCase
{

    protected $backend_class;

    protected function createMessage()
    {
        $driver = $this->getDriver();
        $job = $this->dummyJob($driver);
        return $driver->push($job);
    }

    public function testInit()
    {
        $driver = $this->getDriver();
        $this->assertInstanceOf('AndyTruong\QueuePHP\QueueDriverInterface', $driver);
        $this->assertInstanceOf($this->backend_class, $driver->getBackend());
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

    public function testRelease()
    {
        $driver = $this->getDriver();
        $job = $driver->get($this->createMessage());
        $this->assertTrue($driver->release($job));
    }

}
