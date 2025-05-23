<?php

declare(strict_types=1);

namespace Poodle;

use Poodle\Exception\ValidationException;

/**
 * Configuration class for Poodle SDK settings
 */
class Configuration
{
    /**
     * Default API base URL
     */
    public const DEFAULT_BASE_URL = 'https://api.usepoodle.com';

    /**
     * Default timeout in seconds
     */
    public const DEFAULT_TIMEOUT = 30.0;

    /**
     * Default connect timeout in seconds
     */
    public const DEFAULT_CONNECT_TIMEOUT = 10.0;

    /**
     * Maximum content size in bytes (10MB)
     */
    public const MAX_CONTENT_SIZE = 10 * 1024 * 1024;

    /**
     * SDK version
     */
    public const SDK_VERSION = '1.0.0';

    /**
     * @var string
     */
    private string $apiKey;

    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @var float
     */
    private float $timeout;

    /**
     * @var float
     */
    private float $connectTimeout;

    /**
     * @var bool
     */
    private bool $debug;

    /**
     * @var array<string, mixed>
     */
    private array $httpClientOptions;

    /**
     * @param string|null $apiKey
     * @param string|null $baseUrl
     * @param float|null $timeout
     * @param float|null $connectTimeout
     * @param bool $debug
     * @param array<string, mixed> $httpClientOptions
     */
    public function __construct(
        ?string $apiKey = null,
        ?string $baseUrl = null,
        ?float $timeout = null,
        ?float $connectTimeout = null,
        bool $debug = false,
        array $httpClientOptions = []
    ) {
        $this->apiKey = $apiKey ?? $this->getEnvironmentVariable('POODLE_API_KEY', '');
        $this->baseUrl = $baseUrl ?? $this->getEnvironmentVariable('POODLE_BASE_URL', self::DEFAULT_BASE_URL);
        $this->timeout = $timeout ?? (float) $this->getEnvironmentVariable('POODLE_TIMEOUT', (string) self::DEFAULT_TIMEOUT);
        $this->connectTimeout = $connectTimeout ?? (float) $this->getEnvironmentVariable('POODLE_CONNECT_TIMEOUT', (string) self::DEFAULT_CONNECT_TIMEOUT);
        $this->debug = $debug || (bool) $this->getEnvironmentVariable('POODLE_DEBUG', '0');
        $this->httpClientOptions = $httpClientOptions;

        $this->validate();
    }

    /**
     * Get API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get timeout
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Get connect timeout
     */
    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    /**
     * Check if debug mode is enabled
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Get HTTP client options
     *
     * @return array<string, mixed>
     */
    public function getHttpClientOptions(): array
    {
        return $this->httpClientOptions;
    }

    /**
     * Get SDK version
     */
    public function getSdkVersion(): string
    {
        return self::SDK_VERSION;
    }

    /**
     * Get User-Agent string
     */
    public function getUserAgent(): string
    {
        $phpVersion = PHP_VERSION;
        $sdkVersion = self::SDK_VERSION;

        return "poodle-php/{$sdkVersion} (PHP {$phpVersion})";
    }

    /**
     * Set API key
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        $this->validate();

        return $this;
    }

    /**
     * Set base URL
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        return $this;
    }

    /**
     * Set timeout
     */
    public function setTimeout(float $timeout): self
    {
        if ($timeout <= 0) {
            throw ValidationException::invalidFieldValue('timeout', (string) $timeout, 'Timeout must be greater than 0');
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set connect timeout
     */
    public function setConnectTimeout(float $connectTimeout): self
    {
        if ($connectTimeout <= 0) {
            throw ValidationException::invalidFieldValue('connectTimeout', (string) $connectTimeout, 'Connect timeout must be greater than 0');
        }

        $this->connectTimeout = $connectTimeout;

        return $this;
    }

    /**
     * Set debug mode
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Set HTTP client options
     *
     * @param array<string, mixed> $options
     */
    public function setHttpClientOptions(array $options): self
    {
        $this->httpClientOptions = $options;

        return $this;
    }

    /**
     * Validate configuration
     */
    private function validate(): void
    {
        if (empty($this->apiKey)) {
            throw ValidationException::missingField('apiKey');
        }

        if (empty($this->baseUrl)) {
            throw ValidationException::missingField('baseUrl');
        }

        if (! filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw ValidationException::invalidFieldValue('baseUrl', $this->baseUrl, 'Must be a valid URL');
        }

        if ($this->timeout <= 0) {
            throw ValidationException::invalidFieldValue('timeout', (string) $this->timeout, 'Must be greater than 0');
        }

        if ($this->connectTimeout <= 0) {
            throw ValidationException::invalidFieldValue('connectTimeout', (string) $this->connectTimeout, 'Must be greater than 0');
        }
    }

    /**
     * Get environment variable with fallback
     */
    private function getEnvironmentVariable(string $name, string $default = ''): string
    {
        $value = getenv($name);

        return $value !== false ? $value : $default;
    }
}
