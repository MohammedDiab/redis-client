<?php

require_once '../../vendor/autoload.php';


$client = new \RedisClient\Client\RedisClient(array('host'=>'127.0.0.1', 'port'=>'6379', 'timeout'=>30));

$client->set("name", "'mohamed diab'");
var_dump($client->get("name"));


$client->setArray('list', array(1,2,3));
var_dump($client->getArray('list'));

$client->set("foo", "bar");
var_dump($client->get("foo"));
