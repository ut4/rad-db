<?php

namespace Rad\Db\Integration;

use Aura\SqlQuery\QueryInterface;

trait BasicCrudRepositoryDeleteTests
{
    public function testDeleteDeletesItemsUsingProvidedFilters()
    {
        // Prepare
        $data = [
            ['somecol' => 'jkl', 'number' => 51],
            ['somecol' => 'öä', 'number' => 52]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Execute
        $rowCount = $this->testBasicCrudRepository->delete(
            [],
            function (QueryInterface $q) use ($data) {
                $q->where('somecol = :scv');
                $q->bindValue('scv', $data[0]['somecol']);
            }
        );
        // Assert
        $this->assertEquals(1, $rowCount);
        $this->assertEmpty($this->fetchTestData($id));
        $this->assertNotEmpty($this->fetchTestData($id2));
    }

    public function testDeleteDeletesSingleItemUsingDefaultFilters()
    {
        // Prepare
        $data = [
            ['somecol' => 'zxc', 'number' => 53],
            ['somecol' => 'vbn', 'number' => 54]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Execute
        $rowCount = $this->testBasicCrudRepository->delete(['id' => $id2]);
        // Assert
        $this->assertEquals(1, $rowCount);
        $this->assertEmpty($this->fetchTestData($id2));
        $this->assertNotEmpty($this->fetchTestData($id));
    }
}
