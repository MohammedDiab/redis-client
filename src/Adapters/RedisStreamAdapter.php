<?php

namespace RedisClient\Connection;

use RedisClient\Exception\ConnectionFailedException;

class RedisStreamAdapter extends AbstractConnection implements IConnection
{

    private $host;

    private $port;

    private $timeout;

    public function __construct($config)
    {
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->timeout = $config['timeout'];

        $server = $this->host.":".$this->port;


        $this->resource = @stream_socket_client($server, $err_no, $err_str);
        if ($err_no) {
            throw new ConnectionFailedException("Cant Connect to {$server}, $err_str");
        }

        if (isset($this->timeout)) {
            $this->timeout = $this->timeout * 1000000 ;
            stream_set_timeout($this->resource, 0, $this->timeout);
        }
    }

    public function write($data)
    {
        fwrite($this->resource, $data, strlen($data));
    }

    public function readLine()
    {
        return fgets($this->resource);
    }

    public function read($length = 8192)
    {
        $resource = $this->getResource();
        $remaining = $length;
        $content = '';
        while ($remaining>0) {
            $line = fread($resource, min($remaining, 8192));
            if ($line === false) {
                return null;
            }
            $content.=$line;
            $remaining = $length - strlen($content);
        }
        return $content;
    }
}
