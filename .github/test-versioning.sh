#!/bin/bash

# Test script for automated versioning logic
# Usage: ./.github/test-versioning.sh

set -e

echo "🧪 Testing automated versioning logic..."

# Simulate getting the latest tag
LATEST_TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "v0.0.0")
echo "📋 Latest tag: $LATEST_TAG"

# Parse semantic version
if [[ $LATEST_TAG =~ ^v([0-9]+)\.([0-9]+)\.([0-9]+)$ ]]; then
  MAJOR=${BASH_REMATCH[1]}
  MINOR=${BASH_REMATCH[2]}
  PATCH=${BASH_REMATCH[3]}
else
  MAJOR=0
  MINOR=0
  PATCH=0
fi

echo "📊 Current version: $MAJOR.$MINOR.$PATCH"

# Get recent commits for testing
COMMITS=$(git log $LATEST_TAG..HEAD --oneline --no-merges 2>/dev/null || git log --oneline --no-merges -5)
echo "📝 Recent commits:"
echo "$COMMITS"

# Test different bump types
echo ""
echo "🔍 Testing version bump detection:"

test_bump_type() {
  local test_commits="$1"
  local expected="$2"
  
  if echo "$test_commits" | grep -qi "BREAKING\|major:"; then
    BUMP_TYPE="major"
  elif echo "$test_commits" | grep -qi "feat\|feature\|minor:"; then
    BUMP_TYPE="minor"
  else
    BUMP_TYPE="patch"
  fi
  
  echo "   Input: '$test_commits' → $BUMP_TYPE (expected: $expected)"
  
  if [ "$BUMP_TYPE" = "$expected" ]; then
    echo "   ✅ PASS"
  else
    echo "   ❌ FAIL"
  fi
}

# Test cases
test_bump_type "fix: resolve authentication issue" "patch"
test_bump_type "feat: add new user management" "minor"
test_bump_type "BREAKING: remove deprecated methods" "major"
test_bump_type "docs: update README" "patch"
test_bump_type "feature: implement caching" "minor"
test_bump_type "major: rewrite core architecture" "major"

# Test actual version calculation
echo ""
echo "🧮 Testing version calculations:"

calculate_version() {
  local bump_type="$1"
  
  case $bump_type in
    major)
      NEW_MAJOR=$((MAJOR + 1))
      NEW_MINOR=0
      NEW_PATCH=0
      ;;
    minor)
      NEW_MAJOR=$MAJOR
      NEW_MINOR=$((MINOR + 1))
      NEW_PATCH=0
      ;;
    patch)
      NEW_MAJOR=$MAJOR
      NEW_MINOR=$MINOR
      NEW_PATCH=$((PATCH + 1))
      ;;
  esac
  
  NEW_TAG="v${NEW_MAJOR}.${NEW_MINOR}.${NEW_PATCH}"
  echo "   $LATEST_TAG + $bump_type → $NEW_TAG"
}

calculate_version "patch"
calculate_version "minor"
calculate_version "major"

# Test with actual recent commits
echo ""
echo "🎯 Based on actual recent commits:"
if echo "$COMMITS" | grep -qi "BREAKING\|major:"; then
  ACTUAL_BUMP="major"
elif echo "$COMMITS" | grep -qi "feat\|feature\|minor:"; then
  ACTUAL_BUMP="minor"
else
  ACTUAL_BUMP="patch"
fi

calculate_version "$ACTUAL_BUMP"
echo "   Suggested next version: $NEW_TAG"

echo ""
echo "✅ Versioning test complete!"