<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Poodle\PoodleClient;
use Poodle\Exception\PoodleException;

// Initialize the Poodle client with your API key
$client = new PoodleClient('your_api_key_here');

try {
    // Send a simple email
    $response = $client->send(
        from: 'sender@yourdomain.com',
        to: 'recipient@example.com',
        subject: 'Hello from Poodle PHP SDK!',
        html: '<h1>Welcome!</h1><p>This is a test email sent using the Poodle PHP SDK.</p>',
        text: 'Welcome! This is a test email sent using the Poodle PHP SDK.'
    );

    echo "Email sent successfully!\n";
    echo "Message: " . $response->getMessage() . "\n";

    if ($response->isSuccessful()) {
        echo "Email was queued for delivery.\n";
    }

} catch (PoodleException $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";

    // Get additional context if available
    $context = $e->getContext();
    if (!empty($context)) {
        echo "Error context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
    }
}
