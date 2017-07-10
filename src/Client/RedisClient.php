<?php

namespace RedisClient\Client;

use RedisClient\Connection\AbstractConnection;
use RedisClient\Connection\RedisConnectionFactory;
use RedisClient\Exception\MissingParamException;
use RedisClient\Parsers\AbstractRedisParser;
use RedisClient\Parsers\ParserFactory;

class RedisClient
{

    /**
     * @var AbstractConnection
     */
    private $connection;

    /**
     * @var AbstractRedisParser
     */
    private $parser;

    public function __construct(array $config)
    {
        $this->validateConfig($config);
        $this->connection = RedisConnectionFactory::getConnection($config);
        $this->parser = ParserFactory::getParser(ParserFactory::PARSER_TYPE_BASIC, $this->connection);
    }


    private function validateConfig($config)
    {
        $mandatory_configs = array('host', 'port');
        foreach ($mandatory_configs as $config_key) {
            if (!array_key_exists($config_key, $config)) {
                throw new MissingParamException(" {$config_key} is missing ");
            }
        }
    }

    public function set($key, $value)
    {
        $command = "SET {$key} {$value} \r\n";
        $this->connection->write($command);
        $result = $this->parser->parse();
        if ($result == 'OK') {
            return true;
        }
        return false;
    }

    public function setArray($key, $value)
    {
        $command = "sadd {$key} ".implode($value, " "). "\r\n";
        $this->connection->write($command);
        $result = $this->parser->parse();
        if ($result == count($value)) {
            return true;
        }
        return false;
    }

    public function get($key)
    {
        $command = "GET {$key} \r\n";
        $this->connection->write($command);
        return $this->parser->parse();
    }

    public function getArray($key)
    {
        $command = "smembers $key \r\n";
        $this->connection->write($command);
        return $this->parser->parse();
    }
}
