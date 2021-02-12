<?php

namespace tests\integration;

use Carbon\Carbon;
use FileManager\Entity\File;
use FileManager\Repository\FileRepository;
use tests\TestCase;

class FileManagerTest extends TestCase
{

    public function testFileGet(): void
    {
        $mockFile = new File();
        $mockFile
            ->setId(1)
            ->setExternalId('81be7b67e5fbc5aed6676699a690a0b8746a5738')
            ->setToken('11b95e044a0acc22846b9c9acaf79826de392643')
            ->setName('2ec32348fd17aa00edd0cc09d4046d17469e8924')
            ->setResourceUrl('/var/www/config/../storage/2ec32348fd17aa00edd0cc09d4046d17469e8924.jpg')
            ->setResourceMeta([])
            ->setDeleted(false)
            ->setCreatedAt(Carbon::now());

        $this->mock(FileRepository::class)
            ->method('findOneByExternalId')
            ->willReturn($mockFile);

        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/files/81be7b67e5fbc5aed6676699a690a0b8746a5738');
        $response = $app->handle($request);

        self::assertEquals(200, (int)$response->getStatusCode());

        self::assertEquals('application/octet-stream', $response->getHeader('Content-Type')[0]);
        self::assertEquals('attachment; filename=2ec32348fd17aa00edd0cc09d4046d17469e8924', $response->getHeader('Content-Disposition')[0]);
    }

}