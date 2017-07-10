<?php

namespace Test\Adapters;

use ExtraMocks\Mocks;
use RedisClient\Adapters\RedisStreamAdapter;

class RedisStreamAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    public function setUp()
    {
        $this->configTimeout = array("host" => "127.0.0.1", "port" => "6379", "timeout" => 30);
        $this->config = array("host" => "127.0.0.1", "port" => "6379");
    }

    public function testSocketCalledOnce()
    {
        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_socket_client', function ($server, $err_no, $err_str) {
            return $server;
        });

        new RedisStreamAdapter($this->config);
        $this->assertSame(1, Mocks::getCountCalls('\RedisClient\Adapters\stream_socket_client'));
    }

    public function testSocketIsCalledWithRightInfo()
    {
        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_socket_client', function ($server, $err_no, $err_str) {
            $this->assertSame('127.0.0.1:6379', $server);
            return 'resource';
        });

        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_set_timeout',
            function ($resource, $seconds, $microSeconds) {
                $this->assertSame(30, $seconds);
                $this->assertSame(0, $microSeconds);
                $this->assertNotEmpty($resource);
            });

        new RedisStreamAdapter($this->config);
    }

    public function testWrite()
    {
        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_socket_client', function ($server, $err_no, $err_str) {
            $dummySocket = socket_create(AF_INET, SOCK_STREAM, 0);
            return $dummySocket;
        });

        Mocks::mockGlobalFunction('\RedisClient\Adapters\fwrite', function ($resource, $data, $length) {
            $command = 'SET FOO BAR';
            $this->assertSame($data, $command);
            $this->assertSame($length, strlen($command));
        });
        $adapter = new RedisStreamAdapter($this->config);
        $adapter->write('SET FOO BAR');
    }
}

