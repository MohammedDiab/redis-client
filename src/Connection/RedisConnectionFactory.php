<?php

namespace RedisClient\Connection;

use RedisClient\Adapters\RedisStreamAdapter;

class RedisConnectionFactory
{

    private static $connections = array();

    public static function getConnection($config)
    {
        $serialized_config = self::serializeConfig($config);
        if (array_key_exists($serialized_config, self::$connections)) {
            return self::$connections[$serialized_config];
        } else {
            self::$connections[$serialized_config] = new RedisStreamAdapter($config);
            return self::$connections[$serialized_config];
        }
    }

    private static function serializeConfig($config)
    {
        return $config['host'].$config['port'];
    }
}
