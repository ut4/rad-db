<?php

namespace Rad\Db\Integration;

trait BasicCrudRepositoryInsertTests
{
    public function testInsertMapsAndInsertsSingleItem()
    {
        $data = [
            'somecol' => 'foo',
            'number' => '23',
            // these should be ignored
            'id' => '0',
            'junk' => 'qwe'
        ];
        // Execute
        $insertId = $this->testBasicCrudRepository->insert($data);
        // Assert
        $this->assertGreaterThan(0, $insertId, 'Should return the insertId');
        $inserted = $this->fetchTestData($insertId);
        $this->assertNotEquals($data['id'], $inserted['id']);
        $this->assertEquals($data['somecol'], $inserted['somecol']);
        $this->assertEquals($data['number'], $inserted['number']);
    }

    public function testInsertMapsAndInsertsMultipleItems()
    {
        $data = [[
            'somecol' => 'foo',
            'number' => '23',
            // these should be ignored
            'id' => '0',
            'junk' => 'qwe'
        ], [
            'somecol' => 'bat',
            'number' => '34',
            // this should be ignored
            'id' => '0'
        ]];
        // Execute
        $insertId = $this->testBasicCrudRepository->insert($data);
        // Assert
        $this->assertGreaterThan(0, $insertId, 'Should return the insertId');
        $inserted = $this->fetchTestData(null, 'fetchAll');
        foreach ([0, 1] as $i) {
            $this->assertNotEquals($data[$i]['id'], $inserted[$i]['id']);
            $this->assertEquals($data[$i]['somecol'], $inserted[$i]['somecol']);
            $this->assertEquals($data[$i]['number'], $inserted[$i]['number']);
        }
    }
}
