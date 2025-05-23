<?php

declare(strict_types=1);

namespace Poodle\Exception;

/**
 * Exception thrown when API authentication fails
 */
class AuthenticationException extends PoodleException
{
    /**
     * Create an exception for invalid API key
     */
    public static function invalidApiKey(): self
    {
        return new self(
            'Invalid API key provided. Please check your API key and try again.',
            401,
            null,
            ['error_type' => 'invalid_api_key'],
            401
        );
    }

    /**
     * Create an exception for missing API key
     */
    public static function missingApiKey(): self
    {
        return new self(
            'API key is required. Please provide a valid API key.',
            401,
            null,
            ['error_type' => 'missing_api_key'],
            401
        );
    }

    /**
     * Create an exception for expired API key
     */
    public static function expiredApiKey(): self
    {
        return new self(
            'API key has expired. Please generate a new API key.',
            401,
            null,
            ['error_type' => 'expired_api_key'],
            401
        );
    }

    /**
     * Create an exception for insufficient permissions
     */
    public static function insufficientPermissions(): self
    {
        return new self(
            'API key does not have sufficient permissions for this operation.',
            403,
            null,
            ['error_type' => 'insufficient_permissions'],
            403
        );
    }
}
