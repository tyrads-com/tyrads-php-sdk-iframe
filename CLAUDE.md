# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the TyrAds PHP SDK for web iframe integration - a PHP library that wraps the TyrAds API for embedding offerwalls via iframe. The SDK handles authentication, configuration, and iframe URL generation.

## Installation and Setup

- PHP 5.5+ required
- Install dependencies: `composer install`
- Uses Guzzle HTTP client (supports versions 5.0, 6.0, and 7.0)

## Core Architecture

### Main Classes
- `TyrAdsSdk` - Main SDK entry point at `src/TyrAdsSdk.php:21`
- `Configuration` - API configuration and constants at `src/Configuration.php:58`
- `HttpClient` - HTTP communication wrapper at `src/HttpClient.php:22`
- `AuthenticationRequest` - User authentication data container at `src/Contract/AuthenticationRequest.php:157`
- `AuthenticationSign` - Authentication token wrapper at `src/Contract/AuthenticationSign.php`

### Key Features
- Factory method pattern: Use `TyrAdsSdk::make()` for easy instantiation
- Environment variable support via `Env` class for API credentials
- Guzzle version compatibility handling through `GuzzleCompatibility` helper
- Authentication flow: Request → Token → iframe URL generation

### API Integration
- Base API URL: `https://api.tyrads.com/v3.0`
- iframe URL: `https://sdk.tyrads.com`
- Authentication endpoint: `/auth`
- Requires API key and secret via headers: `X-API-Key`, `X-API-Secret`

### Development Notes
- Compatible with PHP 5.5+ (uses older array syntax and function patterns)
- PSR-4 autoloading: `Tyrads\TyradsSdk\` namespace maps to `src/`
- Linting configured with PHP CodeSniffer (PSR-12 standard, PHP 5.5+ compatible)
- No CI/CD configuration files present

## Development Commands

### Testing
- `composer test` - Run the full test suite with PestPHP
- `composer test:coverage` - Run tests with coverage report

### Linting
- `composer lint` - Run PHP CodeSniffer to check code style
- `composer lint:fix` - Auto-fix code style issues where possible

## Testing Setup

The project uses **PestPHP v1.22** for testing with the following structure:
- `tests/Unit/` - Unit tests for individual classes
- `tests/Integration/` - Integration tests for SDK workflows
- `phpunit.xml` - PHPUnit configuration for Pest
- Test environment variables configured for API credentials

### Test Coverage
- **Configuration class** - Constructor, getters, and URL generation
- **AuthenticationRequest class** - Validation, parameter handling, data parsing
- **TyrAdsSdk class** - Factory methods, iframe URL generation, parameter encoding
- **Integration tests** - End-to-end SDK usage patterns

The project uses PSR-12 coding standard with PHP 5.5+ compatibility adjustments:
- Constant visibility requirements disabled (not available in PHP 5.5)
- Line length limit: 120 characters
- 4-space indentation (no tabs)

## Environment Variables
- `TYRADS_API_KEY` - TyrAds API key
- `TYRADS_API_SECRET` - TyrAds API secret

## Usage Pattern
1. Create SDK instance with API credentials
2. Build `AuthenticationRequest` with user data (publisherUserId, age, gender)
3. Call `authenticate()` to get token
4. Generate iframe URL with `iframeUrl()`