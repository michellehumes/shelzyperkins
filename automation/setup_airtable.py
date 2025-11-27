#!/usr/bin/env python3
"""
Airtable Table Setup Script
Creates the Products and Distribution tables with the correct schema.

Usage:
    python setup_airtable.py

Requires .env file with AIRTABLE_API_KEY and AIRTABLE_BASE_ID
"""

import requests
import sys
import os

def load_env():
    """Load environment variables from .env file."""
    env_path = os.path.join(os.path.dirname(__file__), '.env')
    if os.path.exists(env_path):
        with open(env_path, 'r') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, value = line.split('=', 1)
                    os.environ[key.strip()] = value.strip()

# Load environment
load_env()

API_KEY = os.environ.get('AIRTABLE_API_KEY', '')
BASE_ID = os.environ.get('AIRTABLE_BASE_ID', '')

if not API_KEY or not BASE_ID:
    print("Error: Missing AIRTABLE_API_KEY or AIRTABLE_BASE_ID in .env file")
    sys.exit(1)

headers = {
    "Authorization": f"Bearer {API_KEY}",
    "Content-Type": "application/json"
}

def create_table(name, fields):
    """Create a table in Airtable."""
    url = f"https://api.airtable.com/v0/meta/bases/{BASE_ID}/tables"

    payload = {
        "name": name,
        "fields": fields
    }

    response = requests.post(url, headers=headers, json=payload)

    if response.status_code == 200:
        print(f"✅ Created table: {name}")
        return True
    else:
        print(f"❌ Failed to create {name}: {response.status_code}")
        print(f"   Error: {response.text}")
        return False

def main():
    print("=" * 50)
    print("Airtable Table Setup")
    print("=" * 50)

    # Products table schema
    products_fields = [
        {"name": "keyword", "type": "singleLineText"},
        {"name": "niche", "type": "singleLineText"},
        {"name": "asin", "type": "singleLineText"},
        {"name": "product_url", "type": "url"},
        {"name": "title", "type": "multilineText"},
        {"name": "price", "type": "number", "options": {"precision": 2}},
        {"name": "rating", "type": "number", "options": {"precision": 1}},
        {"name": "review_count", "type": "number", "options": {"precision": 0}},
        {"name": "bullets", "type": "multilineText"},
        {"name": "image_url", "type": "url"},
        {"name": "affiliate_url", "type": "url"},
        {"name": "post_slug", "type": "singleLineText"},
        {
            "name": "status",
            "type": "singleSelect",
            "options": {
                "choices": [
                    {"name": "queued", "color": "blueLight2"},
                    {"name": "ready_to_write", "color": "yellowLight2"},
                    {"name": "drafted", "color": "orangeLight2"},
                    {"name": "published", "color": "greenLight2"},
                    {"name": "error", "color": "redLight2"}
                ]
            }
        },
        {"name": "published_url", "type": "url"},
        {"name": "notes", "type": "multilineText"},
    ]

    # Distribution table schema
    distribution_fields = [
        {"name": "post_slug", "type": "singleLineText"},
        {"name": "Instagram_caption", "type": "multilineText"},
        {"name": "TikTok_caption", "type": "multilineText"},
        {"name": "Pinterest_title_1", "type": "singleLineText"},
        {"name": "Pinterest_desc_1", "type": "multilineText"},
        {"name": "Pinterest_title_2", "type": "singleLineText"},
        {"name": "Pinterest_desc_2", "type": "multilineText"},
        {"name": "Pinterest_title_3", "type": "singleLineText"},
        {"name": "Pinterest_desc_3", "type": "multilineText"},
        {"name": "email_subject", "type": "singleLineText"},
        {"name": "email_blurb", "type": "multilineText"},
    ]

    print("\nCreating Products table...")
    products_ok = create_table("Products", products_fields)

    print("\nCreating Distribution table...")
    distribution_ok = create_table("Distribution", distribution_fields)

    print("\n" + "=" * 50)
    if products_ok and distribution_ok:
        print("✅ All tables created successfully!")
    else:
        print("⚠️  Some tables failed. You may need to add")
        print("   'schema.bases:write' scope to your token.")
        print("\n   To fix: Go to airtable.com/create/tokens")
        print("   Edit your token and add the scope.")
    print("=" * 50)

if __name__ == "__main__":
    main()
