<?php

namespace AndyTruong\QueuePHP\TestCases;

use AndyTruong\QueuePHP\QueueDriverInterface;
use AndyTruong\QueuePHP\QueueJob;
use AndyTruong\QueuePHP\QueueJobInterface;
use DateTime;
use PHPUnit_Framework_TestCase;

/**
 * @group WIP
 */
class QueuePHPTestCase extends PHPUnit_Framework_TestCase
{

    protected function dummyJob(QueueDriverInterface $driver)
    {
        $job = new QueueJob();
        $job->setState(QueueJobInterface::STATE_NEW);
        $job->setCreatedAt(new DateTime('- 2 hours'));
        $job->setHandler('SendMailClass@sendAction');
        $job->setMaxRetries(10);
        $job->setMaxRuntime(60);
        $job->setDriver($driver);
        $job->setParams([
            'from'    => 'mrFrom@mailinator.com',
            'to'      => 'mrTo@mailinator.com',
            'subject' => 'Sample subject',
            'body'    => 'Are you fine?'
        ]);
        $job->setPriority(QueueJobInterface::PRIORITY_HIGH);
        return $job;
    }

}
