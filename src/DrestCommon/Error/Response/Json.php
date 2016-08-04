<?php
namespace DrestCommon\Error\Response;

/**
 * ApiProblem Document (Json)
 * @author Lee
 */
class Json implements ResponseInterface
{
    /**
     * The error message
     * @var string $message
     */
    public $messages;

    /**
     * @see \DrestCommon\Error\Response\ResponseInterface::setMessage()
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return array $message
     */
    public function getMessage()
    {
        return $this->messages;
    }

    /**
     * @see \DrestCommon\Error\Response\ResponseInterface::render()
     */
    public function render()
    {
        return json_encode(
            array('error' => $this->messages)
        );
    }

    /**
     * @see \DrestCommon\Error\Response\ResponseInterface::getContentType()
     */
    public static function getContentType()
    {
        return 'application/json';
    }

    /**
     * Every error document you should be able to recreate from the generated string
     * @param string $string
     * @return Json $errorResponse
     */
    public static function createFromString($string)
    {
        $result = json_decode($string, true);
        $instance = new self();
        if (isset($result['error'])) {
            $instance->setMessages($result['error']);
        }
        return $instance;
    }
}