<?php

namespace RedisClient\Parsers;

use RedisClient\Exception\EmptyResponseException;
use RedisClient\Exception\UnKnownResponseTypeException;

class BasicRedisParser extends AbstractRedisParser
{
    public function parse()
    {
        $metaData = $this->connection->readLine();
        if (!$metaData) {
            throw new EmptyResponseException('Empty response, didn\'t get anything from Redis');
        }

        $responseType = $metaData[0];
        // get the rest of the response by ignoring the first character which is the type
        // and the last two characters which are the new line
        $response = substr($metaData, 1, -2);

        switch ($responseType) {
            //simplest case is the simple string
            case self::RESPONSE_TYPE_SIMPLE_STRING:
                return $this->parseSimpleString($response);

            // handle integers
            case self::RESPONSE_TYPE_INTEGERS:
                return (int)$response;

            // handle bulk of strings
            case self::RESPONSE_TYPE_BULK_STRINGS:
                return $this->parseBulkStrings($response);

            // handle arrays
            case self::RESPONSE_TYPE_ARRAYS:
                return $this->parseArrays($response);

            // in case of error , throw a generic exception with the error message we got from redis
            case self::RESPONSE_TYPE_ERROR:
                throw new \Exception($response);
        }

        throw new UnKnownResponseTypeException("Coudln't recognize the response type : {$responseType} ");
    }

    private function parseSimpleString($response)
    {
        if ($response !== self::RESPONSE_OK) {
            return $response;
        }
        return true;
    }

    private function parseBulkStrings($response)
    {
        // in this case the response first line will have the length of the string
        $length = (int)$response;
        if ($length !== -1) {
            // have to read to the end of the stream, so for the next read the \r\n won't affect the next read
            $content = $this->connection->read($length + 2);
            if ($content!=null) {
                // remove the \r\n from the content, don't need them any more
                return substr($content, 0, -2);
            }
            throw new EmptyResponseException('Couldn\'t Read the Response, it seems the connect reset or timed out');
        }
        return null;
    }

    private function parseArrays($response)
    {
        $numElements = (int)$response;
        if ($numElements > 0) {
            $result  = array();
            for ($i=0; $i < $numElements; ++$i) {
                $result[] = $this->parse();
            }
            return $result;
        }
        return null;
    }
}
