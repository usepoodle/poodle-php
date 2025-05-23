<?php

declare(strict_types=1);

namespace Poodle;

use Poodle\Exception\ValidationException;
use Poodle\Http\HttpClient;
use Poodle\Model\Email;
use Poodle\Model\EmailResponse;

/**
 * Main Poodle SDK client for sending emails
 */
class PoodleClient
{
    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @param string|Configuration $apiKeyOrConfig
     * @param string|null $baseUrl
     */
    public function __construct(string|Configuration $apiKeyOrConfig, ?string $baseUrl = null)
    {
        if ($apiKeyOrConfig instanceof Configuration) {
            $this->config = $apiKeyOrConfig;
        } else {
            $this->config = new Configuration($apiKeyOrConfig, $baseUrl);
        }

        $this->httpClient = new HttpClient($this->config);
    }

    /**
     * Send an email
     *
     * @param Email|array<string, mixed> $email
     * @return EmailResponse
     */
    public function sendEmail(Email|array $email): EmailResponse
    {
        if (is_array($email)) {
            $email = $this->createEmailFromArray($email);
        }

        $response = $this->httpClient->post('v1/send-email', $email->toArray());

        return new EmailResponse($response);
    }

    /**
     * Send a simple email with basic parameters
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string|null $html
     * @param string|null $text
     * @return EmailResponse
     */
    public function send(
        string $from,
        string $to,
        string $subject,
        ?string $html = null,
        ?string $text = null
    ): EmailResponse {
        $email = new Email($from, $to, $subject, $html, $text);

        return $this->sendEmail($email);
    }

    /**
     * Send an HTML email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $html
     * @return EmailResponse
     */
    public function sendHtml(string $from, string $to, string $subject, string $html): EmailResponse
    {
        return $this->send($from, $to, $subject, $html);
    }

    /**
     * Send a plain text email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $text
     * @return EmailResponse
     */
    public function sendText(string $from, string $to, string $subject, string $text): EmailResponse
    {
        return $this->send($from, $to, $subject, null, $text);
    }

    /**
     * Get SDK configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    /**
     * Get SDK version
     */
    public function getVersion(): string
    {
        return $this->config->getSdkVersion();
    }

    /**
     * Create an Email instance from array data
     *
     * @param array<string, mixed> $data
     */
    private function createEmailFromArray(array $data): Email
    {
        // Validate required fields
        if (! isset($data['from'])) {
            throw ValidationException::missingField('from');
        }

        if (! isset($data['to'])) {
            throw ValidationException::missingField('to');
        }

        if (! isset($data['subject'])) {
            throw ValidationException::missingField('subject');
        }

        return new Email(
            $data['from'],
            $data['to'],
            $data['subject'],
            $data['html'] ?? null,
            $data['text'] ?? null
        );
    }
}
