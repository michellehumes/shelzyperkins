"""
ShelzyPerkins Affiliate Content Automation System

A complete end-to-end automation system for:
- Discovering trending Amazon products
- Storing product data in Airtable
- Generating SEO-optimized affiliate roundup posts
- Publishing to WordPress
- Creating social media and email content
"""

__version__ = "1.0.0"
__author__ = "ShelzyPerkins"

from .config import Config
from .airtable_client import AirtableClient
from .wordpress_client import WordPressClient
from .product_discovery import ProductDiscovery
from .content_generator import ContentGenerator
from .distribution_generator import DistributionGenerator
from .pipeline import AffiliatePipeline

__all__ = [
    "Config",
    "AirtableClient",
    "WordPressClient",
    "ProductDiscovery",
    "ContentGenerator",
    "DistributionGenerator",
    "AffiliatePipeline",
]
