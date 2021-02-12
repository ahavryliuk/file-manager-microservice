<?php

namespace FileManager\Service;

use Noodlehaus\Config;
use Slim\Psr7\UploadedFile;

class UploadService
{
    private Config $config;

    /**
     * UploadService constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @return string
     *
     * @throws \Exception
     */
    public function moveUploadedFile(UploadedFile $uploadedFile): array
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = $this->generateRandomToken();
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($this->config->get('storage_path') . '/' . $filename);

        return [
            $this->config->get('storage_path') . '/' . $filename,
            $filename
        ];
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function generateRandomToken(): string
    {
        return bin2hex(random_bytes(20));
    }

}