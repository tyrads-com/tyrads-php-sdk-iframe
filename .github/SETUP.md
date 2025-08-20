# GitHub Actions Setup

This document explains how to configure the GitHub Actions pipeline for automated testing, releases, and Packagist updates.

## Required GitHub Secrets

To enable the full CI/CD pipeline, you need to configure the following secrets in your GitHub repository settings:

### Repository Settings → Secrets and variables → Actions

1. **PACKAGIST_USERNAME** - Your Packagist.org username
2. **PACKAGIST_TOKEN** - Your Packagist API token

### How to get Packagist credentials:

1. Go to [Packagist.org](https://packagist.org)
2. Log in to your account
3. Go to your profile → "Show API Token"
4. Copy the API token and add it as `PACKAGIST_TOKEN` secret
5. Add your username as `PACKAGIST_USERNAME` secret

## Workflow Overview

### 1. CI/CD Pipeline (`ci-cd.yml`)
- **Triggers**: Push to `main`/`develop`, Pull requests to `main`
- **PHP Versions**: Tests against PHP 5.6 through 8.3
- **Steps**:
  - Code validation and linting
  - Test execution across all PHP versions
  - Coverage reporting (PHP 8.1)
  - Automatic release creation (main branch only)
  - Packagist update trigger

### 2. Version Bump (`version-bump.yml`)
- **Trigger**: Manual workflow dispatch
- **Options**: patch, minor, major version bumps
- **Process**: Updates `composer.json` version and commits changes

## Usage

### Automatic Releases
1. Push code to `main` branch
2. If tests pass, a release is automatically created using the version in `composer.json`
3. Packagist is automatically updated

### Manual Version Bumping
1. Go to Actions tab in GitHub
2. Select "Version Bump" workflow
3. Click "Run workflow"
4. Choose version bump type (patch/minor/major)
5. The workflow will update `composer.json` and commit the change

### Packagist Integration
- Packagist is automatically updated after successful releases
- Requires valid `PACKAGIST_USERNAME` and `PACKAGIST_TOKEN` secrets
- Updates are triggered via Packagist API

## Troubleshooting

### Release Not Created
- Check that the version in `composer.json` doesn't already have a corresponding Git tag
- Ensure tests are passing on the main branch

### Packagist Not Updating
- Verify `PACKAGIST_USERNAME` and `PACKAGIST_TOKEN` secrets are correctly configured
- Check that the package is properly configured on Packagist.org
- Review the workflow logs for API errors

### Test Failures
- Check PHP version compatibility
- Ensure all dependencies are properly declared in `composer.json`
- Review test output in the Actions tab