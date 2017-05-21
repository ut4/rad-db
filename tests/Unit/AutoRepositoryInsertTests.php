<?php

namespace Rad\Db\Unit;

trait AutoRepositoryInsertTests
{
    public function testInsertMapsAndInsertsHintedData()
    {
        $data = [
            'title' => 'moonwalkers',
            'pagecount' => '2340',
            'notes' => [['content' => 'notea'], ['content' => 'noteb']]
        ];
        $bookInsertId = 2;
        $hintedQueryResult = 1;
        // main query, one item
        $this->mockQueryBuildingDb
            ->expects($this->exactly(1))
            ->method('insert')
            ->with('books', $this->assertCallback(function ($mapped) use ($data) {
                $actualInsertData = $mapped->jsonSerialize();
                $this->assertEquals(
                    $data['title'],
                    $actualInsertData['title']
                );
                $this->assertEquals(
                    $data['pagecount'],
                    $actualInsertData['pagecount']
                );
            }))
            ->willReturn($bookInsertId);
        // hinted query, multiple items
        $this->mockQueryBuildingDb
            ->expects($this->exactly(1))
            ->method('insertMany')
            ->with('notes', $this->assertCallback(function ($mapped) use ($data, $bookInsertId) {
                foreach ([0, 1 ] as $i) {
                    $actualInsertData = $mapped[$i]->jsonSerialize();
                    $this->assertEquals(
                        $data['notes'][$i]['content'],
                        $actualInsertData['content']
                    );
                    $this->assertEquals(
                        $bookInsertId,
                        $actualInsertData['booksId']
                    );
                }
            }))
            ->willReturn($hintedQueryResult);
        //
        $result = $this->bookRepository->insert($data);
        //
        $this->assertEquals($bookInsertId, $result);
    }

    public function testInsertExecutesHintedQueriesOnlyWhenTheTargetPropExistsInInput()
    {
        // Book has a hint targeted to "notes"-property
        $data = [
            'title' => 'afo'
            // no notes here ...
        ];
        $mainQueryResult = '12';
        $this->mockQueryBuildingDb
            ->expects($this->exactly(1)) // only once (main query)
            ->method('insert')
            ->willReturn($mainQueryResult);
        $result = $this->bookRepository->insert($data);
        $this->assertEquals($mainQueryResult, $result);
    }

    public function testInsertDoesNotExecuteHintedQueriesIfMainQueryFails()
    {
        $data = [
            'title' => 'afo',
            'notes' => [['content' => 'bar']]
        ];
        $fail = 0;
        $this->mockQueryBuildingDb // main query
            ->expects($this->exactly(1))
            ->method('insert')
            ->willReturn($fail);
        $this->mockQueryBuildingDb // hinted query
            ->expects($this->never())
            ->method('insertMany');
        $result = $this->bookRepository->insert($data);
        $this->assertEquals($fail, $result);
    }

    public function testInsertRollsBackIfHintedQueryFails()
    {
        $data = [
            'title' => 'afo',
            'notes' => [['content' => 'bar']]
        ];
        $mainQueryResult = 2;
        $hintedQueryResult = 0;
        // main query, one item
        $this->mockQueryBuildingDb
            ->expects($this->exactly(1))
            ->method('insert')
            ->with('books', $this->callback(function ($mapped) use ($data) {
                return $mapped->jsonSerialize()['title'] === $data['title'];
            }))
            ->willReturn($mainQueryResult);
        // hinted query, multiple items
        $this->mockQueryBuildingDb
            ->expects($this->exactly(1))
            ->method('insertMany')
            ->with('notes', $this->callback(function ($mapped) use ($data) {
                return $mapped[0]->jsonSerialize()['content'] === $data['notes'][0]['content'];
            }))
            ->willReturn($hintedQueryResult);
        $result = $this->bookRepository->insert($data);
        $this->assertEquals($hintedQueryResult, $result);
    }
}
