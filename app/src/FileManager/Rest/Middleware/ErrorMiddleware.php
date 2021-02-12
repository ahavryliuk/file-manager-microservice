<?php

namespace FileManager\Rest\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;

class ErrorMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    /**
     * ErrorMiddleware constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory
    ) {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (\Throwable $throwable) {
            $response = $this->buildErrorResponse($request, $throwable);
        }

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param \Throwable             $throwable
     *
     * @return ResponseInterface
     */
    private function buildErrorResponse(
        ServerRequestInterface $request,
        \Throwable $throwable
    ): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        switch (true) {
            case $throwable instanceof HttpException:
                $message = $throwable->getMessage();
                $statusCode = $throwable->getCode();
                break;
            default:
                $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
                break;
        }

        $out = [
            'error' => [
                'code' => $throwable->getCode(),
                'message' => $message ?? '',
            ],
        ];

        $response->getBody()->write(json_encode($out));

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}
