#!/bin/bash
# One-time setup script for GitHub Actions secrets
# Run this once on your Mac: ./setup_github_secrets.sh

echo "🔧 Setting up GitHub Actions secrets for ShelzyPerkins..."
echo ""

# Check if gh is installed
if ! command -v gh &> /dev/null; then
    echo "Installing GitHub CLI..."
    brew install gh
fi

# Check if logged in
if ! gh auth status &> /dev/null; then
    echo "Please log in to GitHub:"
    gh auth login
fi

# Check for .env file
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$SCRIPT_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ .env file not found at $ENV_FILE"
    echo "Please make sure your .env file exists with your credentials."
    exit 1
fi

# Load .env file
source "$ENV_FILE"

# Verify required variables
if [ -z "$AIRTABLE_API_KEY" ] || [ -z "$AIRTABLE_BASE_ID" ]; then
    echo "❌ Missing required variables in .env file"
    exit 1
fi

# Get the repo name
REPO=$(gh repo view --json nameWithOwner -q .nameWithOwner 2>/dev/null)
if [ -z "$REPO" ]; then
    echo "❌ Could not detect repository. Make sure you're in the shelzyperkins folder."
    exit 1
fi

echo "📦 Repository: $REPO"
echo ""

# Set secrets from .env file
echo "Setting AIRTABLE_API_KEY..."
gh secret set AIRTABLE_API_KEY -b "$AIRTABLE_API_KEY"

echo "Setting AIRTABLE_BASE_ID..."
gh secret set AIRTABLE_BASE_ID -b "$AIRTABLE_BASE_ID"

echo "Setting WORDPRESS_URL..."
gh secret set WORDPRESS_URL -b "$WORDPRESS_URL"

echo "Setting WORDPRESS_USERNAME..."
gh secret set WORDPRESS_USERNAME -b "$WORDPRESS_USERNAME"

echo "Setting WORDPRESS_APP_PASSWORD..."
gh secret set WORDPRESS_APP_PASSWORD -b "$WORDPRESS_APP_PASSWORD"

echo "Setting AMAZON_ASSOCIATE_TAG..."
gh secret set AMAZON_ASSOCIATE_TAG -b "$AMAZON_ASSOCIATE_TAG"

echo ""
echo "✅ All secrets configured!"
echo ""
echo "🚀 To trigger a post generation:"
echo "   gh workflow run generate-affiliate-post.yml"
echo ""
echo "📱 Or use the GitHub app on your iPhone:"
echo "   1. Open GitHub app"
echo "   2. Go to Actions tab"
echo "   3. Tap 'Generate Affiliate Post'"
echo "   4. Tap 'Run workflow'"
