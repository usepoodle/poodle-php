# Poodle PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/usepoodle/poodle-php.svg)](https://packagist.org/packages/usepoodle/poodle-php)
[![Build Status](https://github.com/usepoodle/poodle-php/workflows/CI/badge.svg)](https://github.com/usepoodle/poodle-php/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](./LICENSE)

PHP SDK for the Poodle's email sending API.

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Features](#features)
- [Configuration](#configuration)
- [Usage Examples](#usage-examples)
- [API Reference](#api-reference)
- [Framework Integration](#framework-integration)
- [Development](#development)
- [Error Codes](#error-codes)
- [Contributing](#contributing)
- [License](#license)

## Installation

Install the SDK using Composer:

```bash
composer require usepoodle/poodle-php
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Poodle\PoodleClient;

// Initialize the client
$client = new PoodleClient('your_api_key_here');

// Send an email
$response = $client->send(
    from: 'sender@yourdomain.com',
    to: 'recipient@example.com',
    subject: 'Hello from Poodle!',
    html: '<h1>Hello World!</h1><p>This is a test email.</p>',
    text: 'Hello World! This is a test email.'
);

echo "Email sent! Message: " . $response->getMessage();
```

## Features

- Simple and intuitive API
- HTML and plain text email support
- Comprehensive error handling
- Built-in input validation
- PSR-12 compliant code
- 100% type coverage with PHPDoc
- Extensive test suite
- PHP 8.0+ support

## Configuration

### API Key

Set your API key in one of these ways:

**1. Pass directly to constructor:**

```php
$client = new PoodleClient('your_api_key_here');
```

**2. Use environment variable:**

```bash
export POODLE_API_KEY=your_api_key_here
```

```php
$client = new PoodleClient(); // Will use POODLE_API_KEY
```

**3. Use Configuration object:**

```php
use Poodle\Configuration;

$config = new Configuration(
    apiKey: 'your_api_key_here',
    baseUrl: 'https://api.usepoodle.com',
    timeout: 30.0,
    debug: true
);

$client = new PoodleClient($config);
```

### Environment Variables

| Variable                 | Default                     | Description                   |
| ------------------------ | --------------------------- | ----------------------------- |
| `POODLE_API_KEY`         | -                           | Your Poodle API key           |
| `POODLE_BASE_URL`        | `https://api.usepoodle.com` | API base URL                  |
| `POODLE_TIMEOUT`         | `30.0`                      | Request timeout in seconds    |
| `POODLE_CONNECT_TIMEOUT` | `10.0`                      | Connection timeout in seconds |
| `POODLE_DEBUG`           | `false`                     | Enable debug logging          |

## Usage Examples

### Basic Email Sending

```php
use Poodle\PoodleClient;

$client = new PoodleClient('your_api_key');

// HTML email
$response = $client->sendHtml(
    from: 'sender@yourdomain.com',
    to: 'recipient@example.com',
    subject: 'Welcome!',
    html: '<h1>Welcome to our service!</h1>'
);

// Plain text email
$response = $client->sendText(
    from: 'sender@yourdomain.com',
    to: 'recipient@example.com',
    subject: 'Welcome!',
    text: 'Welcome to our service!'
);
```

### Using the Email Model

```php
use Poodle\Model\Email;
use Poodle\PoodleClient;

$client = new PoodleClient('your_api_key');

// Create email object
$email = new Email(
    from: 'sender@yourdomain.com',
    to: 'recipient@example.com',
    subject: 'Welcome Email',
    html: '<h1>Hello!</h1><p>Welcome to our service!</p>',
    text: 'Hello! Welcome to our service!'
);

// Send the email
$response = $client->sendEmail($email);

if ($response->isSuccessful()) {
    echo "Email queued successfully!";
}
```

### Error Handling

```php
use Poodle\PoodleClient;
use Poodle\Exception\ValidationException;
use Poodle\Exception\AuthenticationException;
use Poodle\Exception\RateLimitException;
use Poodle\Exception\NetworkException;
use Poodle\Exception\PoodleException;

$client = new PoodleClient('your_api_key');

try {
    $response = $client->send(
        from: 'sender@yourdomain.com',
        to: 'recipient@example.com',
        subject: 'Test Email',
        html: '<h1>Hello!</h1>'
    );

    echo "Email sent successfully!";

} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage() . "\n";
    foreach ($e->getErrors() as $field => $errors) {
        echo "  {$field}: " . implode(', ', $errors) . "\n";
    }
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage() . "\n";
} catch (RateLimitException $e) {
    echo "Rate limit exceeded. Retry after: " . $e->getRetryAfter() . " seconds\n";
} catch (NetworkException $e) {
    echo "Network error: " . $e->getMessage() . "\n";
} catch (PoodleException $e) {
    echo "Poodle error: " . $e->getMessage() . "\n";
    echo "Context: " . json_encode($e->getContext()) . "\n";
}
```

For more usage patterns, see the [examples](./examples) directory.

## API Reference

### PoodleClient

The main client class for sending emails.

#### Constructor

```php
new PoodleClient(string|Configuration $apiKeyOrConfig, ?string $baseUrl = null)
```

#### Methods

- `send(string $from, string $to, string $subject, ?string $html = null, ?string $text = null): EmailResponse`
- `sendHtml(string $from, string $to, string $subject, string $html): EmailResponse`
- `sendText(string $from, string $to, string $subject, string $text): EmailResponse`
- `sendEmail(Email|array $email): EmailResponse`

### Email Model

Represents an email to be sent.

#### Constructor

```php
new Email(string $from, string $to, string $subject, ?string $html = null, ?string $text = null)
```

#### Methods

- `getFrom(): string` - Get sender email address
- `getTo(): string` - Get recipient email address
- `getSubject(): string` - Get email subject
- `getHtml(): ?string` - Get HTML content
- `getText(): ?string` - Get plain text content
- `toArray(): array` - Convert to array for API request

### EmailResponse

Represents the API response after sending an email.

#### Methods

- `isSuccessful(): bool` - Check if email was successfully queued
- `getMessage(): string` - Get response message
- `toArray(): array` - Convert to array
- `toJson(): string` - Convert to JSON

### Configuration

SDK configuration object.

#### Constructor

```php
new Configuration(
    ?string $apiKey = null,
    ?string $baseUrl = null,
    ?float $timeout = null,
    ?float $connectTimeout = null,
    bool $debug = false,
    array $httpClientOptions = []
)
```

## Framework Integration

### Laravel

Add to your `.env`:

```env
POODLE_API_KEY=your_api_key_here
```

Create a service:

```php
// app/Services/EmailService.php
use Poodle\PoodleClient;

class EmailService
{
    private PoodleClient $client;

    public function __construct()
    {
        $this->client = new PoodleClient(config('services.poodle.api_key'));
    }

    public function sendWelcomeEmail(string $email, string $name): void
    {
        $this->client->sendHtml(
            from: 'welcome@yourapp.com',
            to: $email,
            subject: "Welcome, {$name}!",
            html: view('emails.welcome', compact('name'))->render()
        );
    }
}
```

### Symfony

```yaml
# config/services.yaml
services:
  Poodle\PoodleClient:
    arguments:
      $apiKeyOrConfig: "%env(POODLE_API_KEY)%"
```

## Development

### Running Tests

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Code style check
composer cs-check

# Fix code style
composer cs-fix

# Static analysis
composer phpstan
```

### Requirements

- PHP 7.4 or higher
- ext-json
- GuzzleHTTP 7.0+

## Error Codes

| HTTP Code | Exception                 | Description                |
| --------- | ------------------------- | -------------------------- |
| 400       | `ValidationException`     | Invalid request data       |
| 401       | `AuthenticationException` | Invalid or missing API key |
| 403       | `AuthenticationException` | Insufficient permissions   |
| 408       | `NetworkException`        | Request timeout            |
| 429       | `RateLimitException`      | Rate limit exceeded        |
| 5xx       | `NetworkException`        | Server error               |

## Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) for details on the process for submitting pull requests and our [Code of Conduct](CODE_OF_CONDUCT.md).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
