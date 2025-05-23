<?php

declare(strict_types=1);

namespace Poodle\Tests\Unit;

use Poodle\Model\EmailResponse;
use Poodle\Tests\Support\TestCase;

/**
 * Test cases for EmailResponse model class
 */
class EmailResponseTest extends TestCase
{
    public function testConstructorWithSuccessResponse(): void
    {
        $data = [
            'success' => true,
            'message' => 'Email queued for sending',
        ];

        $response = new EmailResponse($data);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('Email queued for sending', $response->getMessage());
    }

    public function testConstructorWithErrorResponse(): void
    {
        $data = [
            'success' => false,
            'message' => 'Invalid email address',
        ];

        $response = new EmailResponse($data);

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Invalid email address', $response->getMessage());
    }

    public function testConstructorWithEmptyData(): void
    {
        $response = new EmailResponse([]);

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('', $response->getMessage());
    }

    public function testToArray(): void
    {
        $data = [
            'success' => true,
            'message' => 'Email queued for sending',
        ];

        $response = new EmailResponse($data);

        $this->assertEquals($data, $response->toArray());
    }

    public function testToJson(): void
    {
        $data = [
            'success' => true,
            'message' => 'Email queued for sending',
        ];

        $response = new EmailResponse($data);
        $json = $response->toJson();

        $this->assertIsString($json);
        $this->assertJson($json);
        $this->assertEquals($data, json_decode($json, true));
    }
}
