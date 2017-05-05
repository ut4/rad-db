<?php

namespace Rad\Db\Integration;

use Aura\SqlQuery\QueryInterface;

trait BasicCrudRepositoryUpdateTests
{
    public function testUpdateMapsAndUpdatesSingleItemUsingProvidedFilters()
    {
        // Prepare
        $data = [
            ['title' => 'qwe', 'pagecount' => 45],
            ['title' => 'rty', 'pagecount' => 46]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Modify something & execute
        $itemToUpdate = $this->fetchTestData($id);
        $itemToUpdate['id'] = '567';// this should not have any effect
        $itemToUpdate['title'] = 'uio';
        $itemToUpdate['pagecount'] = '47';
        $rowCount = $this->bookRepository->update(
            $itemToUpdate,
            function (QueryInterface $q) use ($data) {
                $q->where('title = :tv');
                $q->bindValue('tv', $data[0]['title']);
            }
        );
        // Assert
        $this->assertEquals(1, $rowCount);
        $updated = $this->fetchTestData($id);
        $this->assertEquals($id, $updated['id']);
        $this->assertEquals($itemToUpdate['title'], $updated['title']);
        $this->assertEquals($itemToUpdate['pagecount'], $updated['pagecount']);
        $notUpdated = $this->fetchTestData($id2);
        $this->assertEquals($data[1]['title'], $notUpdated['title']);
        $this->assertEquals($data[1]['pagecount'], $notUpdated['pagecount']);
    }

    public function testUpdateMapsAndUpdatesSingleItemUsingDefaultFilters()
    {
        // Prepare
        $data = [
            ['title' => 'på', 'pagecount' => 48],
            ['title' => 'asd', 'pagecount' => 49]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Modify something & execute
        $itemToUpdate = $this->fetchTestData($id);
        $itemToUpdate['title'] = 'fgh';
        $itemToUpdate['pagecount'] = '50';
        $rowCount = $this->bookRepository->update($itemToUpdate);
        // Assert
        $this->assertEquals(1, $rowCount);
        $updated = $this->fetchTestData($id);
        $this->assertEquals($itemToUpdate['title'], $updated['title']);
        $this->assertEquals($itemToUpdate['pagecount'], $updated['pagecount']);
        $notUpdated = $this->fetchTestData($id2);
        $this->assertEquals($data[1]['title'], $notUpdated['title']);
        $this->assertEquals($data[1]['pagecount'], $notUpdated['pagecount']);
    }
}
