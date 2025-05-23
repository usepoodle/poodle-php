<?php

declare(strict_types=1);

namespace Poodle\Exception;

use Throwable;

/**
 * Exception thrown when network or HTTP errors occur
 */
class NetworkException extends PoodleException
{
    /**
     * Create an exception for connection timeout
     */
    public static function connectionTimeout(float $timeout): self
    {
        return new self(
            "Connection timeout after {$timeout} seconds.",
            408,
            null,
            ['timeout' => $timeout, 'error_type' => 'connection_timeout'],
            408
        );
    }

    /**
     * Create an exception for connection failure
     */
    public static function connectionFailed(string $url, ?Throwable $previous = null): self
    {
        return new self(
            "Failed to connect to {$url}.",
            0,
            $previous,
            ['url' => $url, 'error_type' => 'connection_failed']
        );
    }

    /**
     * Create an exception for DNS resolution failure
     */
    public static function dnsResolutionFailed(string $host): self
    {
        return new self(
            "DNS resolution failed for host: {$host}",
            0,
            null,
            ['host' => $host, 'error_type' => 'dns_resolution_failed']
        );
    }

    /**
     * Create an exception for SSL/TLS errors
     */
    public static function sslError(string $message): self
    {
        return new self(
            "SSL/TLS error: {$message}",
            0,
            null,
            ['error_type' => 'ssl_error']
        );
    }

    /**
     * Create an exception for HTTP errors
     */
    public static function httpError(int $statusCode, string $message = ''): self
    {
        $defaultMessage = "HTTP error occurred with status code: {$statusCode}";
        $finalMessage = $message ?: $defaultMessage;

        return new self(
            $finalMessage,
            $statusCode,
            null,
            ['error_type' => 'http_error'],
            $statusCode
        );
    }

    /**
     * Create an exception for malformed response
     */
    public static function malformedResponse(string $response = ''): self
    {
        return new self(
            'Received malformed response from server.',
            0,
            null,
            ['response' => $response, 'error_type' => 'malformed_response']
        );
    }
}
