<?php

namespace AndyTruong\QueuePHP\TestCases\Drivers;

use AndyTruong\QueuePHP\Driver\SQSDriver;
use Aws\Sqs\SqsClient;

class SQSDriverTest extends DriverTestCase
{

    protected $backend_class = 'Aws\Sqs\SqsClient';

    protected function getDriver($backend = null)
    {
        if (null == $backend) {
            $backend = SqsClient::factory([
                    'key'    => '…',
                    'secret' => '…',
                    'region' => 'ap-southeast-1',
            ]);
            $driver = new SQSDriver('https://sqs.ap-southeast-1.amazonaws.com/049718854479/queuephp');
            $driver->setBackend($backend);
        }
        return $driver;
    }

    /**
     * @group WIPDEBUG
     */
    public function testDebug()
    {
        $id = $this->createMessage();
        $response = $this->getDriver()->get();
        print_r([__METHOD__, __LINE__, $response]);
        exit;
        #$job = $this->getDriver()->get();
        #$this->getDriver()->debug($job);
    }

    public function testPushMessage()
    {
        $this->assertNotEmpty($msg_id = $this->createMessage());
    }

    public function testGetMessage($msg_id = null)
    {
        $job = $this->getDriver()->get();
        $this->assertInstanceOf('AndyTruong\QueuePHP\QueueJobInterface', $job);
    }

    public function testDelete()
    {
        $this->assertTrue($response = $this->getDriver()->delete($job));
    }

}
