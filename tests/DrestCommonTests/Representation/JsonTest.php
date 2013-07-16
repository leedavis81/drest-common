<?php
namespace DrestTests\Representation;

use DrestCommon\ResultSet;
use DrestCommon\Representation\Json;
use DrestCommonTests\DrestCommonTestCase;

class JsonTest extends DrestCommonTestCase
{

    protected function getJsonString()
    {
        return '{"user":{"username":"leedavis81","email_address":"lee.davis@somewhere.com","profile":{"id":"1","title":"mr","firstname":"lee","lastname":"davis"},"phone_numbers":[{"id":"1","number":"2087856458"},{"id":"2","number":"2087865978"},{"id":"3","number":"2074855978"}]}}';
    }

    protected function getJsonArray()
    {
        return array('user' => array(
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
        ));
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
}