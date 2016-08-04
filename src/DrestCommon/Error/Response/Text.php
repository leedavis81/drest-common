<?php
namespace DrestCommon\Error\Response;

/**
 * ApiProblem Document (Json)
 * @author Lee
 */
class Text implements ResponseInterface
{
    /**
     * The error message
     * @var string $message
     */
    public $messages;

    /**
     * @see \DrestCommon\Error\Response.ResponseInterface::setMessage()
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return string $message
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @see \DrestCommon\Error\Response\ResponseInterface::render()
     */
    public function render()
    {
        return 'error: ' . implode(', ', $this->messages);
    }

    /**
     * @see \DrestCommon\Error\Response\ResponseInterface::getContentType()
     */
    public static function getContentType()
    {
        return 'text/plain';
    }

    /**
     * recreate this error document from a generated string
     * @param string $string
     * @return Xml $errorResponse
     */
    public static function createFromString($string)
    {
        $instance = new self();
        $parts = explode(':', $string);
        $instance->setMessages(explode(', ', $parts[1]));
        return $instance;
    }
}