<?php
namespace DrestCommon\Representation;

use DrestCommon\Error\Response as ErrorResponse;
use DrestCommon\Request\Request;
use DrestCommon\ResultSet;

abstract class AbstractRepresentation implements InterfaceRepresentation
{
    /**
     * Stored Data representation
     * @var string $data
     */
    protected $data;

    /**
     * Error response document to be used. Can be overwritten from class extension
     * @var string
     */
    protected $defaultErrorResponseClass = 'DrestCommon\\Error\\Response\\Text';

    /**
     * Get the default error response object associated with this representation.
     * @return \DrestCommon\Error\Response\ResponseInterface $response
     */
    public function getDefaultErrorResponse()
    {
        if (class_exists($this->defaultErrorResponseClass)) {
            return new $this->defaultErrorResponseClass();
        }
        return new ErrorResponse\Text();
    }

    /**
     * update the representation to match the data contained within a client data object
     * - This will call the write method that will store its representation in the $data array
     * @param InterfaceRepresentation $object - the representation object
     * @param string $withKey - optional key to wrap the object in
     */
    public function update($object, $withKey = null)
    {
        if (is_object($object)) {
            $objectVars = get_object_vars($object);
            $this->repIntoArray($objectVars);

            $keyName = is_null($withKey)
                ? strtolower(implode('', array_slice(explode('\\', get_class($object)), -1)))
                : $withKey;

            $this->write(
                ResultSet::create(
                    $objectVars,
                    $keyName
                )
            );
        }
    }

    /**
     * Recurse the representation into an array
     * @param array $vars
     */
    protected function repIntoArray(array &$vars)
    {
        foreach ($vars as $key => $var) {
            if (is_array($var)) {
                $this->repIntoArray($vars[$key]);
            } elseif (is_object($var)) {
                $vars[$key] = get_object_vars($var);
                $this->repIntoArray($vars[$key]);
            }
        }
    }

    /**
     * Uses configuration options to determine whether this writer instance is the media type expected by the client
     * @param array $configOptions - configuration options for content detection
     * @param Request $request - request object
     * @return boolean $result
     */
    final public function isExpectedContent(array $configOptions, Request $request)
    {
        foreach ($configOptions as $detectContentOption => $detectContentValue) {
            switch ($detectContentOption) {
                case 1: // HTTP Header
                    $headers = explode(',', $request->getHeaders($detectContentValue));
                    foreach ($headers as $headerEntry) {
                        if (false !== ($pos = strpos($headerEntry, ';'))) {
                            $headerEntry = substr($headerEntry, 0, $pos);
                        }
                        // See if the header matches for this writer
                        if (in_array(trim($headerEntry), $this->getMatchableAcceptHeaders())) {
                            return true;
                        }
                    }
                    break;
                case 2: // Extension
                    // See if an extension has been supplied
                    $ext = $request->getExtension();
                    if (!empty($ext) && in_array($request->getExtension(), $this->getMatchableExtensions())) {
                        return true;
                    }
                    break;
                case 3: // Parameter
                    // Inspect the request object for a "format" parameter
                    if (in_array($request->getQuery($detectContentValue), $this->getMatchableFormatParams())) {
                        return true;
                    }
                    break;
            }
        }
        return false;
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::__toString()
     */
    public function __toString()
    {
        return $this->data;
    }

    /**
     * @see \DrestCommon\Representation\InterfaceRepresentation::output()
     */
    public function output(ResultSet $data)
    {
        $this->write($data);
        return $this->__toString();
    }
}