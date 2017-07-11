<?php

namespace Test\Parsers;

use ExtraMocks\Mocks;
use RedisClient\Adapters\RedisStreamAdapter;
use RedisClient\Parsers\BasicRedisParser;

class BasicRedisParserTest extends \PHPUnit_Framework_TestCase
{

    private $adapterMock;
    private $config;

    public function setUp()
    {
        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_socket_client', function ($server, $err_no, $err_str) {
            $dummySocket = socket_create(AF_INET, SOCK_STREAM, 0);
            return $dummySocket;
        });
        $this->adapterMock= $this->getMockBuilder(RedisStreamAdapter::class)
            ->setConstructorArgs(array("key"=>array()))
            ->getMock();
    }

    public function testSimpleStringParsing()
    {
        $this->adapterMock->method('readLine')
            ->willReturn("+OK\r\n");

        $parser = new BasicRedisParser($this->adapterMock);
        $result = $parser->parse();
        $this->assertEquals($result, true);
    }

    public function testBulkStringParsing()
    {
        $this->adapterMock->method('readLine')
            ->willReturn("$4DATA\r\n");
        $this->adapterMock->method('read')
            ->willReturn("DATA\r\n");

        $parser = new BasicRedisParser($this->adapterMock);
        $result = $parser->parse();

        $this->assertEquals($result, "DATA");
    }
}
