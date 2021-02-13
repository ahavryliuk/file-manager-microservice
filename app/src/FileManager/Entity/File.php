<?php

namespace FileManager\Entity;

use Carbon\Carbon;

class File implements \JsonSerializable
{
    public const FIELD_ID = 'id';
    public const FIELD_EXTERNAL_ID = 'external_id';
    public const FIELD_TOKEN = 'token';
    public const FIELD_NAME = 'name';
    public const FIELD_RESOURCE_URL = 'resource_url';
    public const FIELD_RESOURCE_META = 'resource_meta';
    public const FIELD_SIZE = 'size';
    public const FIELD_DELETED = 'deleted';
    public const FIELD_CREATED_AT = 'created_at';

    private ?int $id = null;
    private string $externalId;
    private string $token;
    private string $name;
    private string $resourceUrl;
    private ?array $resourceMeta;
    private int $size;
    private bool $deleted;
    private ?Carbon $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): File
    {
        $this->id = $id;
        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): File
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): File
    {
        $this->token = $token;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): File
    {
        $this->name = $name;
        return $this;
    }

    public function getResourceUrl(): string
    {
        return $this->resourceUrl;
    }

    public function setResourceUrl(string $resourceUrl): File
    {
        $this->resourceUrl = $resourceUrl;
        return $this;
    }

    public function getResourceMeta(): ?array
    {
        return $this->resourceMeta;
    }

    public function setResourceMeta(?array $resourceMeta): File
    {
        $this->resourceMeta = $resourceMeta;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): File
    {
        $this->size = $size;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): File
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?Carbon $createdAt): File
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            self::FIELD_ID => $this->id,
            self::FIELD_EXTERNAL_ID => $this->externalId,
            self::FIELD_TOKEN => $this->token,
            self::FIELD_NAME => $this->name,
            self::FIELD_RESOURCE_URL => $this->resourceUrl,
            self::FIELD_RESOURCE_META => $this->resourceMeta ?? null,
            self::FIELD_SIZE => $this->size,
            self::FIELD_DELETED => (int)$this->deleted,
            self::FIELD_CREATED_AT => $this->createdAt instanceof Carbon ? $this->createdAt->toDateTimeString() : null,
        ];
    }
}