<?php
namespace DrestCommon\Request;


class RequestException extends \Exception
{
    public static function unknownAdapterForRequestObject($object)
    {
        return new self('Unknown / Not yet created adapter for request object ' . get_class($object));
    }

    public static function invalidRequestObjectPassed()
    {
        return new self('Request object passed in is invalid (not type of object)');
    }

    public static function noRequestObjectDefinedAndCantInstantiateDefaultType($className)
    {
        return new self('No request object has been passed, and cannot instantiate the default request object: ' . $className . ' ensure this class is setup on your autoloader');
    }

    public static function unknownHttpVerb($className)
    {
        return new self('Unable to determine a valid HTTP verb from request adapter ' . $className);
    }
}