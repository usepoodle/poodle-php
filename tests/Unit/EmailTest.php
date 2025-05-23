<?php

declare(strict_types=1);

namespace Poodle\Tests\Unit;

use Poodle\Exception\ValidationException;
use Poodle\Model\Email;
use Poodle\Tests\Support\TestCase;

/**
 * Test cases for Email model class
 */
class EmailTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            '<p>HTML content</p>',
            'Text content'
        );

        $this->assertEquals('sender@example.com', $email->getFrom());
        $this->assertEquals('recipient@example.com', $email->getTo());
        $this->assertEquals('Test Subject', $email->getSubject());
        $this->assertEquals('<p>HTML content</p>', $email->getHtml());
        $this->assertEquals('Text content', $email->getText());
    }

    public function testConstructorWithHtmlOnly(): void
    {
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            '<p>HTML content</p>'
        );

        $this->assertEquals('<p>HTML content</p>', $email->getHtml());
        $this->assertNull($email->getText());
    }

    public function testConstructorWithTextOnly(): void
    {
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            null,
            'Text content'
        );

        $this->assertNull($email->getHtml());
        $this->assertEquals('Text content', $email->getText());
    }

    public function testConstructorWithInvalidFromEmail(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email address provided.');

        new Email(
            'invalid-email',
            'recipient@example.com',
            'Test Subject',
            '<p>HTML content</p>'
        );
    }

    public function testConstructorWithInvalidToEmail(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email address provided.');

        new Email(
            'sender@example.com',
            'invalid-email',
            'Test Subject',
            '<p>HTML content</p>'
        );
    }

    public function testConstructorWithEmptySubject(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing required field: subject');

        new Email(
            'sender@example.com',
            'recipient@example.com',
            '',
            '<p>HTML content</p>'
        );
    }

    public function testConstructorWithNoContent(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Email must contain either HTML content, text content, or both');

        new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject'
        );
    }

    public function testToArray(): void
    {
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            '<p>HTML content</p>',
            'Text content'
        );

        $expected = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Test Subject',
            'html' => '<p>HTML content</p>',
            'text' => 'Text content',
        ];

        $this->assertEquals($expected, $email->toArray());
    }

    public function testToArrayWithHtmlOnly(): void
    {
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            '<p>HTML content</p>'
        );

        $expected = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Test Subject',
            'html' => '<p>HTML content</p>',
        ];

        $this->assertEquals($expected, $email->toArray());
    }

    public function testToArrayWithTextOnly(): void
    {
        $email = new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            null,
            'Text content'
        );

        $expected = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Test Subject',
            'text' => 'Text content',
        ];

        $this->assertEquals($expected, $email->toArray());
    }

    public function testContentSizeValidation(): void
    {
        $largeContent = str_repeat('a', 11 * 1024 * 1024); // 11MB content

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Content size exceeds maximum allowed size');

        new Email(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            $largeContent
        );
    }
}
