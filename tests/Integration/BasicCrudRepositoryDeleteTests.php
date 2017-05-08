<?php

namespace Rad\Db\Integration;

use Aura\SqlQuery\QueryInterface;

trait BasicCrudRepositoryDeleteTests
{
    public function testDeleteDeletesItemsUsingProvidedFilters()
    {
        // Prepare
        $data = [
            ['title' => 'jkl', 'pagecount' => 51],
            ['title' => 'öä', 'pagecount' => 52]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Execute
        $rowCount = $this->bookRepository->delete(
            [],
            function (QueryInterface $q) use ($data) {
                $q->where('title = :tv');
                $q->bindValue('tv', $data[0]['title']);
            }
        );
        // Assert
        $this->assertEquals(1, $rowCount);
        $this->assertEmpty($this->fetchTestData('books', $id));
        $this->assertNotEmpty($this->fetchTestData('books', $id2));
    }

    public function testDeleteDeletesSingleItemUsingDefaultFilters()
    {
        // Prepare
        $data = [
            ['title' => 'zxc', 'pagecount' => 53],
            ['title' => 'vbn', 'pagecount' => 54]
        ];
        $id = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Execute
        $rowCount = $this->bookRepository->delete(['id' => $id2]);
        // Assert
        $this->assertEquals(1, $rowCount);
        $this->assertEmpty($this->fetchTestData('books', $id2));
        $this->assertNotEmpty($this->fetchTestData('books', $id));
    }
}
