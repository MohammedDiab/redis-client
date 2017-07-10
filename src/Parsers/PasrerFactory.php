<?php

namespace RedisClient\Parsers;

use RedisClient\Connection\AbstractConnection;

class ParserFactory
{
    const PARSER_TYPE_BASIC = 1;

    public static function getParser($type, AbstractConnection $connection)
    {
        switch ($type) {
            case self::PARSER_TYPE_BASIC:
                return new BasicRedisParser($connection);
                break;
            default:
                return new BasicRedisParser($connection);
        }
    }
}
