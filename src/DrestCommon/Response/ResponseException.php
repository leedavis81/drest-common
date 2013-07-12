<?php
namespace DrestCommon\Response;


class ResponseException extends \Exception
{
    public static function unknownAdapterForResponseObject($object)
    {
        return new self('Unknown / Not yet created adapter for response object ' . get_class($object));
    }

    public static function invalidResponseObjectPassed()
    {
        return new self('Response object passed in is invalid (not type of object)');
    }

    public static function noResponseObjectDefinedAndCantInstantiateDefaultType($className)
    {
        return new self('No response object has been passed, and cannot instantiate the default response object: ' . $className . ' ensure this class is setup on your autoloader');
    }

    public static function invalidHttpStatusCode($code)
    {
        return new self('Invalid HTTP Status code used "' . $code . '"');
    }
}