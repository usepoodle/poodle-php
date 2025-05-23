<?php

declare(strict_types=1);

namespace Poodle\Exception;

use Exception;

/**
 * Base exception class for all Poodle SDK exceptions
 */
class PoodleException extends Exception
{
    /**
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * @var int|null
     */
    protected ?int $statusCode = null;

    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param array<string, mixed> $context
     * @param int|null $statusCode
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        array $context = [],
        ?int $statusCode = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
        $this->statusCode = $statusCode;
    }

    /**
     * Get additional context information about the exception
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get the HTTP status code if available
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Set additional context information
     *
     * @param array<string, mixed> $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * Set the HTTP status code
     */
    public function setStatusCode(?int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}
