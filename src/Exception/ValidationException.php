<?php

declare(strict_types=1);

namespace Poodle\Exception;

/**
 * Exception thrown when request validation fails
 */
class ValidationException extends PoodleException
{
    /**
     * @var array<string, array<string>>
     */
    protected array $errors = [];

    /**
     * @param string $message
     * @param array<string, array<string>> $errors
     * @param int $code
     */
    public function __construct(string $message = '', array $errors = [], int $code = 422)
    {
        parent::__construct($message, $code, null, ['errors' => $errors], 422);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     *
     * @return array<string, array<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Create an exception for invalid email address
     */
    public static function invalidEmail(string $email, string $field = 'email'): self
    {
        return new self(
            'Invalid email address provided.',
            [$field => ["'{$email}' is not a valid email address."]]
        );
    }

    /**
     * Create an exception for missing required field
     */
    public static function missingField(string $field): self
    {
        return new self(
            "Missing required field: {$field}",
            [$field => ["The {$field} field is required."]]
        );
    }

    /**
     * Create an exception for invalid content
     */
    public static function invalidContent(): self
    {
        return new self(
            'Email must contain either HTML content, text content, or both.',
            ['content' => ['At least one content type (html or text) is required.']]
        );
    }

    /**
     * Create an exception for content too large
     */
    public static function contentTooLarge(string $field, int $maxSize): self
    {
        return new self(
            "Content size exceeds maximum allowed size of {$maxSize} bytes.",
            [$field => ["Content size exceeds maximum allowed size of {$maxSize} bytes."]]
        );
    }

    /**
     * Create an exception for invalid field value
     */
    public static function invalidFieldValue(string $field, string $value, string $reason = ''): self
    {
        $message = "Invalid value for field '{$field}': {$value}";
        if ($reason) {
            $message .= ". {$reason}";
        }

        return new self(
            $message,
            [$field => [$message]]
        );
    }
}
