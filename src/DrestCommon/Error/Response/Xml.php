<?php
namespace DrestCommon\Error\Response;


/**
 * Error Document (Xml)
 * @author Lee
 */
class Xml implements ResponseInterface
{
    /**
     * The error message
     * @var array $message
     */
    public $messages;

    /**
     * @param array $messages
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
        $xml = new \DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('error');
        $xml->appendChild($root);

        $node = $xml->createElement('message', $this->getMessages());
        $root->appendChild($node);

        return $xml->saveXML();
    }

    /**
     * @see \DrestCommon\Error\Response\ResponseInterface::getContentType()
     */
    public static function getContentType()
    {
        return 'application/xml';
    }

    /**
     * recreate this error document from a generated string
     * @param string $string
     * @throws \Exception
     * @return Xml $errorResponse
     */
    public static function createFromString($string)
    {
        $instance = new self();
        $xml = new \DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        if (!$xml->loadXML($string)) {
            throw new \Exception('Unable to load XML document from string');
        }

        $instance->setMessages($xml->documentElement->textContent);
        return $instance;
    }
}