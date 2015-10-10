<?php

use \Mockery as m;
use League\Flysystem\AliyunOSS\AliyunOSSAdapter;

class AliyunOSSAdapterTest extends PHPUnit_Framework_TestCase
{
    public function getClientMock()
    {
        $mock = m::mock('ALIOSS');
        $mock->shouldReceive('__toString')->andReturn('ALIOSS');
        $mock->shouldReceive('create_bucket')->andReturn(true);

        return $mock;
    }

    public function testInstantiable()
    {
        new AliyunOSSAdapter($this->getClientMock(), 'testBucket');
    }

    public function adapterProvider()
    {
        $mock = $this->getClientMock();

        return [
            [new AliyunOSSAdapter($mock, 'testBucket'), $mock],
        ];
    }
}
