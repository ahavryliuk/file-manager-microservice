<?php

namespace FileManager\Repository;

use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use FileManager\Entity\File;
use FileManager\Helper\TypeConversionHelper;

class FileRepository
{
    public const TABLE = 'file';

    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array|null $input
     *
     * @return File
     */
    public function create(?array $input = []): File
    {
        $file = new File();
        if (empty($input)) {
            return $file;
        }

        $file->setId($input[File::FIELD_ID]);
        $file->setExternalId($input[File::FIELD_EXTERNAL_ID]);
        $file->setToken($input[File::FIELD_TOKEN]);
        $file->setName($input[File::FIELD_NAME]);
        $file->setResourceUrl($input[File::FIELD_RESOURCE_URL]);
        $file->setResourceMeta(TypeConversionHelper::jsonToArray($input[File::FIELD_RESOURCE_META]) ?? []);
        $file->setSize($input[File::FIELD_SIZE]);
        $file->setDeleted($input[File::FIELD_DELETED]);
        $file->setCreatedAt(TypeConversionHelper::stringToNullCarbon($input[File::FIELD_CREATED_AT]));

        return $file;
    }

    /**
     * @param string $externalId
     *
     * @return File|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function findOneByExternalId(string $externalId): ?File
    {
        $sql = sprintf("SELECT * FROM `%s` WHERE %s = ?", self::TABLE, File::FIELD_EXTERNAL_ID);
        $row = $this->connection->fetchAssociative($sql, [$externalId]);
        if (empty($row)) {
            return null;
        }

        return $this->create($row);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAll(): array
    {
        $sql = sprintf("SELECT * FROM `%s`", self::TABLE);

        $objects = [];
        $rows = $this->connection->fetchAllAssociative($sql);

        foreach ($rows as $row) {
            $objects[] = $this->create($row);
        }

        return $objects;
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function calculateUsedStorageSpace(): int
    {
        $sql = sprintf("SELECT SUM(`%s`) FROM `%s`", File::FIELD_SIZE, self::TABLE);
        $sum = $this->connection->fetchOne($sql);

        if (empty($sum)) {
            return 0;
        }

        return $sum;
    }

    /**
     * @param File $file
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function persist(File $file): void
    {
        if ($file->getId() === null) {
            $file->setCreatedAt(Carbon::now());
        }

        $data = $file->jsonSerialize();

        $data[File::FIELD_RESOURCE_META] = json_encode($file->getResourceMeta());

        if ($file->getId() !== null) {
            $this->connection->update(self::TABLE, $data, [File::FIELD_ID => $file->getId()]);
            return;
        }

        $this->connection->insert(self::TABLE, $data);

        $file->setId((int)$this->connection->lastInsertId());
    }
}