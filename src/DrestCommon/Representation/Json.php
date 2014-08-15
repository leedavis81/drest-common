<?php
namespace DrestCommon\Representation;

use DrestCommon\ResultSet;
/**
 * Server implementation of the JSON representation
 * @author Lee
 */
class Json extends AbstractRepresentation
{
    /**
     * default error response document when handling an error
     * @var string $defaultErrorResponseClass
     */
    protected $defaultErrorResponseClass = 'DrestCommon\\Error\\Response\\Json';

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::write()
     */
    public function write(ResultSet $data)
    {
        $dataArray = $data->toArray();
        $this->formatData($dataArray);
        $this->data = json_encode($dataArray);
    }

    protected function formatData(&$parts)
    {
        foreach ($parts as &$part) {
            if (is_array($part)) {
                $this->formatData($part);
            }
            if (is_object($part)) {
                // Catch toString objects, and datetime. Note Closure's will fall into here
                if (method_exists($part, '__toString')) {
                    $part = $part->__toString();
                } elseif ($part instanceof \DateTime) {
                    $part = $part->format(\DateTime::ISO8601);
                } else {
                    throw new \Exception('Invalid object type used in JSON conversion. Must be instance of \DateTime or implement __toString()');
                }
            }
        }
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::toArray()
     */
    public function toArray($includeKey = true)
    {
        if (empty($this->data)) {
            throw new \Exception('Json data hasn\'t been loaded. Use either ->write() or ->createFromString() to create it');
        }

        $arrayData = json_decode($this->data, true);
        if (!$includeKey && sizeof($arrayData) == 1 && is_string(key($arrayData))) {
            return $arrayData[key($arrayData)];
        }
        return $arrayData;
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::createFromString($string)
     */
    public static function createFromString($string)
    {
        $instance = new self();
        $instance->data = $string;
        return $instance;
    }

    /**
     * Content type to be used when this writer is matched
     * @return string content type
     */
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::getMatchableAcceptHeaders()
     */
    public function getMatchableAcceptHeaders()
    {
        return array(
            'application/json',
            'application/x-javascript',
            'text/javascript',
            'text/x-javascript',
            'text/x-json'
        );
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::getMatchableExtensions()
     */
    public function getMatchableExtensions()
    {
        return array(
            'json'
        );
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::getMatchableFormatParams()
     */
    public function getMatchableFormatParams()
    {
        return array(
            'json'
        );
    }
}