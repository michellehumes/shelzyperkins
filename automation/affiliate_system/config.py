"""
Configuration module for the ShelzyPerkins Affiliate System.

All configuration is loaded from environment variables for security.
Create a .env file or set these variables in your environment.
"""

import os
from dataclasses import dataclass, field
from typing import Optional, Dict, List
import json


@dataclass
class AirtableConfig:
    """Airtable API configuration."""
    api_key: str = ""
    base_id: str = ""
    products_table: str = "Products"
    distribution_table: str = "Distribution"

    @property
    def is_configured(self) -> bool:
        return bool(self.api_key and self.base_id)


@dataclass
class WordPressConfig:
    """WordPress REST API configuration."""
    url: str = ""
    username: str = ""
    app_password: str = ""
    default_category_id: Optional[int] = None  # "Deals" category ID
    default_author_id: int = 1

    @property
    def is_configured(self) -> bool:
        return bool(self.url and self.username and self.app_password)

    @property
    def api_base(self) -> str:
        """Get the WordPress REST API base URL."""
        url = self.url.rstrip('/')
        return f"{url}/wp-json/wp/v2"


@dataclass
class AmazonConfig:
    """Amazon Product Advertising API configuration."""
    associate_tag: str = "shelzysdesigns-20"
    access_key: str = ""
    secret_key: str = ""
    partner_tag: str = ""
    region: str = "us-east-1"
    marketplace: str = "www.amazon.com"

    # Category node IDs for Amazon
    category_nodes: Dict[str, str] = field(default_factory=lambda: {
        "beauty": "3760911",
        "home": "1055398",
        "kitchen": "284507",
        "tech": "172282",
        "fashion": "7141123011",
        "baby": "165796011",
        "pets": "2619533011",
        "outdoor": "3375251",
        "fitness": "3407731",
        "office": "1064954",
    })

    @property
    def is_configured(self) -> bool:
        """Check if PA-API is configured (optional - can use scraping fallback)."""
        return bool(self.access_key and self.secret_key)


@dataclass
class ContentConfig:
    """Content generation configuration."""
    author_name: str = "Shelzy Perkins"
    site_name: str = "ShelzyPerkins"
    site_url: str = "https://shelzyperkins.com"
    logo_url: str = "https://shelzyperkins.com/logo.png"
    about_url: str = "https://shelzyperkins.com/about/"

    # Default SEO settings
    default_tags: List[str] = field(default_factory=lambda: [
        "amazon finds", "deals", "affordable"
    ])

    # Content templates
    affiliate_disclosure: str = (
        "This post contains affiliate links. As an Amazon Associate, I earn from "
        "qualifying purchases at no extra cost to you. All opinions are my own."
    )

    # CTA templates
    cta_templates: Dict[str, str] = field(default_factory=lambda: {
        "check_price": "Check Current Price on Amazon",
        "buy_now": "Buy Now on Amazon",
        "see_deal": "See This Deal",
        "shop_now": "Shop Now",
    })


@dataclass
class NicheConfig:
    """Niche-specific settings for content generation."""
    name: str
    keywords: List[str]
    hashtags: List[str]
    emoji: str
    category_id: Optional[int] = None  # WordPress category ID


