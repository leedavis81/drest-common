<?php
namespace DrestCommonTests\Response;


use DrestCommon\Response\Response;
use DrestCommonTests\DrestCommonTestCase;

class ResponseTest extends DrestCommonTestCase
{

    public function testCreateResponse()
    {
        $response = new Response();

        // Ensure default response object creates a symfony response
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response->getResponse());
    }

    public function testStaticCreateResponse()
    {
        $response = Response::create();

        // Ensure default response object creates a symfony response
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response->getResponse());
    }
}

