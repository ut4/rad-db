<?php

namespace Rad\Db\Integration;

use Rad\Db\Db;
use Rad\Db\QueryBuildingDb;
use Rad\Db\HintQueryRunner;
use Rad\Db\Resources\AutoBookRepository;

class AutoRepositoryTests extends InMemoryPDOTestCase
{
    private $queryBuildingDb;
    private $hintQueryRunner;
    private $bookRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        parent::beforeEach(true);
        $this->queryBuildingDb = new QueryBuildingDb(
            new Db($this->connection),
            $this->queryFactory
        );
        $this->hintQueryRunner = new HintQueryRunner();
        $this->bookRepository = new AutoBookRepository(
            $this->hintQueryRunner,
            $this->queryBuildingDb
        );
    }

    public function testInsertMapsAndInsertsOneToManyHintedItems()
    {
        $data = [
            'title' => 'fos',
            'pagecount' => '24',
            'notes' => [
                [
                    'content' => 'a text'
                ],
                [
                    'content' => 'rt'
                ]
            ],
            // these should be ignored
            'id' => '0',
            'junk' => 'qwe'
        ];
        // Execute
        $insertId = $this->bookRepository->insert($data);
        // Assert
        $this->assertGreaterThan(0, $insertId, 'Should return the insertId');
        $insertedBook = $this->fetchTestData('books', $insertId);
        $this->assertNotEquals($data['id'], $insertedBook['id']);
        $this->assertEquals($data['title'], $insertedBook['title']);
        $this->assertEquals($data['pagecount'], $insertedBook['pagecount']);
        $insertedNotes = $this->fetchTestData('notes', 'booksId = ?', [$insertId], 'fetchAll');
        $this->assertCount(2, $insertedNotes);
        $this->assertEquals($data['notes'][0]['content'], $insertedNotes[0]['content']);
        $this->assertEquals($data['notes'][1]['content'], $insertedNotes[1]['content']);
    }
}