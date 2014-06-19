<?php
namespace DrestCommonTests\Representation;

use DrestCommon\Representation\Xml;
use DrestCommon\Request\Request;
use DrestCommon\ResultSet;
use DrestCommonTests\DrestCommonTestCase;


class XmlTest extends DrestCommonTestCase
{

    protected function getXmlString($formatted = true)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<user>
  <username>leedavis81</username>
  <email_address>lee.davis@somewhere.com</email_address>
  <profile>
    <id>1</id>
    <title>mr</title>
    <firstname>lee</firstname>
    <lastname>davis</lastname>
  </profile>
  <phone_numbers>
    <phone_number>
      <id>1</id>
      <number>2087856458</number>
    </phone_number>
    <phone_number>
      <id>2</id>
      <number>2087865978</number>
    </phone_number>
    <phone_number>
      <id>3</id>
      <number>2074855978</number>
    </phone_number>
  </phone_numbers>
</user>';
        return ($formatted) ? $xml : $this->removeXmlFormatting($xml);
    }

    private function removeXmlFormatting($string)
    {
        return str_replace(array(" ", "\n", "\r"), '', $string);
    }

    protected function getXmlArray()
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

    public function testArrayToXmlMatches()
    {
        $representation = new Xml();
        $array = $this->getXmlArray();
        $resultSet = ResultSet::create($array['user'], 'user');

        $this->assertInstanceOf('DrestCommon\Representation\Xml', $representation);
        $this->assertEquals(
            $this->getXmlString(false),
            $this->removeXmlFormatting($representation->output($resultSet))
        );
    }

    public function testXmlToArrayMatches()
    {
        $representation = Xml::createFromString($this->getXmlString());

        $this->assertInstanceOf('DrestCommon\Representation\Xml', $representation);

        $this->assertEquals($this->getXmlArray(), $representation->toArray());
    }

    public function testIsExpectedContentFromExtension()
    {
        $representation = Xml::createFromString($this->getXmlString());

        $symRequest1 = \Symfony\Component\HttpFoundation\Request::create(
            '/users.xml',
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
        $representation = Xml::createFromString($this->getXmlString());

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

        // By default sym requests are created with accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET',
            array(),
            array(),
            array(),
            array('HTTP_ACCEPT' => '')
        );

        $request2 = Request::create($symRequest);
        $this->assertFalse($representation->isExpectedContent(array(1 => 'Accept'), $request2));
    }

    public function testIsExpectedContentFromParams()
    {
        $representation = Xml::createFromString($this->getXmlString());

        $symRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/users',
            'GET',
            array('format' => 'xml')
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
    public function testInvalidTagName()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<user>
  <1>leedavis81</1>
  <email_address>lee.davis@somewhere.com</email_address>
</user>';
        $representation = Xml::createFromString($xml);
    }

    /**
     * @expectedException \Exception
     */
    public function testToArrayWithNoData()
    {
        $rep = new Xml();
        $rep->toArray();
    }

    public function testDataWithDateTimeObject()
    {
        $dts = '2013-08-05T14:12:46+0100';
        $date = new \DateTime($dts);
        $data = array('date' => $date);

        $resp = '<?xml version="1.0" encoding="UTF-8"?><user><date>' . $dts . '</date></user>';

        $representation = new Xml();
        $representation->write(ResultSet::create($data, 'user'));

        $this->assertEquals(
            $this->removeXmlFormatting($resp),
            $this->removeXmlFormatting($representation->__toString())
        );
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
        $representation = new Xml();
        $representation->write(ResultSet::create($data, 'user'));
    }


    public function testDataWithToStringObject()
    {
        $obj = new ToStringClass();
        $data = array('obj' => $obj);
        $resp = '<?xml version="1.0" encoding="UTF-8"?><user><obj>' . $obj->__toString() . '</obj></user>';

        $representation = new Xml();
        $representation->write(ResultSet::create($data, 'user'));

        $this->assertEquals(
            $this->removeXmlFormatting($resp),
            $this->removeXmlFormatting($representation->__toString())
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testDataWithNoToStringObject()
    {
        $obj = new \StdClass();
        $data = array('obj' => $obj);

        $representation = new Xml();
        $representation->write(ResultSet::create($data, 'user'));
    }

}

class ToStringClass
{
    public function __toString()
    {
        return 'string representation';
    }
}