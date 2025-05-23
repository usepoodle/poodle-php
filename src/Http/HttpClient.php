<?php

declare(strict_types=1);

namespace Poodle\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use Poodle\Configuration;
use Poodle\Exception\AuthenticationException;
use Poodle\Exception\NetworkException;
use Poodle\Exception\PoodleException;
use Poodle\Exception\RateLimitException;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP client wrapper for Poodle API communication
 */
class HttpClient
{
    private GuzzleClient $client;

    public function __construct(private Configuration $config)
    {
        $this->client = $this->createGuzzleClient();
    }

    /**
     * Send a POST request
     *
     * @param string $endpoint
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws PoodleException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, [
            RequestOptions::JSON => $data,
        ]);
    }

    /**
     * Send a GET request
     *
     * @param string $endpoint
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     * @throws PoodleException
     */
    public function get(string $endpoint, array $query = []): array
    {
        $options = [];
        if (! empty($query)) {
            $options[RequestOptions::QUERY] = $query;
        }

        return $this->request('GET', $endpoint, $options);
    }

    /**
     * Send an HTTP request
     *
     * @param string $method
     * @param string $endpoint
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     * @throws PoodleException
     */
    private function request(string $method, string $endpoint, array $options = []): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = $this->client->request($method, $url, $options);

            return $this->handleResponse($response);
        } catch (ConnectException $e) {
            throw $this->handleConnectException($e);
        } catch (RequestException $e) {
            throw $this->handleRequestException($e);
        } catch (TransferException $e) {
            throw NetworkException::connectionFailed($url, $e);
        }
    }

    /**
     * Create Guzzle HTTP client
     */
    private function createGuzzleClient(): GuzzleClient
    {
        $defaultOptions = [
            RequestOptions::TIMEOUT => $this->config->getTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $this->config->getConnectTimeout(),
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->config->getApiKey(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => $this->config->getUserAgent(),
            ],
            RequestOptions::HTTP_ERRORS => false, // We'll handle errors manually
        ];

        // Merge with custom HTTP client options
        $options = array_merge($defaultOptions, $this->config->getHttpClientOptions());

        return new GuzzleClient($options);
    }

    /**
     * Build full URL for endpoint
     */
    private function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');

        return $this->config->getBaseUrl() . '/' . $endpoint;
    }

    /**
     * Handle HTTP response
     *
     * @param ResponseInterface $response
     * @return array<string, mixed>
     * @throws PoodleException
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($this->config->isDebug()) {
            error_log("Poodle API Response: {$statusCode} - {$body}");
        }

        // Handle rate limiting
        if ($statusCode === 429) {
            throw RateLimitException::fromHeaders($this->extractHeaders($response));
        }

        // Handle authentication errors
        if ($statusCode === 401) {
            throw AuthenticationException::invalidApiKey();
        }

        if ($statusCode === 403) {
            throw AuthenticationException::insufficientPermissions();
        }

        // Handle other HTTP errors
        if ($statusCode >= 400) {
            $errorMessage = $this->extractErrorMessage($body, $statusCode);

            throw NetworkException::httpError($statusCode, $errorMessage);
        }

        // Parse JSON response
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw NetworkException::malformedResponse($body);
        }

        return $data ?? [];
    }

    /**
     * Handle connect exceptions
     */
    private function handleConnectException(ConnectException $e): PoodleException
    {
        $message = $e->getMessage();

        if (str_contains($message, 'cURL error 28') || str_contains($message, 'timeout')) {
            return NetworkException::connectionTimeout($this->config->getTimeout());
        }

        if (str_contains($message, 'SSL') || str_contains($message, 'certificate')) {
            return NetworkException::sslError($message);
        }

        if (str_contains($message, 'resolve') || str_contains($message, 'DNS')) {
            $host = parse_url($this->config->getBaseUrl(), PHP_URL_HOST);
            if ($host === false || $host === null) {
                $host = 'unknown';
            }

            return NetworkException::dnsResolutionFailed($host);
        }

        return NetworkException::connectionFailed($this->config->getBaseUrl(), $e);
    }

    /**
     * Handle request exceptions
     */
    private function handleRequestException(RequestException $e): PoodleException
    {
        $response = $e->getResponse();
        if ($response !== null) {
            try {
                $this->handleResponse($response);
            } catch (PoodleException $poodleException) {
                return $poodleException;
            }
        }

        return NetworkException::connectionFailed($this->config->getBaseUrl(), $e);
    }

    /**
     * Extract headers from response
     *
     * @param ResponseInterface $response
     * @return array<string, string>
     */
    private function extractHeaders(ResponseInterface $response): array
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }

        return $headers;
    }

    /**
     * Extract error message from response body
     */
    private function extractErrorMessage(string $body, int $statusCode): string
    {
        $data = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['message'])) {
            return $data['message'];
        }

        if (json_last_error() === JSON_ERROR_NONE && isset($data['error'])) {
            return is_string($data['error']) ? $data['error'] : 'An error occurred';
        }

        return "HTTP {$statusCode} error occurred";
    }
}
