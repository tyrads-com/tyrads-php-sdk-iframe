# PHP Compatibility Testing Guide

This document explains how the TyrAds PHP SDK handles testing across multiple PHP versions from 5.6 to 8.3.

## Supported PHP Versions

### Production Support (PHP 5.5+)
The SDK is designed to work with PHP 5.5+ as specified in `composer.json`:
```json
"require": {
    "php": ">=5.5"
}
```

### CI Testing Matrix
The CI workflow tests these PHP versions:
- **PHP 5.6** - Legacy support with Composer v1
- **PHP 7.0** - Legacy support with Composer v1  
- **PHP 7.1** - Legacy support with Composer v1
- **PHP 7.2** - Modern support with Composer v2
- **PHP 7.3** - Modern support with Composer v2
- **PHP 7.4** - Modern support with Composer v2
- **PHP 8.0** - Current support with Composer v2
- **PHP 8.1** - Current support with Composer v2
- **PHP 8.2** - Latest support with Composer v2 (reference)
- **PHP 8.3** - Latest support with Composer v2

## Compatibility Strategy

### Composer Version Management
- **PHP 5.6-7.1**: Uses Composer v1 (older, more compatible)
- **PHP 7.2+**: Uses Composer v2 (modern features)

### Dependency Resolution
- **PHP 8.2**: Uses locked `composer.lock` for consistent dependencies
- **All others**: Removes `composer.lock` and runs `composer update` to resolve compatible dependencies

### Error Suppression
All tests run with error suppression to avoid deprecation warnings:
```bash
php -d error_reporting='E_ERROR | E_PARSE'
```

### Tool Compatibility
- **PHPUnit**: Automatic version selection (4.8, 5.7, 6.5, 7.5, 8.5, 9.6)
- **PHP_CodeSniffer**: Only runs on PHP 7.2+ (requires modern PHP)
- **Compatibility Test**: Runs on all PHP versions

## How It Works

### For Modern PHP (7.2+)
```yaml
- Remove composer.lock (except PHP 8.2)
- Run composer update --prefer-stable
- Run compatibility test
- Run PHPUnit test suite
- Run PHP_CodeSniffer linting
```

### For Legacy PHP (5.6-7.1)
```yaml
- Remove composer.lock
- Run composer update --prefer-stable (with Composer v1)
- Run compatibility test
- Run PHPUnit if available, fallback to compatibility test only
- Skip linting (not compatible)
```

## Guzzle HTTP Client Compatibility

The SDK supports multiple Guzzle versions:
```json
"guzzlehttp/guzzle": "^5.3|^6.0|^7.0"
```

### Version Matrix
- **PHP 5.6-7.1**: Typically gets Guzzle 5.3 or 6.x
- **PHP 7.2-7.4**: Can use Guzzle 6.x or 7.x
- **PHP 8.0+**: Typically gets Guzzle 7.x

## Testing Locally

### Test All Scenarios
```bash
# Run the CI setup test
./.github/test-ci-setup.sh
```

### Test Specific PHP Version
```bash
# Install dependencies for older PHP
rm composer.lock
composer update --prefer-stable

# Run compatibility test
php .github/test-compatibility.php

# Run tests (if PHPUnit available)
vendor/bin/phpunit
```

### Test Version Detection
```bash
# Test automated versioning logic
./.github/test-versioning.sh
```

## Known Limitations

### PHP 5.6-7.1 Limitations
- Limited dev dependencies available
- Some newer PHPUnit features unavailable
- No linting support (PHP_CodeSniffer requires PHP 7.2+)
- May show deprecation warnings with newer dependencies

### Dependency Conflicts
- Some modern dependencies require PHP 8.1+
- CI automatically resolves to compatible versions per PHP version
- Lock file compatibility issues handled by removing lock file

## Troubleshooting

### "prefer-lowest option does not exist"
- Fixed: CI uses Composer v1 for older PHP versions
- Composer v1 doesn't support `--prefer-lowest`

### "Package requires php >=8.1"
- Fixed: CI removes `composer.lock` and resolves dependencies per PHP version
- Each PHP version gets compatible dependency versions

### "PHPUnit not found"
- Expected: Some PHP versions may not get PHPUnit installed
- Fallback: Runs compatibility test only

### Deprecation Warnings
- Expected: Older libraries may show warnings on newer PHP
- Suppressed: CI uses error_reporting setting to focus on real errors

## Best Practices

### For Development
1. Use PHP 8.2 for primary development (has locked dependencies)
2. Test compatibility script before committing: `php .github/test-compatibility.php`
3. Run CI setup test for major changes: `./.github/test-ci-setup.sh`

### For Releases
1. Ensure all CI tests pass across PHP versions
2. Compatibility test should pass on all versions
3. PHPUnit tests should pass where available

### For Dependencies
1. Keep production dependencies minimal and compatible
2. Use version constraints that support wide PHP range
3. Test with both oldest and newest supported versions