<?php

namespace RedisClient\Connection;

interface IConnection
{

    public function write($data);

    public function read($length = 8192);

    public function readLine();
}
