#!/bin/bash

# Test script to simulate the GitHub Actions pipeline locally

echo "🔍 Testing GitHub Actions Pipeline Components..."

# Test 1: Composer validation
echo -e "\n📋 Testing composer validation..."
composer validate
if [ $? -eq 0 ]; then
    echo "✅ Composer validation passed"
else
    echo "❌ Composer validation failed"
    exit 1
fi

# Test 2: Install dependencies
echo -e "\n📦 Testing dependency installation..."
composer install --prefer-dist --no-progress --no-interaction
if [ $? -eq 0 ]; then
    echo "✅ Dependencies installed successfully"
else
    echo "❌ Dependency installation failed"
    exit 1
fi

# Test 3: Code style check
echo -e "\n🎨 Testing code style..."
composer lint
if [ $? -eq 0 ]; then
    echo "✅ Code style check passed"
else
    echo "⚠️  Code style warnings found (non-blocking)"
fi

# Test 4: Run tests
echo -e "\n🧪 Running test suite..."
composer test
if [ $? -eq 0 ]; then
    echo "✅ All tests passed"
else
    echo "❌ Tests failed"
    exit 1
fi

# Test 5: Version extraction (simulate release job)
echo -e "\n🏷️  Testing version extraction..."
VERSION=$(php -r "echo json_decode(file_get_contents('composer.json'))->version;")
echo "Current version: $VERSION"
if [ ! -z "$VERSION" ]; then
    echo "✅ Version extraction successful"
else
    echo "❌ Version extraction failed"
    exit 1
fi

echo -e "\n🎉 All pipeline tests completed successfully!"
echo "📝 The GitHub Actions pipeline should work correctly when pushed to GitHub."
echo -e "\n📋 Next steps:"
echo "1. Push these files to your GitHub repository"
echo "2. Configure PACKAGIST_USERNAME and PACKAGIST_TOKEN secrets in GitHub"
echo "3. Watch the Actions tab for automatic CI/CD execution"