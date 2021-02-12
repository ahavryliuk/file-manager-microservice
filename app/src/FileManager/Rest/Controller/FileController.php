<?php

namespace FileManager\Rest\Controller;

use FileManager\Entity\File;
use FileManager\Repository\FileRepository;
use FileManager\Service\UploadService;
use Noodlehaus\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\UploadedFile;

class FileController extends Controller
{
    private FileRepository $fileRepository;
    private UploadService $uploadService;

    /**
     * FileController constructor.
     *
     * @param FileRepository $fileRepository
     * @param UploadService $uploadService
     */
    public function __construct(FileRepository $fileRepository, UploadService $uploadService)
    {
        $this->fileRepository = $fileRepository;
        $this->uploadService = $uploadService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     *
     * @throws HttpNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $externalId = (string)$request->getAttribute('external_id');

        $file = $this->fileRepository->findOneByExternalId($externalId);
        if (!$file || $file->isDeleted()) {
            throw new HttpNotFoundException($request);
        }

        return $this->respondResource($response, $file);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     *
     * @throws HttpBadRequestException
     * @throws \JsonException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function upload(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $upload = $request->getUploadedFiles();

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $upload['file'];

        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
           throw new HttpBadRequestException($request);
        }

        [$url, $name] = $this->uploadService->moveUploadedFile($uploadedFile);

        $file = new File();
        $file
            ->setExternalId($this->uploadService->generateRandomToken())
            ->setToken($this->uploadService->generateRandomToken())
            ->setName($name)
            ->setResourceUrl($url)
            ->setResourceMeta([
                'client_file_name' => $uploadedFile->getClientFilename(),
                'client_file_size' => $uploadedFile->getSize(),
                'client_file_media_type' => $uploadedFile->getClientMediaType(),
                'client_file_path' => $uploadedFile->getFilePath(),
            ])
            ->setDeleted(false);

        $this->fileRepository->persist($file);

        return $this->respondItem($response, $file);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     *
     * @throws HttpNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws HttpForbiddenException
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $externalId = (string)$request->getAttribute('external_id');
        $token = (string)$request->getAttribute('token');

        $file = $this->fileRepository->findOneByExternalId($externalId);
        if (!$file) {
            throw new HttpNotFoundException($request);
        }

        if ($file->getToken() !== $token || $file->isDeleted()) {
            throw new HttpForbiddenException($request);
        }

        $file->setDeleted(true);
        $this->fileRepository->persist($file);

        return $this->respondItem($response, null);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     */
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = $this->fileRepository->findAll();

        return $this->respondCollection($response, $files);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     */
    public function info(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = $this->fileRepository->findAll();

        $uploadedSize = 0;

        /** @var File $file */
        foreach ($files as $file) {
            $meta = $file->getResourceMeta();

            $uploadedSize += $meta['client_file_size'];
        }

        return $this->respond($response, (object)[
            'uploaded_size' => $uploadedSize
        ]);
    }

}