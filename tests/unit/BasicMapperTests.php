<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\BasicMapper;
use Rad\Db\Resources\TestTableEntity;

class BasicMapperTests extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructThrowsIfEntityClassPathIsNotValid()
    {
        new BasicMapper('stdClass');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEntityClassPathSetterThrowsIfNewValueIsNotValid()
    {
        $mapper = new BasicMapper(TestTableEntity::class);
        $mapper->setEntityClassPath('stdClass');
    }

    public function testMapInstantiatesAndMapsDataToEntityClass()
    {
        $input = ['somecol' => 'a value', 'number' => 45];
        $mapper = new BasicMapper(TestTableEntity::class);
        // Execute
        $result = $mapper->map($input);
        // Assert
        $this->assertInstanceOf(TestTableEntity::class, $result);
        $this->assertEquals($input['somecol'], $result->getSomecol());
        $this->assertEquals($input['number'], $result->getNumber());
    }

    public function testMapUsesSettersToMapValues()
    {
        $input = ['somecol' => 'a value', 'number' => '45', 'junk' => 'afo'];
        $mapper = new BasicMapper(TestTableEntity::class);
        // Execute
        $result = $mapper->map($input);
        // Assert
        $this->assertInstanceOf(TestTableEntity::class, $result);
        $expected = new TestTableEntity();
        $expected->setSomecol($input['somecol']);
        $expected->setNumber($input['number']);
        $this->assertEquals($expected->getSomecol(), $result->getSomecol());
        $this->assertEquals($expected->getNumber(), $result->getNumber());
    }

    public function testMapAllInstantiatesAndMapsAnArrayOfItems()
    {
        $inputs = [
            ['somecol' => 'a value', 'number' => 45],
            ['somecol' => 'a valyr', 'number' => 46]
        ];
        $mapper = new BasicMapper(TestTableEntity::class);
        // Execute
        $results = $mapper->mapAll($inputs);
        // Assert
        $this->assertCount(2, $results);
        $this->assertInstanceOf(TestTableEntity::class, $results[0]);
        $this->assertEquals($inputs[0]['somecol'], $results[0]->getSomecol());
        $this->assertEquals($inputs[0]['number'], $results[0]->getNumber());
        $this->assertInstanceOf(TestTableEntity::class, $results[1]);
        $this->assertEquals($inputs[1]['somecol'], $results[1]->getSomecol());
        $this->assertEquals($inputs[1]['number'], $results[1]->getNumber());
    }

    public function testMapAllAlwaysReturnsAnArrayOfMappedItems()
    {
        $inputs = ['somecol' => 'a value', 'number' => 45];
        $mapper = new BasicMapper(TestTableEntity::class);
        // Execute
        $results = $mapper->mapAll($inputs);
        // Assert
        $this->assertCount(1, $results, 'Should be 1 [0], not 2 ([somecol, number])');
        $this->assertInstanceOf(TestTableEntity::class, $results[0]);
        $this->assertEquals($inputs['somecol'], $results[0]->getSomecol());
        $this->assertEquals($inputs['number'], $results[0]->getNumber());
    }

    public function testMapSetsOmitListToMappedItem()
    {
        $omitList = ['id'];
        $mapper = new BasicMapper(TestTableEntity::class);
        // Execute
        $result = $mapper->map(['somedata' =>'afoo'], $omitList);
        // Assert
        $this->assertEquals($omitList, $result->__omit);
    }
}