class Config:
    """
    Main configuration class that loads all settings from environment variables.

    Required environment variables:
        - AIRTABLE_API_KEY: Your Airtable API key
        - AIRTABLE_BASE_ID: Your Airtable base ID
        - WP_URL: WordPress site URL (e.g., https://shelzyperkins.com)
        - WP_USERNAME: WordPress username
        - WP_APP_PASSWORD: WordPress application password

    Optional environment variables:
        - AIRTABLE_PRODUCTS_TABLE_NAME: Products table name (default: "Products")
        - AIRTABLE_DISTRIBUTION_TABLE_NAME: Distribution table name (default: "Distribution")
        - AMAZON_ASSOC_TAG: Amazon Associate tag (default: "shelzysdesigns-20")
        - AMAZON_ACCESS_KEY: Amazon PA-API access key
        - AMAZON_SECRET_KEY: Amazon PA-API secret key
    """

    def __init__(self, env_file: Optional[str] = None):
        """
        Initialize configuration from environment variables.

        Args:
            env_file: Optional path to .env file to load
        """
        if env_file:
            self._load_env_file(env_file)

        self.airtable = self._load_airtable_config()
        self.wordpress = self._load_wordpress_config()
        self.amazon = self._load_amazon_config()
        self.content = self._load_content_config()
        self.niches = self._load_niche_configs()

    def _load_env_file(self, path: str) -> None:
        """Load environment variables from a .env file."""
        try:
            with open(path, 'r') as f:
                for line in f:
                    line = line.strip()
                    if line and not line.startswith('#') and '=' in line:
                        key, value = line.split('=', 1)
                        os.environ[key.strip()] = value.strip().strip('"').strip("'")
        except FileNotFoundError:
            print(f"Warning: .env file not found at {path}")

    def _load_airtable_config(self) -> AirtableConfig:
        """Load Airtable configuration from environment."""
        return AirtableConfig(
            api_key=os.getenv("AIRTABLE_API_KEY", ""),
            base_id=os.getenv("AIRTABLE_BASE_ID", ""),
            products_table=os.getenv("AIRTABLE_PRODUCTS_TABLE_NAME", "Products"),
            distribution_table=os.getenv("AIRTABLE_DISTRIBUTION_TABLE_NAME", "Distribution"),
        )

    def _load_wordpress_config(self) -> WordPressConfig:
        """Load WordPress configuration from environment."""
        return WordPressConfig(
            url=os.getenv("WP_URL", "https://shelzyperkins.com"),
            username=os.getenv("WP_USERNAME", ""),
            app_password=os.getenv("WP_APP_PASSWORD", ""),
            default_category_id=int(os.getenv("WP_DEFAULT_CATEGORY_ID", "0")) or None,
        )

    def _load_amazon_config(self) -> AmazonConfig:
        """Load Amazon configuration from environment."""
        return AmazonConfig(
            associate_tag=os.getenv("AMAZON_ASSOC_TAG", "shelzysdesigns-20"),
            access_key=os.getenv("AMAZON_ACCESS_KEY", ""),
            secret_key=os.getenv("AMAZON_SECRET_KEY", ""),
            partner_tag=os.getenv("AMAZON_PARTNER_TAG", os.getenv("AMAZON_ASSOC_TAG", "shelzysdesigns-20")),
        )

    def _load_content_config(self) -> ContentConfig:
        """Load content configuration."""
        return ContentConfig(
            author_name=os.getenv("AUTHOR_NAME", "Shelzy Perkins"),
            site_name=os.getenv("SITE_NAME", "ShelzyPerkins"),
            site_url=os.getenv("SITE_URL", "https://shelzyperkins.com"),
        )

    def _load_niche_configs(self) -> Dict[str, NicheConfig]:
        """Load niche-specific configurations."""
        return {
            "beauty": NicheConfig(
                name="Beauty",
                keywords=["skincare", "makeup", "beauty dupes", "cosmetics", "self-care"],
                hashtags=["#beautyfinds", "#skincare", "#makeupdupes", "#amazonbeauty", "#beautyonabudget"],
                emoji="💄",
            ),
            "home": NicheConfig(
                name="Home",
                keywords=["home decor", "organization", "home upgrades", "interior design"],
                hashtags=["#homedecor", "#amazonhome", "#homeorganization", "#cozyvibes", "#homefinds"],
                emoji="🏠",
            ),
            "kitchen": NicheConfig(
                name="Kitchen",
                keywords=["kitchen gadgets", "cooking", "meal prep", "kitchen organization"],
                hashtags=["#kitchenfinds", "#cookinghacks", "#amazonkitchen", "#foodprep", "#kitchengadgets"],
                emoji="🍳",
            ),
            "tech": NicheConfig(
                name="Tech",
                keywords=["tech gadgets", "electronics", "smart home", "accessories"],
                hashtags=["#techfinds", "#gadgets", "#amazontech", "#smartthome", "#techdeals"],
                emoji="📱",
            ),
            "fashion": NicheConfig(
                name="Fashion",
                keywords=["fashion finds", "clothing", "accessories", "style dupes"],
                hashtags=["#fashionfinds", "#amazonfashion", "#styledupes", "#ootd", "#budgetfashion"],
                emoji="👗",
            ),
            "fitness": NicheConfig(
                name="Fitness",
                keywords=["fitness gear", "workout", "home gym", "athleisure"],
                hashtags=["#fitnessfinds", "#workoutgear", "#homegym", "#fitnessmotivation", "#amazonfit"],
                emoji="💪",
            ),
            "baby": NicheConfig(
                name="Baby & Kids",
                keywords=["baby essentials", "kids", "parenting", "nursery"],
                hashtags=["#babyfinds", "#momhacks", "#amazonbaby", "#parentingtips", "#nurseryinspo"],
                emoji="👶",
            ),
            "pets": NicheConfig(
                name="Pets",
                keywords=["pet supplies", "dog", "cat", "pet accessories"],
                hashtags=["#petfinds", "#dogsofinstagram", "#catsofinstagram", "#amazonpets", "#petlover"],
                emoji="🐕",
            ),
        }

    def validate(self) -> Dict[str, bool]:
        """
        Validate configuration and return status of each component.

        Returns:
            Dictionary with component names and their configuration status
        """
        return {
            "airtable": self.airtable.is_configured,
            "wordpress": self.wordpress.is_configured,
            "amazon_paapi": self.amazon.is_configured,
        }

    def get_missing_config(self) -> List[str]:
        """Get list of missing required configuration."""
        missing = []

        if not self.airtable.api_key:
            missing.append("AIRTABLE_API_KEY")
        if not self.airtable.base_id:
            missing.append("AIRTABLE_BASE_ID")
        if not self.wordpress.url:
            missing.append("WP_URL")
        if not self.wordpress.username:
            missing.append("WP_USERNAME")
        if not self.wordpress.app_password:
            missing.append("WP_APP_PASSWORD")

        return missing

    def print_status(self) -> None:
        """Print configuration status."""
        print("\n" + "=" * 50)
        print("ShelzyPerkins Affiliate System Configuration")
        print("=" * 50)

        status = self.validate()

        for component, configured in status.items():
            icon = "✓" if configured else "✗"
            status_text = "Configured" if configured else "Not Configured"
            print(f"  {icon} {component.title()}: {status_text}")

        missing = self.get_missing_config()
        if missing:
            print("\nMissing required configuration:")
            for var in missing:
                print(f"  - {var}")
            print("\nPlease set these environment variables or add them to your .env file.")
        else:
            print("\n✓ All required configuration is present!")

        print("=" * 50 + "\n")


