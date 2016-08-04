<?php
namespace DrestCommon\Error\Handler;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * The response HTTP status code
     * @var integer $response_code - defaults to 500
     */
    private $response_code = 500;

    /**
     * An array of error messages
     * @var array $error_messages
     */
    private $error_messages = [];

    /**
     * Get the response code
     * @return integer $response_code
     */
    final public function getResponseCode()
    {
        return (int)$this->response_code;
    }

    /**
     * Set a response code
     * @param $code
     */
    final public function setResponseCode($code)
    {
        $this->response_code = (int) $code;
    }

    /**
     * Get registered error messages
     * @return array
     */
    final public function getErrorMessages()
    {
        return $this->error_messages;
    }

    /**
     * Add an error message to the stack
     * @param $message
     */
    final public function addErrorMessage($message)
    {
        $this->error_messages[] = (string) $message;
    }

    /**
     * Reset the error messages array
     */
    final public function resetErrorMessages()
    {
        $this->error_messages = [];
    }
}