<?php

declare(strict_types=1);

namespace Poodle\Exception;

/**
 * Exception thrown when API rate limits are exceeded
 */
class RateLimitException extends PoodleException
{
    /**
     * @var int|null
     */
    protected ?int $retryAfter = null;

    /**
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * @var int|null
     */
    protected ?int $remaining = null;

    /**
     * @var int|null
     */
    protected ?int $resetTime = null;

    /**
     * @param string $message
     * @param int|null $retryAfter
     * @param int|null $limit
     * @param int|null $remaining
     * @param int|null $resetTime
     */
    public function __construct(
        string $message = '',
        ?int $retryAfter = null,
        ?int $limit = null,
        ?int $remaining = null,
        ?int $resetTime = null
    ) {
        $context = [
            'error_type' => 'rate_limit_exceeded',
            'retry_after' => $retryAfter,
            'limit' => $limit,
            'remaining' => $remaining,
            'reset_time' => $resetTime,
        ];

        parent::__construct($message, 429, null, $context, 429);

        $this->retryAfter = $retryAfter;
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->resetTime = $resetTime;
    }

    /**
     * Get the number of seconds to wait before retrying
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    /**
     * Get the rate limit
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the remaining requests
     */
    public function getRemaining(): ?int
    {
        return $this->remaining;
    }

    /**
     * Get the time when the rate limit resets
     */
    public function getResetTime(): ?int
    {
        return $this->resetTime;
    }

    /**
     * Create a rate limit exception from response headers
     *
     * @param array<string, string> $headers
     */
    public static function fromHeaders(array $headers): self
    {
        $retryAfter = isset($headers['Retry-After']) ? (int) $headers['Retry-After'] : null;
        $limit = isset($headers['X-RateLimit-Limit']) ? (int) $headers['X-RateLimit-Limit'] : null;
        $remaining = isset($headers['X-RateLimit-Remaining']) ? (int) $headers['X-RateLimit-Remaining'] : null;
        $resetTime = isset($headers['X-RateLimit-Reset']) ? (int) $headers['X-RateLimit-Reset'] : null;

        $message = 'Rate limit exceeded.';
        if ($retryAfter !== null) {
            $message .= " Retry after {$retryAfter} seconds.";
        }

        return new self($message, $retryAfter, $limit, $remaining, $resetTime);
    }
}