def create_env_template() -> str:
    """
    Generate a template .env file with all configuration options.

    Returns:
        String content for a .env template file
    """
    return '''# ShelzyPerkins Affiliate System Configuration
# Copy this to .env and fill in your values

# ===========================================
# AIRTABLE CONFIGURATION (Required)
# ===========================================
# Get your API key from: https://airtable.com/account
AIRTABLE_API_KEY=your_airtable_api_key_here

# Find your Base ID in the Airtable API documentation
# It looks like: appXXXXXXXXXXXXXX
AIRTABLE_BASE_ID=your_base_id_here

# Table names (optional - defaults shown)
AIRTABLE_PRODUCTS_TABLE_NAME=Products
AIRTABLE_DISTRIBUTION_TABLE_NAME=Distribution

# ===========================================
# WORDPRESS CONFIGURATION (Required)
# ===========================================
# Your WordPress site URL
WP_URL=https://shelzyperkins.com

# WordPress username
WP_USERNAME=your_wordpress_username

# WordPress Application Password (not your regular password)
# Create one at: WordPress > Users > Profile > Application Passwords
WP_APP_PASSWORD=your_application_password_here

# Default category ID for posts (optional)
WP_DEFAULT_CATEGORY_ID=

# ===========================================
# AMAZON CONFIGURATION
# ===========================================
# Your Amazon Associates tag
AMAZON_ASSOC_TAG=shelzysdesigns-20

# Amazon Product Advertising API (optional - enables real-time product data)
# Sign up at: https://affiliate-program.amazon.com/assoc_credentials/home
AMAZON_ACCESS_KEY=your_amazon_access_key
AMAZON_SECRET_KEY=your_amazon_secret_key
AMAZON_PARTNER_TAG=shelzysdesigns-20

# ===========================================
# SITE CONFIGURATION (Optional)
# ===========================================
AUTHOR_NAME=Shelzy Perkins
SITE_NAME=ShelzyPerkins
SITE_URL=https://shelzyperkins.com
'''


if __name__ == "__main__":
    # Print env template if run directly
    print(create_env_template())
