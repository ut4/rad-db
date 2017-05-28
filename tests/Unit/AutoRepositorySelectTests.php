<?php

namespace Rad\Db\Unit;

use Aura\SqlQuery\QueryFactory;
use Rad\Db\Resources\Note;

trait AutoRepositorySelectTests
{
    private function manualBeforeEach()
    {
        $this->mockQueryBuildingDb->method('getQueryFactory')->willReturn(
            new QueryFactory('sqlite')
        );
    }

    public function testSelectAllReturnsAnEmptyArrayIfNoResultsWereFound()
    {
        $this->manualBeforeEach();
        $this->mockQueryBuildingDb->method('selectAll')->willReturn([]);
        $results = $this->bookRepository->selectAll();
        $this->assertEquals([], $results);
    }

    public function testSelectAllMapsOneToManyHintedItem()
    {
        $mockBook = [
            'id' => '1',
            'title' => 'harry potta',
            'pagecount' => '7'
        ];
        $mockNote = [
            'notes.id' => '2',
            'notes.content' => 'foo',
            'notes.booksId' => '1'
        ];
        $this->manualBeforeEach();
        $this->mockQueryBuildingDb->method('selectAll')->willReturn([
            $mockBook + $mockNote
        ]);
        //
        $results = $this->bookRepository->selectAll();
        //
        $this->assertDidMap(
            [
                $mockBook + [
                    'notes' => [$mockNote]
                ]
            ],
            $results
        );
    }

    public function testSelectAllMapsOneToManyHintedItems()
    {
        $mockBook1 = [
            'id' => '3',
            'title' => 'harry potta',
            'pagecount' => '7'
        ];
        $mockBook2 = [
            'id' => '4',
            'title' => 'harry potty',
            'pagecount' => '77'
        ];
        $notes = [
            [
                'notes.id' => '3',
                'notes.content' => 'foo',
                'notes.booksId' => '3'
            ],
            [
                'notes.id' => '4',
                'notes.content' => 'bar',
                'notes.booksId' => '3'
            ],
            [
                'notes.id' => '5',
                'notes.content' => 'baz',
                'notes.booksId' => '4'
            ]
        ];
        $this->manualBeforeEach();
        $this->mockQueryBuildingDb->method('selectAll')->willReturn([
            $mockBook1 + $notes[0],
            $mockBook1 + $notes[1],
            $mockBook2 + $notes[2]
        ]);
        //
        $results = $this->bookRepository->selectAll();
        $this->assertDidMap([
            $mockBook1 + [
                'notes' => [
                    $notes[0],
                    $notes[1],
                ]
            ],
            $mockBook2 + [
                'notes' => [
                    $notes[2]
                ]
            ]
        ], $results);
    }

    private function assertDidMap(array $testRows, array $results)
    {
        foreach ($testRows as $i => $row) {
            $expectedBook = $this->mapper->map($row);
            $actualBook = $results[$i];
            $this->assertSame($expectedBook->getId(), $actualBook->getId());
            $this->assertSame($expectedBook->getTitle(), $actualBook->getTitle());
            $this->assertSame($expectedBook->getPagecount(), $actualBook->getPagecount());
            foreach ($row['notes'] as $i2 => $testNote) {
                $expectedNote = $this->mapper->map(
                    [
                        'id' => $testNote['notes.id'],
                        'content' => $testNote['notes.content'],
                        'booksId' => $testNote['notes.booksId']
                    ],
                    Note::class
                );
                $expectedBook->notes[] = $expectedNote;
                $actualNote = $actualBook->notes[$i2];
                $this->assertEquals($expectedNote->jsonSerialize(), $actualNote->jsonSerialize());
                $this->assertSame($expectedNote->getId(), $actualNote->getId());
                $this->assertSame($expectedNote->getContent(), $actualNote->getContent());
                $this->assertSame($expectedNote->getBooksId(), $actualNote->getBooksId());
            }
            $expectedBook->markIsSet('notes');
            $this->assertEquals(json_encode($expectedBook), json_encode($actualBook));
        }
    }
}
