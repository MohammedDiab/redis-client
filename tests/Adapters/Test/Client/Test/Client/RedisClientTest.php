<?php

namespace Test\Client;

use ExtraMocks\Mocks;
use RedisClient\Adapters\RedisStreamAdapter;
use RedisClient\Client\RedisClient;
use RedisClient\Parsers\BasicRedisParser;

class RedisClientTest extends \PHPUnit_Framework_TestCase
{
    private $config = array("host" => "127.0.0.1", "port" => "6379");

    /**
     * @var RedisClient
     */
    private $client;

    private $adapterMock;

    private $parserMock;

    public function setUp()
    {
        Mocks::mockGlobalFunction('\RedisClient\Adapters\stream_socket_client', function ($server, $err_no, $err_str) {
            $dummySocket = socket_create(AF_INET, SOCK_STREAM, 0);
            return $dummySocket;
        });
        $this->createMockDependenciesForClient();
    }

    private function createMockDependenciesForClient()
    {
        $this->client = new RedisClient($this->config);

        $this->adapterMock =$this->getMockBuilder(RedisStreamAdapter::class)
            ->setConstructorArgs(array("config"=>$this->config))
            ->getMock();


        $this->parserMock =$this->getMockBuilder(BasicRedisParser::class)
            ->setConstructorArgs(array("config"=>new RedisStreamAdapter($this->config)))
            ->getMock();

        $reflection = new \ReflectionClass($this->client);

        $connection = $reflection->getProperty('connection');
        $connection->setAccessible(true);
        $connection->setValue($this->client, $this->adapterMock);

        $parser = $reflection->getProperty('parser');
        $parser->setAccessible(true);
        $parser->setValue($this->client, $this->parserMock);
    }

    /**
     * @expectedException  RedisClient\Exception\MissingParamException
     */
    public function testNotPassingMandatoryFields()
    {
        new RedisClient(array());
    }

    /**
     * @expectedException  RedisClient\Exception\MissingParamException
     */
    public function testNotPassingHost()
    {
        new RedisClient(array('port'=>"123"));
    }

    /**
     * @expectedException  RedisClient\Exception\MissingParamException
     */
    public function testNotPassingPort()
    {
        new RedisClient(array('host'=>"host"));
    }

    public function testGeneratingSetCommands()
    {

        $this->adapterMock->expects($this->once())
                    ->method('write')
                    ->with($this->equalTo("SET foo bar \r\n"));


        $this->parserMock->expects($this->once())
            ->method('parse');

        $this->client->set("foo", "bar");
    }

    public function testGeneratingGetCommands()
    {
        $this->adapterMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo("GET foo \r\n"));

        $this->parserMock->expects($this->once())
            ->method('parse');

        $this->client->get("foo");
    }

    public function testGeneratingSetArrayCommands()
    {
        $this->adapterMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo("sadd array 1 2 3\r\n"));

        $this->parserMock->expects($this->once())
            ->method('parse');

        $this->client->setArray("array", array(1, 2, 3));
    }

    public function testGeneratingGetArrayCommands()
    {
        $this->adapterMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo("smembers array \r\n"));

        $this->parserMock->expects($this->once())
            ->method('parse');

        $this->client->getArray("array");
    }
}
