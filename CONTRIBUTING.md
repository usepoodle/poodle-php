# Contributing to Poodle PHP SDK

Thank you for your interest in contributing to the Poodle PHP SDK! We welcome contributions from the community.

## Development Setup

### Requirements

- PHP 7.4 or higher
- Composer
- Git

### Setup

1. Fork the repository
2. Clone your fork:

   ```bash
   git clone https://github.com/yourusername/poodle-php.git
   cd poodle-php
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

## Development Workflow

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test file
./vendor/bin/phpunit tests/Unit/ConfigurationTest.php
```

### Code Quality

```bash
# Check code style
composer cs-check

# Fix code style issues
composer cs-fix

# Run static analysis
composer phpstan

# Run Psalm
composer psalm
```

### Making Changes

1. Create a feature branch:

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes
3. Write or update tests
4. Ensure all tests pass and code style is correct
5. Commit your changes with a descriptive message
6. Push to your fork
7. Create a pull request

## Code Standards

### PHP Standards

- Follow PSR-12 coding standards
- Use strict typing (`declare(strict_types=1)`)
- Add comprehensive PHPDoc annotations
- Maintain PHP 7.4+ compatibility

### Testing Standards

- Write unit tests for all new functionality
- Maintain or improve test coverage
- Use descriptive test method names
- Test both success and failure scenarios

## License

By contributing to Poodle PHP SDK, you agree that your contributions will be licensed under the MIT License.
