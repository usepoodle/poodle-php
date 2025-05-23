<?php

declare(strict_types=1);

namespace Poodle\Model;

use Poodle\Configuration;
use Poodle\Exception\ValidationException;

/**
 * Email model representing an email to be sent
 */
class Email
{
    /**
     * @var string
     */
    private string $from;

    /**
     * @var string
     */
    private string $to;

    /**
     * @var string
     */
    private string $subject;

    /**
     * @var string|null
     */
    private ?string $html = null;

    /**
     * @var string|null
     */
    private ?string $text = null;

    /**
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string|null $html
     * @param string|null $text
     */
    public function __construct(
        string $from,
        string $to,
        string $subject,
        ?string $html = null,
        ?string $text = null
    ) {
        $this->setFrom($from);
        $this->setTo($to);
        $this->setSubject($subject);

        if ($html !== null) {
            $this->setHtml($html);
        }

        if ($text !== null) {
            $this->setText($text);
        }

        $this->validate();
    }

    /**
     * Get sender email address
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Set sender email address
     */
    public function setFrom(string $from): self
    {
        if (!$this->isValidEmail($from)) {
            throw ValidationException::invalidEmail($from, 'from');
        }

        $this->from = $from;

        return $this;
    }

    /**
     * Get recipient email address
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * Set recipient email address
     */
    public function setTo(string $to): self
    {
        if (!$this->isValidEmail($to)) {
            throw ValidationException::invalidEmail($to, 'to');
        }

        $this->to = $to;

        return $this;
    }

    /**
     * Get email subject
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set email subject
     */
    public function setSubject(string $subject): self
    {
        if (empty(trim($subject))) {
            throw ValidationException::missingField('subject');
        }

        $this->subject = $subject;

        return $this;
    }

    /**
     * Get HTML content
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * Set HTML content
     */
    public function setHtml(string $html): self
    {
        if (strlen($html) > Configuration::MAX_CONTENT_SIZE) {
            throw ValidationException::contentTooLarge('html', Configuration::MAX_CONTENT_SIZE);
        }

        $this->html = $html;

        return $this;
    }

    /**
     * Get plain text content
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set plain text content
     */
    public function setText(string $text): self
    {
        if (strlen($text) > Configuration::MAX_CONTENT_SIZE) {
            throw ValidationException::contentTooLarge('text', Configuration::MAX_CONTENT_SIZE);
        }

        $this->text = $text;

        return $this;
    }

    /**
     * Convert email to array for API request
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
        ];

        if ($this->html !== null) {
            $data['html'] = $this->html;
        }

        if ($this->text !== null) {
            $data['text'] = $this->text;
        }

        return $data;
    }

    /**
     * Validate email data
     */
    private function validate(): void
    {
        if ($this->html === null && $this->text === null) {
            throw ValidationException::invalidContent();
        }
    }

    /**
     * Validate email address format
     */
    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
