<?php

namespace RedisClient\Connection;

abstract class AbstractConnection
{

    protected $config;

    protected $resource;

    abstract public function __construct($config);

    public function getResource()
    {
        return $this->resource;
    }
}
