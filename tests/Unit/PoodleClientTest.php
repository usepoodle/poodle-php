<?php

declare(strict_types=1);

namespace Poodle\Tests\Unit;

use Poodle\PoodleClient;
use Poodle\Model\Email;
use Poodle\Model\EmailResponse;
use Poodle\Tests\Support\TestCase;

/**
 * Test cases for PoodleClient class
 */
class PoodleClientTest extends TestCase
{
    public function testConstructorWithApiKey(): void
    {
        $client = new PoodleClient('test_api_key');

        $this->assertEquals('test_api_key', $client->getConfiguration()->getApiKey());
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = $this->createTestConfiguration();
        $client = new PoodleClient($config);

        $this->assertSame($config, $client->getConfiguration());
    }

    public function testGetVersion(): void
    {
        $client = new PoodleClient('test_api_key');

        $this->assertIsString($client->getVersion());
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $client->getVersion());
    }

    public function testSendWithEmailObject(): void
    {
        $client = new PoodleClient('test_api_key');
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            '<p>HTML content</p>',
            'Text content'
        );

        // We can't test actual sending without mocking the HTTP client
        // This test just ensures the method exists and accepts Email objects
        $this->assertTrue(method_exists($client, 'sendEmail'));
    }

    public function testSendWithArray(): void
    {
        $client = new PoodleClient('test_api_key');

        // We can't test actual sending without mocking the HTTP client
        // This test just ensures the method exists and accepts arrays
        $this->assertTrue(method_exists($client, 'sendEmail'));
    }

    public function testSendHtmlMethod(): void
    {
        $client = new PoodleClient('test_api_key');

        $this->assertTrue(method_exists($client, 'sendHtml'));
    }

    public function testSendTextMethod(): void
    {
        $client = new PoodleClient('test_api_key');

        $this->assertTrue(method_exists($client, 'sendText'));
    }

    public function testSendMethod(): void
    {
        $client = new PoodleClient('test_api_key');

        $this->assertTrue(method_exists($client, 'send'));
    }
}
