<?php

namespace Rad\Db\Integration;

trait BasicCrudRepositoryInsertTests
{
    public function testInsertMapsAndInsertsSingleItem()
    {
        $data = [
            'title' => 'foo',
            'pagecount' => '23',
            // these should be ignored
            'id' => '0',
            'junk' => 'qwe'
        ];
        // Execute
        $insertId = $this->bookRepository->insert($data);
        // Assert
        $this->assertGreaterThan(0, $insertId, 'Should return the insertId');
        $inserted = $this->fetchTestData('books', $insertId);
        $this->assertNotEquals($data['id'], $inserted['id']);
        $this->assertEquals($data['title'], $inserted['title']);
        $this->assertEquals($data['pagecount'], $inserted['pagecount']);
    }

    public function testInsertMapsAndInsertsMultipleItems()
    {
        $data = [[
            'title' => 'foo',
            'pagecount' => '23',
            // these should be ignored
            'id' => '0',
            'junk' => 'qwe'
        ], [
            'title' => 'bat',
            'pagecount' => '34',
            // this should be ignored
            'id' => '0'
        ]];
        // Execute
        $insertId = $this->bookRepository->insert($data);
        // Assert
        $this->assertGreaterThan(0, $insertId, 'Should return the insertId');
        $inserted = $this->fetchTestData('books', null, null, 'fetchAll');
        foreach ([0, 1] as $i) {
            $this->assertNotEquals($data[$i]['id'], $inserted[$i]['id']);
            $this->assertEquals($data[$i]['title'], $inserted[$i]['title']);
            $this->assertEquals($data[$i]['pagecount'], $inserted[$i]['pagecount']);
        }
    }
}
