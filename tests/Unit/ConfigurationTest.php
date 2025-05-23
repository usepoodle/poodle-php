<?php

declare(strict_types=1);

namespace Poodle\Tests\Unit;

use Poodle\Configuration;
use Poodle\Exception\ValidationException;
use Poodle\Tests\Support\TestCase;

/**
 * Test cases for Configuration class
 */
class ConfigurationTest extends TestCase
{
    public function testConstructorWithValidParameters(): void
    {
        $config = new Configuration(
            'test_api_key',
            'https://api.example.com',
            10.0,
            5.0,
            true
        );

        $this->assertEquals('test_api_key', $config->getApiKey());
        $this->assertEquals('https://api.example.com', $config->getBaseUrl());
        $this->assertEquals(10.0, $config->getTimeout());
        $this->assertEquals(5.0, $config->getConnectTimeout());
        $this->assertTrue($config->isDebug());
    }

    public function testConstructorWithDefaults(): void
    {
        putenv('POODLE_API_KEY=env_test_key');

        $config = new Configuration();

        $this->assertEquals('env_test_key', $config->getApiKey());
        $this->assertEquals(Configuration::DEFAULT_BASE_URL, $config->getBaseUrl());
        $this->assertEquals(Configuration::DEFAULT_TIMEOUT, $config->getTimeout());
        $this->assertEquals(Configuration::DEFAULT_CONNECT_TIMEOUT, $config->getConnectTimeout());
        $this->assertFalse($config->isDebug());

        putenv('POODLE_API_KEY');
    }

    public function testMissingApiKeyThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing required field: apiKey');

        new Configuration('');
    }

    public function testInvalidBaseUrlThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Must be a valid URL');

        new Configuration('test_key', 'invalid-url');
    }

    public function testNegativeTimeoutThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Must be greater than 0');

        new Configuration('test_key', null, -1.0);
    }

    public function testZeroConnectTimeoutThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Must be greater than 0');

        new Configuration('test_key', null, null, 0.0);
    }

    public function testSetApiKey(): void
    {
        $config = $this->createTestConfiguration();
        $config->setApiKey('new_key');

        $this->assertEquals('new_key', $config->getApiKey());
    }

    public function testSetBaseUrl(): void
    {
        $config = $this->createTestConfiguration();
        $config->setBaseUrl('https://new.api.com/');

        $this->assertEquals('https://new.api.com', $config->getBaseUrl());
    }

    public function testSetTimeout(): void
    {
        $config = $this->createTestConfiguration();
        $config->setTimeout(15.0);

        $this->assertEquals(15.0, $config->getTimeout());
    }

    public function testSetConnectTimeout(): void
    {
        $config = $this->createTestConfiguration();
        $config->setConnectTimeout(3.0);

        $this->assertEquals(3.0, $config->getConnectTimeout());
    }

    public function testSetDebug(): void
    {
        $config = $this->createTestConfiguration();
        $config->setDebug(false);

        $this->assertFalse($config->isDebug());
    }

    public function testSetHttpClientOptions(): void
    {
        $config = $this->createTestConfiguration();
        $options = ['verify' => false, 'proxy' => 'http://proxy.com'];
        $config->setHttpClientOptions($options);

        $this->assertEquals($options, $config->getHttpClientOptions());
    }

    public function testGetSdkVersion(): void
    {
        $config = $this->createTestConfiguration();

        $this->assertEquals(Configuration::SDK_VERSION, $config->getSdkVersion());
    }

    public function testGetUserAgent(): void
    {
        $config = $this->createTestConfiguration();
        $userAgent = $config->getUserAgent();

        $this->assertStringContainsString('poodle-php/', $userAgent);
        $this->assertStringContainsString(Configuration::SDK_VERSION, $userAgent);
        $this->assertStringContainsString(PHP_VERSION, $userAgent);
    }

    public function testEnvironmentVariables(): void
    {
        putenv('POODLE_API_KEY=env_key');
        putenv('POODLE_BASE_URL=https://env.api.com');
        putenv('POODLE_TIMEOUT=25');
        putenv('POODLE_CONNECT_TIMEOUT=8');
        putenv('POODLE_DEBUG=1');

        $config = new Configuration();

        $this->assertEquals('env_key', $config->getApiKey());
        $this->assertEquals('https://env.api.com', $config->getBaseUrl());
        $this->assertEquals(25.0, $config->getTimeout());
        $this->assertEquals(8.0, $config->getConnectTimeout());
        $this->assertTrue($config->isDebug());

        // Clean up
        putenv('POODLE_API_KEY');
        putenv('POODLE_BASE_URL');
        putenv('POODLE_TIMEOUT');
        putenv('POODLE_CONNECT_TIMEOUT');
        putenv('POODLE_DEBUG');
    }
}
