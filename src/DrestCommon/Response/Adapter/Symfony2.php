<?php
namespace DrestCommon\Response\Adapter;

class Symfony2 extends AdapterAbstract
{
    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::__toString()
     */
    public function toString()
    {
        $this->getResponse()->sendHeaders();
        return $this->getResponse()->getContent();
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::getAdaptedClassName()
     */
    public static function getAdaptedClassName()
    {
        return 'Symfony\Component\HttpFoundation\Response';
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::getHttpHeader()
     */
    public function getHttpHeader($name = null)
    {
        if ($name !== null) {
            return $this->getResponse()->headers->get($name);
        }

        if (($this->getResponse()->headers->count() === 0)) {
            return array();
        } else {
            return array_map(
                function ($item) {
                    return implode(', ', $item);
                },
                $this->getResponse()->headers->all()
            );
        }
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::setHttpHeader()
     */
    public function setHttpHeader($name, $value = null)
    {
        $value = (array)$value;
        if (is_array($name)) {
            foreach ($this->getResponse()->headers->all() as $key => $value) {
                $this->getResponse()->headers->remove($key);
            }
            $this->getResponse()->headers->add($name);
        } else {
            $this->getResponse()->headers->set($name, $value);
        }
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::getBody()
     */
    public function getBody()
    {
        return $this->getResponse()->getContent();
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::setBody()
     */
    public function setBody($body)
    {
        $this->getResponse()->setContent($body);
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::getStatusCode()
     */
    public function getStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    /**
     * @see \DrestCommon\Response\Adapter\AdapterInterface::setStatusCode()
     */
    public function setStatusCode($code, $text)
    {
        $this->getResponse()->setStatusCode($code, $text);
    }

    /**
     * Symfony 2 Response object
     * @return \Symfony\Component\HttpFoundation\Response $response
     */
    public function getResponse()
    {
        return $this->response;
    }
}