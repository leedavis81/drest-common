<?php
namespace DrestTests\Request\Adapter;


use DrestCommon\Request\Request;
use DrestCommonTests\DrestCommonTestCase;
use Zend\Http;

class ZendFramework2Test extends DrestCommonTestCase
{

    /**
     * Get an instance of the request object with a zf2 adapter used
     * @return Request;
     */
    public static function getZF2AdapterRequest()
    {
        $zfRequest = new Http\Request();
        $request = Request::create($zfRequest, array('DrestCommon\\Request\\Adapter\\ZendFramework2'));
        return $request;
    }

    public function testCanSaveAndRetrieveHttpVerb()
    {
        $request = self::getZF2AdapterRequest();

        $method = 'OPTIONS';
        $zf2RequestObject = $request->getRequest();
        $zf2RequestObject->setMethod($method);

        $this->assertEquals($method, $request->getHttpMethod());
    }

    public function testCanSaveAndRetrieveCookie()
    {
        $cookieName = 'frodo';
        $cookieValue = 'baggins';
        $httpString = "GET /foo HTTP/1.1\r\nCookie: $cookieName=$cookieValue\r\nAccept: */*\r\n";

        $zfRequest = Http\Request::fromString($httpString);
        $request = Request::create($zfRequest, array('DrestCommon\\Request\\Adapter\\ZendFramework2'));

        $this->assertNotEmpty($request->getCookie());
        $this->assertCount(1, $request->getCookie());
        $this->assertEquals($cookieValue, $request->getCookie($cookieName));

        $this->assertEquals('', $request->getCookie('notset'));

        $httpString = "GET /foo HTTP/1.1\r\nAccept: */*\r\n";

        $zfRequest = Http\Request::fromString($httpString);
        $request = Request::create($zfRequest, array('DrestCommon\\Request\\Adapter\\ZendFramework2'));
        $this->assertEmpty($request->getCookie());

    }


    public function testCanSaveAndRetrievePostVars()
    {
        $request = self::getZF2AdapterRequest();

        $varName = 'frodo';
        $varValue = 'baggins';

        $request->setPost($varName, $varValue);
        $this->assertNotEmpty($request->getPost());
        $this->assertCount(1, $request->getPost());
        $this->assertEquals($varValue, $request->getPost($varName));

        $newValues = array('samwise' => 'gamgee', 'peregrin' => 'took');
        $request->setPost($newValues);
        $this->assertCount(2, $request->getPost());

        $request->setPost(array());
        $this->assertCount(0, $request->getPost());
        $this->assertEquals('', $request->getPost($varName));
    }

    public function testCanSaveAndRetrieveQueryVars()
    {
        $request = self::getZF2AdapterRequest();

        $varName = 'frodo';
        $varValue = 'baggins';

        $request->setQuery($varName, $varValue);
        $this->assertNotEmpty($request->getQuery());
        $this->assertCount(1, $request->getQuery());
        $this->assertEquals($varValue, $request->getQuery($varName));

        $newValues = array('samwise' => 'gamgee', 'peregrin' => 'took');
        $request->setQuery($newValues);
        $this->assertCount(2, $request->getQuery());

        $request->setQuery(array());
        $this->assertCount(0, $request->getQuery());
        $this->assertEquals('', $request->getQuery($varName));
    }

    public function testCanSaveAndRetrieveHeaderVars()
    {
        $request = self::getZF2AdapterRequest();

        $zf2RequestObject = $request->getRequest();

        $varName = 'frodo';
        $varValue = 'baggins';

        $header = new Http\Header\GenericHeader($varName, $varValue);
        $zf2RequestObject->getHeaders()->addHeader($header);

        $this->assertNotEmpty($request->getHeaders());
        $this->assertCount(1, $request->getHeaders());
        $this->assertEquals($varValue, $request->getHeaders($varName));

        $newValues = array('samwise' => 'gamgee', 'peregrin' => 'took');
        foreach ($newValues as $headerName => $headerValue) {
            $headers[] = new Http\Header\GenericHeader($headerName, $headerValue);
        }

        $zf2RequestObject->getHeaders()->clearHeaders();
        $this->assertEquals('', $request->getHeaders($varName));

        if (isset($headers))
        {
            $zf2RequestObject->getHeaders()->addHeaders($headers);
        }

        $this->assertCount(2, $request->getHeaders());
    }

    public function testCanSaveCombinedParamTypes()
    {
        $varName1 = 'frodo';
        $varValue1 = 'baggins';
        $httpString = "GET /foo HTTP/1.1\r\nCookie: $varName1=$varValue1\r\nAccept: */*\r\n";

        $zfRequest = Http\Request::fromString($httpString);
        $request = Request::create($zfRequest, array('DrestCommon\\Request\\Adapter\\ZendFramework2'));

        $varName2 = 'samwise';
        $varValue2 = 'gamgee';
        $request->setPost($varName2, $varValue2);
        $varName3 = 'peregrin';
        $varValue3 = 'took';
        $request->setQuery($varName3, $varValue3);
        $this->assertCount(3, $request->getParams());
        $this->assertArrayHasKey($varName2, $request->getParams());
        $varName4 = 'peregrin';
        $varValue4 = 'peanut';
        $request->setQuery($varName4, $varValue4);
        $this->assertCount(3, $request->getParams());
    }

    public function testCanFetchBody()
    {
        $body = '<span>This is the body string</span>';
        $httpString = "GET /foo HTTP/1.1\r\nAccept: */*\r\n\r\n$body";
        $zfRequest = Http\Request::fromString($httpString);
        $request = Request::create($zfRequest, array('DrestCommon\\Request\\Adapter\\ZendFramework2'));

        $this->assertEquals($body, $request->getBody());
    }

}