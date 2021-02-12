<?php

namespace tests\unit\FileManager\Repository;

use Doctrine\DBAL\Connection;
use FileManager\Repository\FileRepository;
use PHPUnit\Framework\MockObject\MockObject;
use tests\TestCase;

class FileRepositoryTest extends TestCase
{
    /** @var Connection|MockObject */
    private $connection;

    private FileRepository $fileRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();

        $this->fileRepository = new FileRepository($this->connection);
    }

    public function testFindOneByExternalId(): void
    {
        $this->expectQueryAndValues(
            "SELECT * FROM file WHERE external_id = ?",
            [123]
        );
        $this->fileRepository->findOneByExternalId(123);
    }

    private function expectQueryAndValues(string $expectedSQL, array $values): void
    {
        $this->connection
            ->method('fetchAll')
            ->with(self::equalTo($expectedSQL), self::equalTo($values))
            ->willReturn([]);
    }
}