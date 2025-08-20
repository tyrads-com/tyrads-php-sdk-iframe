# Publishing Guide

This document outlines the automatic publishing process for the TyrAds PHP SDK to Packagist.

## Automatic Publishing

The package is automatically published to Packagist when changes are pushed to the `main` branch or when new releases are created.

### Workflow Triggers

The CD workflow (`.github/workflows/cd.yml`) is triggered by:

1. **Push to main branch** - Automatically creates version tags and updates Packagist
2. **Manual workflow dispatch** - Choose version bump type (patch/minor/major)
3. **Creating version tags** (e.g., `v1.0.0`) - Triggers release creation
4. **GitHub releases** - Publishes and creates release notes

### Publishing Process

When triggered, the workflow:

1. 🏷️ **Auto-versions** based on commit messages (patch/minor/major)
2. ✅ **Validates** the package structure and dependencies
3. 🧪 **Runs tests** to ensure code quality
4. 🔍 **Checks compatibility** across PHP versions
5. 📦 **Updates Packagist** automatically via GitHub webhook
6. 📋 **Creates GitHub release** with auto-generated changelog
7. 📧 **Notifies** about deployment status with version info

## Manual Publishing Steps

If you need to publish manually:

### 1. Prepare the Package

```bash
# Ensure all tests pass
composer test

# Run linting
composer lint

# Validate package structure
composer validate --strict

# Test compatibility
php .github/test-compatibility.php
```

### 2. Create a New Release

**Option A: Automatic Versioning (Recommended)**
Push to main branch - versions are automatically determined by commit messages:
```bash
git add .
git commit -m "feat: add new authentication method"  # Creates minor version bump
git push origin main
```

**Commit Message Conventions for Auto-Versioning:**
- `feat:` or `feature:` → **minor** version bump (0.1.0 → 0.2.0)
- `BREAKING:` or `major:` → **major** version bump (0.1.0 → 1.0.0)  
- Other messages → **patch** version bump (0.1.0 → 0.1.1)

**Option B: Manual Release Script**
```bash
# Use the release script for manual control
./.github/create-release.sh v1.0.0
```

**Option C: Manual Workflow Trigger**
- Go to GitHub Actions → "CD - Continuous Deployment" → "Run workflow"
- Choose version bump type (patch/minor/major)
- Optionally create GitHub release

**Option D: Traditional Git Tags**
```bash
# Create and push a version tag manually
git tag v1.0.0
git push origin v1.0.0
```

### 3. Monitor Packagist

- Visit [https://packagist.org/packages/tyrads/tyrads-sdk](https://packagist.org/packages/tyrads/tyrads-sdk)
- Verify the new version appears
- Check that download stats are updating

## Packagist Setup (One-time)

### Initial Submission

1. **Submit package** to Packagist:
   - Go to [https://packagist.org/packages/submit](https://packagist.org/packages/submit)
   - Enter repository URL: `https://github.com/tyrads/tyrads-php-sdk-iframe`
   - Click "Check" and then "Submit"

2. **Configure GitHub webhook**:
   - Packagist will provide a webhook URL
   - Add it to GitHub repository settings → Webhooks
   - This enables automatic updates on push

### Package Requirements

✅ **Required files present**:
- `composer.json` - Package metadata and dependencies
- `README.md` - Installation and usage documentation
- `LICENSE` - MIT license file
- Source code in `src/` directory

✅ **composer.json metadata**:
- Package name: `tyrads/tyrads-sdk`
- Description with PHP version compatibility
- Keywords for discoverability
- Support links (issues, source, docs)
- Homepage and author information

## Versioning Strategy

The package follows [Semantic Versioning](https://semver.org/):

- **MAJOR** (X.0.0) - Breaking changes
- **MINOR** (0.X.0) - New features (backward compatible)
- **PATCH** (0.0.X) - Bug fixes (backward compatible)

### Examples:
- `v0.1.0` - Initial release
- `v0.1.1` - Bug fix
- `v0.2.0` - New feature
- `v1.0.0` - Stable API release

## Installation Instructions

Users can install the package via Composer:

```bash
composer require tyrads/tyrads-sdk
```

## Troubleshooting

### Common Issues

1. **Package not updating on Packagist**
   - Check GitHub webhook is configured
   - Verify webhook deliveries in GitHub settings
   - Manually trigger update via Packagist dashboard

2. **CI/CD workflow failing**
   - Check GitHub Actions logs
   - Ensure all tests pass locally
   - Validate composer.json structure

3. **Compatibility issues**
   - Run compatibility test: `php .github/test-compatibility.php`
   - Check PHPUnit version constraints
   - Verify Guzzle version compatibility

### Manual Packagist Update

If automatic updates fail, manually trigger an update:

```bash
curl -XPOST -H'Content-type: application/json' \
  'https://packagist.org/api/update-package?username=USERNAME&apiToken=API_TOKEN' \
  -d'{"repository":{"url":"https://github.com/tyrads/tyrads-php-sdk-iframe"}}'
```

Replace `USERNAME` and `API_TOKEN` with your Packagist credentials.