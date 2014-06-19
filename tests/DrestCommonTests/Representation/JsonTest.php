<?php
namespace DrestCommonTests\Representation;

use DrestCommon\Representation\Json;
use DrestCommon\Request\Request;
use DrestCommon\ResultSet;
use DrestCommonTests\DrestCommonTestCase;

class JsonTest extends DrestCommonTestCase
{

    protected function getJsonString()
    {
        return '{"user":{"username":"leedavis81","email_address":"lee.davis@somewhere.com","profile":{"id":"1","title":"mr","firstname":"lee","lastname":"davis"},"phone_numbers":[{"id":"1","number":"2087856458"},{"id":"2","number":"2087865978"},{"id":"3","number":"2074855978"}]}}';
    }

    protected function getJsonArray()
    {
        return array(
            'user' => array(
                'username' => 'leedavis81',
                'email_address' => 'lee.davis@somewhere.com',
                'profile' => array(
                    'id' => '1',
                    'title' => 'mr',
                    'firstname' => 'lee',
                    'lastname' => 'davis',
                ),
                'phone_numbers' => array(
                    array(
                        'id' => '1',
                        'number' => '2087856458'
                    ),
                    array(
                        'id' => '2',
                        'number' => '2087865978'
                    ),
                    array(
                        'id' => '3',
                        'number' => '2074855978'
                    )
                )
            )
        );
    }

    public function testArrayToJsonMatches()
    {
        $representation = new Json();
        $array = $this->getJsonArray();
        $resultSet = ResultSet::create($array['user'], 'user');

        $this->assertInstanceOf('DrestCommon\Representation\Json', $representation);

        $this->assertEquals($this->getJsonString(), $representation->output($resultSet));
    }

    public function testJsonToArrayMatches()
    {
        $representation = Json::createFromString($this->getJsonString());

        $this->assertInstanceOf('DrestCommon\Representation\Json', $representation);

        $this->assertEquals($this->getJsonArray(), $representation->toArray());
    }

    public function testJsonRepCanBeUpdated()
    {
        $representation = Json::createFromString($this->getJsonString());

        $obj = new \StdClass();
        $obj->username = 'billybob';
        $obj->email_address = 'billybob@somewhere.com';
        $obj->phone_numbers = array('02045485658', '02096589654');
        $representation->update($obj);

        $exp = '{"stdclass":{"username":"billybob","email_address":"billybob@somewhere.com","phone_numbers":["02045485658","02096589654"]}}';

        $this->assertEquals($exp, $representation->__toString());
    }

    public function testGetDefaultErrorResponseObject()
    {
        $representation = Json::createFromString($this->getJsonString());
        $errorResp = $representation->getDefaultErrorResponse();
        $this->assertInstanceOf('DrestCommon\Error\Response\ResponseInterface', $errorResp);
    }

    public function testIsExpectedContentFromExtension()
    {
        $representation = Json::createFromString($this->getJsonString());

        $symRequest1 = \Symfony\Component\HttpFoundation\Request::create(
            '/users.json',
            'GET'
        );
        $request1 = Request::create($symRequest1);
        $this->assertTrue($representation->isExpectedContent(array(2 => true), $request1));

        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET'
        );
        $request2 = Request::create($symRequest);
        $this->assertFalse($representation->isExpectedContent(array(2 => true), $request2));
    }

    public function testIsExpectedContentFromHeader()
    {
        $representation = Json::createFromString($this->getJsonString());

        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET',
            array(),
            array(),
            array(),
            array('HTTP_ACCEPT' => $representation->getContentType())
        );

        $request1 = Request::create($symRequest);
        $this->assertTrue($representation->isExpectedContent(array(1 => 'Accept'), $request1));

        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET'
        );

        $request2 = Request::create($symRequest);
        $this->assertFalse($representation->isExpectedContent(array(1 => 'Accept'), $request2));
    }

    public function testIsExpectedContentFromParams()
    {
        $representation = Json::createFromString($this->getJsonString());

        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET',
            array('format' => 'json')
        );
        $request1 = Request::create($symRequest);
        $this->assertTrue($representation->isExpectedContent(array(3 => 'format'), $request1));

        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET'
        );

        $request2 = Request::create($symRequest);
        $this->assertFalse($representation->isExpectedContent(array(3 => 'format'), $request2));
    }


    /**
     * @expectedException \Exception
     */
    public function testToArrayWithNoData()
    {
        $rep = new Json();
        $rep->toArray();
    }

    public function testDataWithDateTimeObject()
    {
        $dts = '2013-08-05T14:12:46+0100';
        $date = new \DateTime($dts);
        $data = array('date' => $date);

        $resp = '{"user":{"date":"' . $dts . '"}}';

        $representation = new Json();
        $representation->write(ResultSet::create($data, 'user'));

        $this->assertEquals($resp, $representation->__toString());
    }


    /**
     * @expectedException \Exception
     */
    public function testDataWithClosure()
    {
        $data = array(
            'closure' => function () {
                }
        );
        $representation = new Json();
        $representation->write(ResultSet::create($data, 'user'));
    }


    public function testDataWithToStringObject()
    {
        $obj = new ToStringClass();
        $data = array('obj' => $obj);
        $resp = '{"user":{"obj":"' . $obj->__toString() . '"}}';

        $representation = new Json();
        $representation->write(ResultSet::create($data, 'user'));

        $this->assertEquals($resp, $representation->__toString());
    }

    /**
     * @expectedException \Exception
     */
    public function testDataWithNoToStringObject()
    {
        $obj = new \StdClass();
        $data = array('obj' => $obj);

        $representation = new Json();
        $representation->write(ResultSet::create($data, 'user'));
    }

}