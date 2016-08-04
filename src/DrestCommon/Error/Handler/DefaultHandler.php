<?php
namespace DrestCommon\Error\Handler;

use DrestCommon\Error\Response\ResponseInterface;
use DrestCommon\Response\Response;

/**
 * Default error handler class
 * These can be customised by creating your own handler that extends the AbstractHandler class
 * These should only be provided with the failure exception
 * @author Lee
 *
 */
class DefaultHandler extends AbstractHandler
{

    public function error(\Exception $e, $defaultResponseCode = 500, ResponseInterface &$errorDocument)
    {
        if (!$this->processException($e))
        {
            $this->setResponseCode($defaultResponseCode);
            $this->addErrorMessage('An unknown error occurred');
        }

        $errorDocument->setMessages($this->getErrorMessages());
    }

    /**
     * Process an application exception
     * @param \Exception $e
     * @return string
     */
    private function processException(\Exception $e)
    {
        /**
         * results exceptions
         * ORM\NonUniqueResultException
         * ORM\NoResultException
         * ORM\OptimisticLockException
         * ORM\PessimisticLockException
         * ORM\TransactionRequiredException
         * ORM\UnexpectedResultException
         */
        if ($e instanceof \Doctrine\ORM\NonUniqueResultException)
        {
            $this->setResponseCode(Response::STATUS_CODE_300);
            $this->addErrorMessage('Multiple resources available');
            return true;
        }

        if ($e instanceof \Doctrine\ORM\NoResultException)
        {
            $this->setResponseCode(Response::STATUS_CODE_404);
            $this->addErrorMessage('No resource available');
            return true;
        }

        /**
         * configuration / request exception
         * Drest\Route\MultipleRoutesException
         */
        if ($e instanceof \Drest\Query\InvalidExposeFieldsException)
        {
            $this->setResponseCode(Response::STATUS_CODE_400);
            $this->addErrorMessage($e->getMessage());
            return true;
        }

        if ($e instanceof \Drest\Route\NoMatchException)
        {
            $this->setResponseCode(Response::STATUS_CODE_404);
            $this->addErrorMessage($e->getMessage());
            return true;
        }

        if ($e instanceof \Drest\Service\Action\ActionException)
        {
            $this->addErrorMessage($e->getMessage());
            return true;
        }

        if ($e instanceof \DrestCommon\Representation\UnableToMatchRepresentationException)
        {
            $this->setResponseCode(Response::STATUS_CODE_415);
            $this->addErrorMessage('Requested media type is not supported');
            return true;
        }
        return false;
    }
}