<?php

namespace Rad\Db\Integration;

use Aura\SqlQuery\QueryInterface;

trait BasicCrudRepositoryUpdateTests
{
    public function testUpdateMapsAndUpdatesSingleItemUsingProvidedFilters()
    {
        // Prepare
        $data = [
            ['somecol' => 'qwe', 'number' => 45],
            ['somecol' => 'rty', 'number' => 46]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Modify something & execute
        $itemToUpdate = $this->fetchTestData($id);
        $itemToUpdate['id'] = '567';// this should not have any effect
        $itemToUpdate['somecol'] = 'uio';
        $itemToUpdate['number'] = '47';
        $rowCount = $this->testBasicCrudRepository->update(
            $itemToUpdate,
            function (QueryInterface $q) use ($data) {
                $q->where('somecol = :scv');
                $q->bindValue('scv', $data[0]['somecol']);
            }
        );
        // Assert
        $this->assertEquals(1, $rowCount);
        $updated = $this->fetchTestData($id);
        $this->assertEquals($id, $updated['id']);
        $this->assertEquals($itemToUpdate['somecol'], $updated['somecol']);
        $this->assertEquals($itemToUpdate['number'], $updated['number']);
        $notUpdated = $this->fetchTestData($id2);
        $this->assertEquals($data[1]['somecol'], $notUpdated['somecol']);
        $this->assertEquals($data[1]['number'], $notUpdated['number']);
    }

    public function testUpdateMapsAndUpdatesSingleItemUsingDefaultFilters()
    {
        // Prepare
        $data = [
            ['somecol' => 'på', 'number' => 48],
            ['somecol' => 'asd', 'number' => 49]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Modify something & execute
        $itemToUpdate = $this->fetchTestData($id);
        $itemToUpdate['somecol'] = 'fgh';
        $itemToUpdate['number'] = '50';
        $rowCount = $this->testBasicCrudRepository->update($itemToUpdate);
        // Assert
        $this->assertEquals(1, $rowCount);
        $updated = $this->fetchTestData($id);
        $this->assertEquals($itemToUpdate['somecol'], $updated['somecol']);
        $this->assertEquals($itemToUpdate['number'], $updated['number']);
        $notUpdated = $this->fetchTestData($id2);
        $this->assertEquals($data[1]['somecol'], $notUpdated['somecol']);
        $this->assertEquals($data[1]['number'], $notUpdated['number']);
    }
}
