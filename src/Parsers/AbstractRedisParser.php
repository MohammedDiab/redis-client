<?php

namespace RedisClient\Parsers;

use \RedisClient\Connection\AbstractConnection;
use RedisClient\Connection\IConnection;

abstract class AbstractRedisParser
{
    /**
     * @var IConnection
     */
    protected $connection ;

    const RESPONSE_TYPE_SIMPLE_STRING = '+';
    const RESPONSE_TYPE_BULK_STRINGS = '$';
    const RESPONSE_TYPE_INTEGERS = ':';
    const RESPONSE_TYPE_ARRAYS = '*';
    const RESPONSE_TYPE_ERROR = '-';
    const RESPONSE_OK = 'OK';

    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
    }

    abstract public function parse();
}
