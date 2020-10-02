<?php

namespace App\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class CurlService
{
    /**
     * @var GuzzleClient
     */
    private $client;

    /** @var LoggerInterface  */
    protected $logger;

    /**
     * CurrencyExchangeManager constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = new GuzzleClient();
    }

    /**
     * Creating a request with the given arguments.
     *
     * If api_version is set, appends it immediately after host
     *
     * @param string $httpMethod
     * @param string $path
     * @param array  $headers
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function executeRequest(string $httpMethod, string $path, array $headers = []): ResponseInterface
    {
        try {
            $result = $this->client->request($httpMethod, $path, $headers);
        } catch (GuzzleException $e) {
            $this->logger->error('GuzzleException error: '.$e->getMessage());
        }

        return $result ?? null;
    }
}
