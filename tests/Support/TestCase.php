<?php

declare(strict_types=1);

namespace Poodle\Tests\Support;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Poodle\Configuration;

/**
 * Base test case with common testing functionality
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Create a test configuration
     */
    protected function createTestConfiguration(array $overrides = []): Configuration
    {
        $defaults = [
            'apiKey' => 'test_api_key_12345',
            'baseUrl' => 'https://api.test.poodle.com',
            'timeout' => 5.0,
            'connectTimeout' => 2.0,
            'debug' => true,
        ];

        $config = array_merge($defaults, $overrides);

        return new Configuration(
            $config['apiKey'],
            $config['baseUrl'],
            $config['timeout'],
            $config['connectTimeout'],
            $config['debug']
        );
    }

    /**
     * Get a valid test email address
     */
    protected function getTestEmail(): string
    {
        return 'test@example.com';
    }

    /**
     * Get a valid test sender email address
     */
    protected function getTestSenderEmail(): string
    {
        return 'sender@example.com';
    }

    /**
     * Get test email data
     *
     * @return array<string, mixed>
     */
    protected function getTestEmailData(array $overrides = []): array
    {
        $defaults = [
            'from' => $this->getTestSenderEmail(),
            'to' => $this->getTestEmail(),
            'subject' => 'Test Email Subject',
            'html' => '<h1>Hello World</h1><p>This is a test email.</p>',
            'text' => 'Hello World\n\nThis is a test email.',
        ];

        return array_merge($defaults, $overrides);
    }

    /**
     * Assert that an array has the expected structure
     *
     * @param array<string> $expectedKeys
     * @param array<string, mixed> $actual
     */
    protected function assertArrayStructure(array $expectedKeys, array $actual): void
    {
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $actual, "Expected key '{$key}' not found in array");
        }
    }

    /**
     * Assert that a string is a valid email address
     */
    protected function assertValidEmail(string $email): void
    {
        $this->assertNotFalse(
            filter_var($email, FILTER_VALIDATE_EMAIL),
            "'{$email}' is not a valid email address"
        );
    }

    /**
     * Assert that a string is a valid URL
     */
    protected function assertValidUrl(string $url): void
    {
        $this->assertNotFalse(
            filter_var($url, FILTER_VALIDATE_URL),
            "'{$url}' is not a valid URL"
        );
    }
}
