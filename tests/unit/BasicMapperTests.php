<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\BasicMapper;
use Rad\Db\Resources\Book;
use Rad\Db\Resources\Note;

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
        $mapper = new BasicMapper(Book::class);
        $mapper->setEntityClassPath('stdClass');
    }

    public function testMapWithADifferentEntityClassPath()
    {
        $mapper = new BasicMapper(Book::class);
        $this->assertInstanceof(Note::class, $mapper->map([], null, Note::class));
    }

    public function testMapInstantiatesAndMapsDataToEntityClass()
    {
        $input = ['title' => 'a value', 'pagecount' => 45];
        $mapper = new BasicMapper(Book::class);
        // Execute
        $result = $mapper->map($input);
        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($input['title'], $result->getTitle());
        $this->assertEquals($input['pagecount'], $result->getPagecount());
    }

    public function testMapUsesSettersToMapValues()
    {
        $input = ['title' => 'a value', 'pagecount' => '45', 'junk' => 'afo'];
        $mapper = new BasicMapper(Book::class);
        // Execute
        $result = $mapper->map($input);
        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $expected = new Book();
        $expected->setTitle($input['title']);
        $expected->setPagecount($input['pagecount']);
        $this->assertEquals($expected->getTitle(), $result->getTitle());
        $this->assertEquals($expected->getPagecount(), $result->getPagecount());
    }

    public function testMapMarksAllSetPropertiesAsSet()
    {
        $input = ['title' => 'a value', 'pagecount' => '45', 'junk' => 'afo'];
        $mapper = new BasicMapper(Book::class);
        // Execute
        $result = $mapper->map($input);
        // Assert
        $property = (new \ReflectionObject($result))->getProperty('mappedProps');
        $property->setAccessible(true);
        $this->assertEquals(['title', 'pagecount'], $property->getValue($result));
    }

    public function testMapSetsOmitListToMappedItem()
    {
        $omitList = ['id'];
        $mapper = new BasicMapper(Book::class);
        // Execute & Assert
        $result = $mapper->map(['somedata' =>'afoo'], $omitList);
        // Hackssert
        $property = (new \ReflectionObject($result))->getProperty('propsToOmit');
        $property->setAccessible(true);
        $this->assertEquals($omitList, $property->getValue($result));
    }

    public function testMapAllInstantiatesAndMapsAnArrayOfItems()
    {
        $inputs = [
            ['title' => 'a value', 'pagecount' => 45],
            ['title' => 'a valyr', 'pagecount' => 46]
        ];
        $mapper = new BasicMapper(Book::class);
        // Execute
        $results = $mapper->mapAll($inputs);
        // Assert
        $this->assertCount(2, $results);
        $this->assertInstanceOf(Book::class, $results[0]);
        $this->assertEquals($inputs[0]['title'], $results[0]->getTitle());
        $this->assertEquals($inputs[0]['pagecount'], $results[0]->getPagecount());
        $this->assertInstanceOf(Book::class, $results[1]);
        $this->assertEquals($inputs[1]['title'], $results[1]->getTitle());
        $this->assertEquals($inputs[1]['pagecount'], $results[1]->getPagecount());
    }

    public function testMapAllAlwaysReturnsAnArrayOfMappedItems()
    {
        $inputs = ['title' => 'a value', 'pagecount' => 45];
        $mapper = new BasicMapper(Book::class);
        // Execute
        $results = $mapper->mapAll($inputs);
        // Assert
        $this->assertCount(1, $results, 'Should be 1 [0], not 2 ([title, pagecount])');
        $this->assertInstanceOf(Book::class, $results[0]);
        $this->assertEquals($inputs['title'], $results[0]->getTitle());
        $this->assertEquals($inputs['pagecount'], $results[0]->getPagecount());
    }

    public function testMapAllReturnsEmptyArrayIfTheresNothingToMap()
    {
        $mapper = new BasicMapper(Book::class);
        // Execute
        $results = $mapper->mapAll([]);
        // Assert
        $this->assertSame([], $results);
    }

    public function testGetKeysCollectsKeysFromSetters()
    {
        $mapper = new BasicMapper(Book::class);
        // Execute
        $result = $mapper->getKeys();
        // Assert
        $this->assertEquals(['id', 'title', 'pagecount'], $result);
    }
}
