<?php

namespace Rad\Db\Integration;

use Rad\Db\Db;
use Rad\Db\QueryBuildingDb;
use Rad\Db\Planner;
use Rad\Db\PlanExecutor;
use Rad\Db\Resources\AutoBookRepository;
use Rad\Db\Resources\Book;
use Rad\Db\Resources\Note;

class AutoRepositoryTests extends InMemoryPDOTestCase
{
    private $queryBuildingDb;
    private $bookRepository;
    private $testInputData = [
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
        $this->bookRepository = new AutoBookRepository(
            new Planner(),
            new PlanExecutor($this->queryBuildingDb)
        );
    }

    public function testInsertMapsAndInsertsOneToManyHintedItems()
    {
        $data = $this->testInputData;
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

    public function testSelectAllMapsOneToManyHintedItems()
    {
        // Insert test data
        $data = $this->testInputData;
        $bookId = $this->insertTestData(
            ['title' => $data['title'], 'pagecount' => $data['pagecount']],
            'books'
        );
        $this->assertGreaterThan(0, $bookId);
        foreach ([0, 1] as $i) {
            ${'noteId' . $i} = $this->insertTestData(
                ['content' => $data['notes'][$i]['content'], 'booksId' => $bookId],
                'notes'
            );
        }
        // Execute
        $results = $this->bookRepository->selectAll();
        $this->assertCount(1, $results);
        // Assert book got mapp'd't's
        $mappedBook = $results[0];
        $this->assertInstanceOf(Book::class, $mappedBook);
        $this->assertSame($bookId, $mappedBook->getId());
        $this->assertSame($data['title'], $mappedBook->getTitle());
        $this->assertSame((int) $data['pagecount'], $mappedBook->getPagecount());
        // Assert hinted notes
        $this->assertCount(2, $mappedBook->notes);
        foreach ([0, 1] as $i) {
            $mappedNote = $mappedBook->notes[$i];
            $this->assertInstanceOf(Note::class, $mappedNote);
            $this->assertSame(${'noteId' . $i}, $mappedNote->getId());
            $this->assertSame($data['notes'][$i]['content'], $mappedNote->getContent());
        }
        $this->assertEquals(
            json_encode([
                [
                    'id' => $bookId,
                    'title' => $data['title'],
                    'pagecount' => (int) $data['pagecount'],
                    'notes' => [
                        [
                            'id' => $noteId0,
                            'content' => $data['notes'][0]['content'],
                            'booksId' => $bookId
                        ],
                        [
                            'id' => $noteId1,
                            'content' => $data['notes'][1]['content'],
                            'booksId' => $bookId
                        ]
                    ]
                ]
            ]),
            json_encode($results)
        );
    }
}