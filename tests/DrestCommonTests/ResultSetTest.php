<?php
namespace DrestCommonTests;


use DrestCommon\Representation\Xml;
use DrestCommon\ResultSet;


class ResultSetTest extends DrestCommonTestCase
{

    public function testResultSetConstruction()
    {
        $resultSet = ResultSet::create(array('part1', 'part2', 'part3'), 'parts');
        self::assertInstanceOf('DrestCommon\ResultSet', $resultSet);

        $refl = new \ReflectionClass('DrestCommon\ResultSet');
        self::assertTrue($refl->getConstructor()->isPrivate());
    }

    /**
     * @expectedException \Exception
     */
    public function testResultSetThrowExceptionWithObjectKeyname()
    {
        $resultSet = ResultSet::create(array('part1', 'part2', 'part3'), new \StdClass);
    }

    /**
     * @expectedException \Exception
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
        foreach ($resultSet as $part) {
            self::assertEquals($partsArray[$x], $part);
            $x++;
        }

        $rsIterator = $resultSet->getIterator();
        self::assertInstanceOf('ArrayIterator', $rsIterator);
        $rsIterator->rewind();
        self::assertEquals($partsArray[0], $rsIterator->current());
        $rsIterator->next();
        self::assertEquals($partsArray[1], $rsIterator->current());
    }

    public function testResultSetCountable()
    {
        $partsArray = array('part1', 'part2', 'part3');
        $resultSet = ResultSet::create($partsArray, 'parts');

        self::assertCount(3, $resultSet);
    }

    public function testResultSetUnset()
    {
        $partsArray = array('part1', 'part2', 'part3');
        $resultSet = ResultSet::create($partsArray, 'parts');

        unset($resultSet[999]);

        unset($resultSet[0]);
        self::assertCount(2, $resultSet);
        self::assertFalse(($resultSet[0] == 'part1'));
    }

    public function testResultSetSet()
    {
        $partsArray = array('part1', 'part2', 'part3');
        $resultSet = ResultSet::create($partsArray, 'parts');

        $resultSet[] = 'part4';
        self::assertCount(4, $resultSet);
        self::assertTrue(($resultSet[3] == 'part4'));

        $resultSet[1] = 'newpart2';
        self::assertCount(4, $resultSet);
        self::assertTrue(($resultSet[1] == 'newpart2'));
    }

    public function testResultSetOffsetExists()
    {
        $partsArray = array('a' => 'part1', 'b' => 'part2', 'c' => 'part3');
        $resultSet = ResultSet::create($partsArray, 'parts');

        self::assertEquals('part2', $resultSet['b']);

        self::assertTrue(isset($resultSet['a']));
    }

    public function testCreatingResultSetWithNullKeyname()
    {
        $resultSet = ResultSet::create([1, 2, 3]);
    }
}