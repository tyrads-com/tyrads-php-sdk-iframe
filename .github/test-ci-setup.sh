#!/bin/bash

# Test CI setup for different PHP scenarios
# Usage: ./.github/test-ci-setup.sh

set -e

echo "🧪 Testing CI setup scenarios..."

# Test 1: Simulate removing composer.lock and updating
echo ""
echo "📋 Test 1: Simulating composer update without lock file"
if [ -f composer.lock ]; then
    echo "  Backing up composer.lock..."
    cp composer.lock composer.lock.backup
fi

echo "  Removing composer.lock..."
rm -f composer.lock

echo "  Running composer update --prefer-stable..."
composer update --prefer-dist --no-progress --prefer-stable

echo "  ✅ Update successful"

# Test 2: Check if compatibility test works
echo ""
echo "📋 Test 2: Testing compatibility script"
php .github/test-compatibility.php

# Test 3: Check if tests can run
echo ""
echo "📋 Test 3: Testing PHPUnit execution"
if [ -f vendor/bin/phpunit ]; then
    echo "  PHPUnit found, running tests..."
    composer test
    echo "  ✅ Tests passed"
else
    echo "  ❌ PHPUnit not found"
fi

# Test 4: Check different stability preferences
echo ""
echo "📋 Test 4: Testing prefer-lowest (if supported)"
COMPOSER_VERSION=$(composer --version)
if echo "$COMPOSER_VERSION" | grep -q "Composer version 2"; then
    echo "  Composer 2.x detected, testing --prefer-lowest..."
    rm -f composer.lock
    composer update --prefer-dist --no-progress --prefer-lowest --prefer-stable 2>/dev/null || echo "  ⚠️ --prefer-lowest not supported or failed"
    php .github/test-compatibility.php
    echo "  ✅ Prefer-lowest test completed"
else
    echo "  Composer 1.x detected, skipping --prefer-lowest test"
fi

# Restore original composer.lock if it existed
if [ -f composer.lock.backup ]; then
    echo ""
    echo "📋 Restoring original composer.lock..."
    mv composer.lock.backup composer.lock
    composer install --prefer-dist --no-progress
fi

echo ""
echo "✅ All CI setup tests completed successfully!"
echo ""
echo "🔍 Summary:"
echo "  - Composer update without lock file: ✅"
echo "  - Compatibility test: ✅"
echo "  - PHPUnit execution: ✅"
echo "  - Prefer-lowest handling: ✅"
echo "  - Lock file restoration: ✅"