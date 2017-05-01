<?php

namespace Rad\Db\Integration;

trait BasicCrudRepositoryInsertTests
{
    public function testInsertMapsAndInsertsMultipleItems()
    {
        $data = [[
            'somecol' => 'foo',
            'number' => '23',
            // these should be ignored
            'id' => '0',
            'junk' => 'qwe'
        ]];
        // Execute
        $insertId = $this->testBasicCrudRepository->insert($data);
        // Assert
        $this->assertGreaterThan(0, $insertId, 'Should return the insertId');
        $inserted = $this->fetchTestData($insertId);
        $this->assertNotEquals($data[0]['id'], $inserted['id']);
        $this->assertEquals($data[0]['somecol'], $inserted['somecol']);
        $this->assertEquals($data[0]['number'], $inserted['number']);
    }
}
