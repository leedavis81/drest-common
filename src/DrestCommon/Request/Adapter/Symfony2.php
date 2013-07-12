<?php
namespace DrestCommon\Request\Adapter;

use DrestCommon\Request\RequestException;

class Symfony2 extends AdapterAbstract
{

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getAdpatedClassName()
     */
    public static function getAdaptedClassName()
    {
        return 'Symfony\Component\HttpFoundation\Request';
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getHttpMethod()
     */
    public function getHttpMethod()
    {
        $const = 'METHOD_' . $this->getRequest()->getMethod();
        if (!defined('Drest\Request::' . $const)) {
            throw RequestException::unknownHttpVerb(get_class($this));
        }
        return constant('Drest\Request::' . $const);
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getBody()
     */
    public function getBody()
    {
        return $this->getRequest()->getContent();
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getCookie()
     */
    public function getCookie($name = null)
    {
        if ($name === null) {
            return $this->getRequest()->cookies->all();
        }
        if ($this->getRequest()->cookies->has($name)) {
            return $this->getRequest()->cookies->get($name);
        }
        return '';
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getHeaders()
     */
    public function getHeaders($name = null)
    {
        if ($name === null) {
            return $this->getRequest()->headers->all();
        }
        if ($this->getRequest()->headers->has($name)) {
            return $this->getRequest()->headers->get($name);
        }
        return '';
    }


    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::setPost()
     */
    public function setPost($name, $value = null)
    {
        if (is_array($name)) {
            $this->getRequest()->request->replace($name);
        } else {
            $this->getRequest()->request->set($name, $value);
        }
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getPost()
     */
    public function getPost($name = null)
    {
        if ($name === null) {
            return $this->getRequest()->request->all();
        }
        if ($this->getRequest()->request->has($name)) {
            return $this->getRequest()->request->get($name);
        }
        return '';
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getQuery()
     */
    public function getQuery($name = null)
    {
        if ($name === null) {
            return $this->getRequest()->query->all();
        }
        if ($this->getRequest()->query->has($name)) {
            return $this->getRequest()->query->get($name);
        }
        return '';
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::setQuery()
     */
    public function setQuery($name, $value = null)
    {
        if (is_array($name)) {
            $this->getRequest()->query->replace($name);
        } else {
            $this->getRequest()->query->set($name, $value);
        }
    }

    /**
     * @see \DrestCommon\Request\Adapter\AdapterInterface::getUri()
     */
    public function getUri()
    {
        return $this->getRequest()->getUri();
    }

    /**
     * Symfony 2 Request object
     * @return \Symfony\Component\HttpFoundation\Request $request
     */
    public function getRequest()
    {
        return $this->request;
    }
}