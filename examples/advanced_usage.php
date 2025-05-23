<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Poodle\PoodleClient;
use Poodle\Model\Email;
use Poodle\Configuration;
use Poodle\Exception\PoodleException;

// Create a custom configuration
$config = new Configuration(
    apiKey: 'your_api_key_here',
    baseUrl: 'https://api.usepoodle.com',
    timeout: 30.0,
    connectTimeout: 10.0,
    debug: true
);

// Initialize the client with custom configuration
$client = new PoodleClient($config);

try {
    // Create an email object
    $email = new Email(
        from: 'sender@yourdomain.com',
        to: 'recipient@example.com',
        subject: 'Advanced Email Example',
        html: '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Welcome Email</title>
            </head>
            <body>
                <h1>Welcome to Our Service!</h1>
                <p>Dear Customer,</p>
                <p>Thank you for signing up. We\'re excited to have you on board!</p>
                <p>Best regards,<br>The Team</p>
            </body>
            </html>
        ',
        text: 'Welcome to Our Service! Dear Customer, Thank you for signing up. We\'re excited to have you on board! Best regards, The Team'
    );

    // Send the email
    $response = $client->sendEmail($email);

    echo "Email sent successfully!\n";
    echo "Message: " . $response->getMessage() . "\n";

    // Check response status
    if ($response->isSuccessful()) {
        echo "✅ Email was successfully queued for delivery.\n";
    } else {
        echo "❌ Email delivery failed.\n";
    }

    // Print full response as JSON
    echo "\nFull response:\n";
    echo $response->toJson() . "\n";

} catch (PoodleException $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";

    // Handle specific exception types
    if ($e instanceof \Poodle\Exception\ValidationException) {
        echo "Validation errors:\n";
        foreach ($e->getErrors() as $field => $errors) {
            echo "  {$field}: " . implode(', ', $errors) . "\n";
        }
    } elseif ($e instanceof \Poodle\Exception\AuthenticationException) {
        echo "Authentication failed. Please check your API key.\n";
    } elseif ($e instanceof \Poodle\Exception\RateLimitException) {
        echo "Rate limit exceeded.\n";
        if ($e->getRetryAfter()) {
            echo "Retry after: " . $e->getRetryAfter() . " seconds\n";
        }
    } elseif ($e instanceof \Poodle\Exception\NetworkException) {
        echo "Network error occurred.\n";
    }
}
