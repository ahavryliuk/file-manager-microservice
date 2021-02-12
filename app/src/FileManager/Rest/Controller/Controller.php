<?php

namespace FileManager\Rest\Controller;

use FileManager\Entity\File;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class Controller
{

    /**
     * @param ResponseInterface $response
     * @param File $file
     *
     * @return ResponseInterface
     */
    public function respondResource(ResponseInterface $response, File $file): ResponseInterface
    {
        return $response
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment; filename=' . $file->getName())
            ->withAddedHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Cache-Control', 'post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache')
            ->withBody((new \Slim\Psr7\Stream(fopen($file->getResourceUrl(), 'rb'))));
    }

    /**
     * @param ResponseInterface $response
     * @param \JsonSerializable|null $item
     *
     * @return Response
     *
     * @throws \JsonException
     */
    public function respondItem(ResponseInterface $response, ?\JsonSerializable $item): ResponseInterface
    {
        $serializedItem = null;

        if ($item) {
            $serializedItem = $item->jsonSerialize();
        }

        return $this->respond($response, $serializedItem);
    }

    /**
     * @param ResponseInterface $response
     * @param \JsonSerializable[]|null $collection
     *
     * @return Response
     *
     * @throws \JsonException
     */
    public function respondCollection(ResponseInterface $response, ?array $collection): ResponseInterface
    {
        $serializedCollection = [];

        if ($collection) {

            foreach ($collection as $item) {
                $serializedCollection[] = $item->jsonSerialize();
            }
        }

        return $this->respond($response, $serializedCollection);
    }

    /**
     * @param ResponseInterface $response
     * @param mixed $data
     *
     * @return ResponseInterface
     *
     * @throws \JsonException
     */
    public function respond(ResponseInterface $response, $data): ResponseInterface
    {
        $payload = [
            'data' => $data ?? []
        ];

        $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

}