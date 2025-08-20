#!/bin/bash

# TyrAds PHP SDK Release Creation Script
# Usage: ./.github/create-release.sh <version>
# Example: ./.github/create-release.sh v1.0.0

set -e

VERSION="$1"

if [ -z "$VERSION" ]; then
    echo "❌ Error: Version is required"
    echo "Usage: $0 <version>"
    echo "Example: $0 v1.0.0"
    exit 1
fi

# Validate version format
if [[ ! "$VERSION" =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "❌ Error: Invalid version format. Use format: v1.0.0"
    exit 1
fi

echo "🚀 Creating release for TyrAds PHP SDK $VERSION"

# Check if we're on main branch
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo "⚠️  Warning: You're on branch '$CURRENT_BRANCH', not 'main'"
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "❌ Aborted"
        exit 1
    fi
fi

# Check if working directory is clean
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ Error: Working directory is not clean. Please commit or stash changes."
    git status --short
    exit 1
fi

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Run tests
echo "🧪 Running tests..."
composer test

echo "🔍 Running linting..."
composer lint || echo "⚠️  Linting completed with warnings"

echo "✅ Validating package..."
composer validate --strict

echo "🔧 Testing compatibility..."
php "$(dirname "$0")/test-compatibility.php"

# Check if tag already exists
if git tag -l | grep -q "^$VERSION$"; then
    echo "❌ Error: Tag $VERSION already exists"
    exit 1
fi

# Create and push tag
echo "🏷️  Creating git tag: $VERSION"
git tag -a "$VERSION" -m "Release $VERSION"

echo "📤 Pushing tag to origin..."
git push origin "$VERSION"

echo "✅ Release $VERSION created successfully!"
echo ""
echo "📋 Next steps:"
echo "   1. Check GitHub Actions workflow: https://github.com/tyrads-com/tyrads-php-sdk-iframe/actions"
echo "   2. Monitor Packagist updates: https://packagist.org/packages/tyrads/tyrads-sdk"
echo "   3. Review GitHub release: https://github.com/tyrads-com/tyrads-php-sdk-iframe/releases"
echo ""
echo "📦 Users can install this version with:"
echo "   composer require tyrads/tyrads-sdk:$VERSION"