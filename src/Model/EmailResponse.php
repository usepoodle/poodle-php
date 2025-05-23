<?php

declare(strict_types=1);

namespace Poodle\Model;

/**
 * Email response model representing the API response for email operations
 */
class EmailResponse
{
    private bool $success;
    private string $message;

    public function __construct(bool|array $success = false, string $message = '')
    {
        if (is_array($success)) {
            // Old array-based constructor
            $data = $success;
            $this->success = $data['success'] ?? false;
            $this->message = $data['message'] ?? '';
        } else {
            // New individual parameter constructor
            $this->success = $success;
            $this->message = $message;
        }
    }

    /**
     * Create from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['success'] ?? false, $data['message'] ?? '');
    }

    /**
     * Check if email was successfully queued
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Get response message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Convert response to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
        ];
    }

    /**
     * Convert response to JSON
     */
    public function toJson(): string
    {
        $json = json_encode($this->toArray(), JSON_PRETTY_PRINT);

        if ($json === false) {
            throw new \RuntimeException('Failed to encode response as JSON');
        }

        return $json;
    }
}
