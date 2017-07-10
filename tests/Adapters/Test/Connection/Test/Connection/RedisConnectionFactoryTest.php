<?php

namespace Test\Connection;

use ExtraMocks\Mocks;
use RedisClient\Connection\RedisConnectionFactory;

class RedisConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $config1 = array("host"=>"127.0.0.1" , "port"=>"1234");
    private $config2 = array("host"=>"127.0.0.1" , "port"=>"5678");
    public function setUp()
    {
        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_socket_client', function ($server, $err_no, $err_str) {
            $dummySocket = socket_create(AF_INET, SOCK_STREAM, 0);
            return $dummySocket;
        });
    }


    public function testOneObjectPerConnection()
    {
        //trying to create multiple objects for two connections, the expected result is two calls only to the socket
        for ($i=0; $i < 10; $i++) {
            RedisConnectionFactory::getConnection($this->config1);
            RedisConnectionFactory::getConnection($this->config2);
        }
        $this->assertSame(2, Mocks::getCountCalls('\RedisClient\Adapters\stream_socket_client'));
    }
}
