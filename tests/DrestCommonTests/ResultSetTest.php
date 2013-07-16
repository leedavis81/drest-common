<?php
namespace DrestTests;


use DrestCommon\ResultSet;
use DrestCommonTests\DrestCommonTestCase;


class ResultSetTest extends DrestCommonTestCase
{

    public function testResultSetConstruction()
    {
        $resultSet = ResultSet::create(array('part1', 'part2', 'part3'), 'parts');
        $this->assertInstanceOf('DrestCommon\ResultSet', $resultSet);

        $refl = new \ReflectionClass('DrestCommon\ResultSet');
        $this->assertTrue($refl->getConstructor()->isPrivate());
    }

    /**
     * @expectedException Exception
     */
    public function testResultSetThrowExceptionWithObjectKeyname()
    {
        $resultSet = ResultSet::create(array('part1', 'part2', 'part3'), new \StdClass);
    }

    /**
     * @expectedException Exception
     */
    public function testResultSetThrowExceptionWithArrayKeyname()
    {
        $resultSet = ResultSet::create(array('part1', 'part2', 'part3'), array(1, 2));
    }

    public function testResultSetIteration()
    {
        $partsArray = array('part1', 'part2', 'part3');
        $resultSet = ResultSet::create($partsArray, 'parts');

        $x = 0;
        foreach ($resultSet as $part)
        {
            $this->assertEquals($partsArray[$x], $part);
            $x++;
        }

        $rsIterator = $resultSet->getIterator();
        $this->assertInstanceOf('ArrayIterator', $rsIterator);
        reset($rsIterator);
        $this->assertEquals($partsArray[0], current($rsIterator));
        $this->assertEquals($partsArray[1], next($rsIterator));
    }


}