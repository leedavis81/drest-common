<?php
namespace DrestTests\Request;


use DrestCommon\Request\Request;
use DrestCommonTests\DrestCommonTestCase;
use Symfony\Component\HttpFoundation;
use Zend\Http;

class RequestTest extends DrestCommonTestCase
{

    public function testCreateRequest()
    {
        $request = new Request();

        // Ensure default request object creates a symfony request
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request->getRequest());
    }

    public function testStaticCreateRequest()
    {
        $request = Request::create();
    }

    public function testCreateRequestWithZendFramework2RequestObject()
    {
        $zfRequest = new Http\Request();
        $request = Request::create($zfRequest, array('DrestCommon\\Request\\Adapter\\ZendFramework2'));

        // Ensure request object creates a zf2 request
        $this->assertInstanceOf('Zend\Http\Request', $request->getRequest());
    }

    public function testCreateRequestWithSymfony2RequestObject()
    {
        $symRequest = new HttpFoundation\Request();
        $request = Request::create($symRequest, array('DrestCommon\\Request\\Adapter\\Symfony2'));

        // Ensure request object creates a symfony2 request
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request->getRequest());
    }
}

