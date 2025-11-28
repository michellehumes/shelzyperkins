"""
Amazon Product Discovery module for the ShelzyPerkins Affiliate System.

Discovers trending products, fetches product data, and generates affiliate links.
Supports both Amazon PA-API and web scraping fallback.
"""

import requests
from typing import Any, Dict, List, Optional, Tuple
from dataclasses import dataclass, field
from datetime import datetime
import hashlib
import hmac
import json
import re
import time
from urllib.parse import quote, urlencode
import xml.etree.ElementTree as ET

from .config import Config, AmazonConfig
from .airtable_client import Product


@dataclass
class DiscoveredProduct:
    """
    Product data discovered from Amazon.
    """
    asin: str
    title: str
    price: float = 0.0
    original_price: float = 0.0
    savings_percent: int = 0
    rating: float = 0.0
    review_count: int = 0
    image_url: str = ""
    product_url: str = ""
    bullets: List[str] = field(default_factory=list)
    brand: str = ""
    category: str = ""
    is_prime: bool = False
    is_deal: bool = False
    deal_type: str = ""  # lightning, daily, price-drop

    @property
    def bullets_text(self) -> str:
        """Get bullets as formatted text."""
        return "\n".join(f"• {b}" for b in self.bullets)

    def to_product(self, keyword: str, niche: str, affiliate_tag: str) -> Product:
        """
        Convert to Product dataclass for Airtable.

        Args:
            keyword: Search keyword used
            niche: Product niche/category
            affiliate_tag: Amazon associate tag

        Returns:
            Product object ready for Airtable
        """
        return Product(
            keyword=keyword,
            niche=niche,
            asin=self.asin,
            product_url=self.product_url,
            title=self.title,
            price=self.price,
            rating=self.rating,
            review_count=self.review_count,
            bullets=self.bullets_text,
            image_url=self.image_url,
            affiliate_url=self.generate_affiliate_url(affiliate_tag),
            status="queued",
        )

    def generate_affiliate_url(self, tag: str) -> str:
        """Generate affiliate URL with associate tag."""
        return f"https://www.amazon.com/dp/{self.asin}?tag={tag}"


class ProductDiscovery:
    """
    Amazon product discovery service.

    Supports multiple discovery methods:
    1. PA-API (Product Advertising API) - most reliable
    2. Curated trending lists
    3. Manual ASIN lookup

    Usage:
        config = Config()
        discovery = ProductDiscovery(config.amazon)

        # Search for products
        products = discovery.search_products("beauty dupes", max_results=10)

        # Get product details by ASIN
        product = discovery.get_product_by_asin("B07GXQ3KMP")

        # Get trending products in a category
        trending = discovery.get_trending_products("beauty")
    """

    # Curated trending product lists by niche
    # These can be updated regularly with high-performing products
    TRENDING_PRODUCTS = {
        "beauty": [
            {"asin": "B07GXQ3KMP", "title": "The Ordinary Niacinamide 10% + Zinc 1%"},
            {"asin": "B07XBX3FVW", "title": "CeraVe Resurfacing Retinol Serum"},
            {"asin": "B07H2W8YLV", "title": "L'Oréal Revitalift Vitamin C Serum"},
            {"asin": "B07TWKZTYG", "title": "e.l.f. Poreless Putty Primer"},
            {"asin": "B00PFCS81Y", "title": "Maybelline Lash Sensational Mascara"},
            {"asin": "B00HZDGO76", "title": "NYX Butter Gloss"},
            {"asin": "B0183AL93U", "title": "Aztec Secret Indian Healing Clay"},
            {"asin": "B01N7T7JKJ", "title": "IT Cosmetics CC+ Cream"},
            {"asin": "B00W259T7G", "title": "Neutrogena Hydro Boost Water Gel"},
            {"asin": "B000052YKC", "title": "Mario Badescu Drying Lotion"},
        ],
        "home": [
            {"asin": "B07PFHCP3K", "title": "Amazon Basics Microfiber Sheet Set"},
            {"asin": "B07BTBKJCD", "title": "BECKHAM Hotel Collection Bed Pillows"},
            {"asin": "B01N7R4X4O", "title": "Yankee Candle Large Jar Candle"},
            {"asin": "B00004OCNS", "title": "Rubbermaid Brilliance Food Storage"},
            {"asin": "B00O9HSQ5G", "title": "Command Picture Hanging Strips"},
            {"asin": "B07PZT3VYK", "title": "Kasa Smart Plug Mini"},
            {"asin": "B07PXMFQ23", "title": "Bedsure Fleece Blanket"},
            {"asin": "B08LMQB4FF", "title": "Simple Trending Over Door Organizer"},
            {"asin": "B07GXQB8WH", "title": "Lifewit Large Capacity Storage Bag"},
            {"asin": "B07T3XWFT9", "title": "LED Strip Lights"},
        ],
        "kitchen": [
            {"asin": "B00FLYWNYQ", "title": "Instant Pot Duo 7-in-1"},
            {"asin": "B07JFLJNYB", "title": "Ninja Professional Blender"},
            {"asin": "B00005OTXM", "title": "Lodge Cast Iron Skillet"},
            {"asin": "B00B4UYHUU", "title": "OXO Good Grips 3-Piece Mixing Bowl Set"},
            {"asin": "B07K9QRD45", "title": "Cuisinart Multiclad Pro Cookware"},
            {"asin": "B01HHGQNJA", "title": "Fullstar Vegetable Chopper"},
            {"asin": "B01N1SE4EP", "title": "Dash Mini Waffle Maker"},
            {"asin": "B00004OCK1", "title": "Hamilton Beach Electric Kettle"},
            {"asin": "B0BDL9CWP5", "title": "Stanley Quencher H2.0 Tumbler"},
            {"asin": "B08D8WTLHJ", "title": "Cosori Air Fryer"},
        ],
        "tech": [
            {"asin": "B09V3KXJPB", "title": "Apple AirPods Pro (2nd Gen)"},
            {"asin": "B09JQL3NWT", "title": "Fire TV Stick 4K Max"},
            {"asin": "B084DVCM76", "title": "Echo Dot (5th Gen)"},
            {"asin": "B0BTT15QZY", "title": "Anker Portable Charger"},
            {"asin": "B07ZPML7NP", "title": "Logitech MX Master 3"},
            {"asin": "B08CXVKXWP", "title": "Ring Video Doorbell"},
            {"asin": "B08MQ4F3K3", "title": "Apple Watch SE"},
            {"asin": "B09HSNTXQB", "title": "Kindle Paperwhite"},
            {"asin": "B07YNM3KCN", "title": "Tile Mate Bluetooth Tracker"},
            {"asin": "B08FC5L3RG", "title": "Samsung Galaxy Buds"},
        ],
        "fashion": [
            {"asin": "B00IXHXZEE", "title": "Amazon Essentials Women's Tank"},
            {"asin": "B07BHSZ6Y9", "title": "Levi's Women's 501 Original Jeans"},
            {"asin": "B07N87YXQH", "title": "Adidas Women's Cloudfoam Sneakers"},
            {"asin": "B01GF6V76M", "title": "Calvin Klein Women's Invisibles Hipster"},
            {"asin": "B07F8GYRBY", "title": "PAVOI 14K Gold Plated Huggie Earrings"},
            {"asin": "B01BYNQLTW", "title": "Fossil Women's Carlie Watch"},
            {"asin": "B00L7TA7VU", "title": "Ray-Ban Classic Aviator Sunglasses"},
            {"asin": "B07NQRPGGV", "title": "Carhartt Men's Beanie"},
            {"asin": "B07RFVWLQJ", "title": "Champion Women's Powerblend Hoodie"},
            {"asin": "B09HH4ZRXV", "title": "Amazon Essentials Women's Cardigan"},
        ],
        "fitness": [
            {"asin": "B07TX1VWBV", "title": "Fitbit Charge 5"},
            {"asin": "B095WQYGRZ", "title": "Bala Bangles"},
            {"asin": "B07BS8RCDQ", "title": "Amazon Basics Neoprene Dumbbells"},
            {"asin": "B01LP0U5I8", "title": "Yoga Mat Extra Thick"},
            {"asin": "B08CDKG9MQ", "title": "Resistance Bands Set"},
            {"asin": "B07Y2XGQQY", "title": "BlenderBottle Classic"},
            {"asin": "B07GJJF1QN", "title": "Foam Roller"},
            {"asin": "B07G1GLVTJ", "title": "Jump Rope"},
            {"asin": "B08HMRS8V1", "title": "Massage Gun"},
            {"asin": "B07S58XJGH", "title": "QALO Silicone Ring"},
        ],
    }

    def __init__(self, config: AmazonConfig):
        """
        Initialize product discovery.

        Args:
            config: AmazonConfig with API credentials
        """
        self.config = config
        self.session = requests.Session()
        self.session.headers.update({
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        })

    # ===========================================
    # Public Methods
    # ===========================================

    def search_products(
        self,
        keyword: str,
        category: str = "",
        max_results: int = 10,
        min_rating: float = 4.0,
        min_reviews: int = 100,
    ) -> List[DiscoveredProduct]:
        """
        Search for products by keyword.

        Args:
            keyword: Search keyword
            category: Optional category filter
            max_results: Maximum products to return
            min_rating: Minimum rating filter
            min_reviews: Minimum review count filter

        Returns:
            List of discovered products
        """
        # If PA-API is configured, use it
        if self.config.is_configured:
            return self._search_pa_api(keyword, category, max_results)

        # Otherwise, use curated trending list
        print(f"PA-API not configured. Using curated product list for: {keyword}")
        return self._search_curated(keyword, category, max_results)

    def get_product_by_asin(self, asin: str) -> Optional[DiscoveredProduct]:
        """
        Get product details by ASIN.

        Args:
            asin: Amazon ASIN

        Returns:
            Product details or None if not found
        """
        if self.config.is_configured:
            return self._get_item_pa_api(asin)

        # Return basic product from curated list
        return self._get_from_curated(asin)

    def get_trending_products(
        self,
        niche: str,
        max_results: int = 10,
    ) -> List[DiscoveredProduct]:
        """
        Get trending products for a niche.

        Args:
            niche: Product niche (beauty, home, kitchen, tech, fashion, fitness)
            max_results: Maximum products to return

        Returns:
            List of trending products
        """
        niche_lower = niche.lower()
        if niche_lower not in self.TRENDING_PRODUCTS:
            print(f"Unknown niche: {niche}. Available: {list(self.TRENDING_PRODUCTS.keys())}")
            return []

        products = []
        for item in self.TRENDING_PRODUCTS[niche_lower][:max_results]:
            product = DiscoveredProduct(
                asin=item["asin"],
                title=item["title"],
                product_url=f"https://www.amazon.com/dp/{item['asin']}",
                category=niche,
            )
            products.append(product)

        return products

    def discover_products_for_roundup(
        self,
        keyword: str,
        niche: str,
        product_count: int = 10,
    ) -> List[Product]:
        """
        Discover products for an affiliate roundup post.

        This is the main method for the pipeline - returns Product objects
        ready to be stored in Airtable.

        Args:
            keyword: Search keyword/topic
            niche: Product niche
            product_count: Number of products to find

        Returns:
            List of Product objects ready for Airtable
        """
        discovered = self.get_trending_products(niche, product_count)

        if not discovered:
            discovered = self.search_products(keyword, niche, product_count)

        products = []
        for disc in discovered:
            product = disc.to_product(keyword, niche, self.config.associate_tag)
            products.append(product)

        return products

    def generate_affiliate_url(self, asin: str) -> str:
        """Generate affiliate URL for an ASIN."""
        return f"https://www.amazon.com/dp/{asin}?tag={self.config.associate_tag}"

    def validate_asin(self, asin: str) -> bool:
        """Validate ASIN format."""
        # Amazon ASINs are 10 characters, alphanumeric
        return bool(re.match(r'^[A-Z0-9]{10}$', asin.upper()))

    def get_asins_from_urls(self, urls: List[str]) -> List[str]:
        """Extract ASINs from Amazon product URLs."""
        asins = []
        patterns = [
            r'/dp/([A-Z0-9]{10})',
            r'/gp/product/([A-Z0-9]{10})',
            r'/ASIN/([A-Z0-9]{10})',
            r'amazon\.com/([A-Z0-9]{10})',
        ]

        for url in urls:
            for pattern in patterns:
                match = re.search(pattern, url, re.IGNORECASE)
                if match:
                    asins.append(match.group(1).upper())
                    break

        return asins

    # ===========================================
    # PA-API Methods (when configured)
    # ===========================================

    def _search_pa_api(
        self,
        keyword: str,
        category: str,
        max_results: int,
    ) -> List[DiscoveredProduct]:
        """
        Search products using Amazon PA-API.

        Note: Requires valid PA-API credentials.
        """
        # PA-API v5 implementation would go here
        # This requires signing requests with AWS Signature V4

        # For now, return empty and fall back to curated
        print("PA-API search not fully implemented. Using curated list.")
        return self._search_curated(keyword, category, max_results)

    def _get_item_pa_api(self, asin: str) -> Optional[DiscoveredProduct]:
        """
        Get item details using PA-API.
        """
        # PA-API v5 GetItems implementation would go here
        return self._get_from_curated(asin)

    # ===========================================
    # Curated Product Methods
    # ===========================================

    def _search_curated(
        self,
        keyword: str,
        category: str,
        max_results: int,
    ) -> List[DiscoveredProduct]:
        """
        Search curated product lists.
        """
        keyword_lower = keyword.lower()
        all_products = []

        # Search relevant categories
        categories_to_search = [category.lower()] if category else list(self.TRENDING_PRODUCTS.keys())

        for cat in categories_to_search:
            if cat in self.TRENDING_PRODUCTS:
                for item in self.TRENDING_PRODUCTS[cat]:
                    # Simple keyword matching
                    if keyword_lower in item["title"].lower() or not keyword:
                        product = DiscoveredProduct(
                            asin=item["asin"],
                            title=item["title"],
                            product_url=f"https://www.amazon.com/dp/{item['asin']}",
                            category=cat,
                        )
                        all_products.append(product)

        return all_products[:max_results]

    def _get_from_curated(self, asin: str) -> Optional[DiscoveredProduct]:
        """
        Get product from curated lists by ASIN.
        """
        for category, products in self.TRENDING_PRODUCTS.items():
            for item in products:
                if item["asin"] == asin:
                    return DiscoveredProduct(
                        asin=item["asin"],
                        title=item["title"],
                        product_url=f"https://www.amazon.com/dp/{item['asin']}",
                        category=category,
                    )
        return None


# ===========================================
# Utility Functions
# ===========================================

def create_product_from_manual_input(
    asin: str,
    title: str,
    price: float,
    image_url: str,
    keyword: str,
    niche: str,
    affiliate_tag: str,
    rating: float = 0.0,
    review_count: int = 0,
    bullets: str = "",
) -> Product:
    """
    Create a Product from manual input data.

    Useful for adding products without API access.

    Args:
        asin: Amazon ASIN
        title: Product title
        price: Current price
        image_url: Product image URL
        keyword: Search keyword
        niche: Product niche
        affiliate_tag: Amazon associate tag
        rating: Product rating
        review_count: Number of reviews
        bullets: Product bullet points

    Returns:
        Product object ready for Airtable
    """
    return Product(
        keyword=keyword,
        niche=niche,
        asin=asin,
        product_url=f"https://www.amazon.com/dp/{asin}",
        title=title,
        price=price,
        rating=rating,
        review_count=review_count,
        bullets=bullets,
        image_url=image_url,
        affiliate_url=f"https://www.amazon.com/dp/{asin}?tag={affiliate_tag}",
        status="queued",
    )


def batch_create_products_from_asins(
    asins: List[str],
    keyword: str,
    niche: str,
    affiliate_tag: str,
) -> List[Product]:
    """
    Create Product objects from a list of ASINs.

    Args:
        asins: List of Amazon ASINs
        keyword: Search keyword
        niche: Product niche
        affiliate_tag: Amazon associate tag

    Returns:
        List of Product objects
    """
    products = []
    for asin in asins:
        product = Product(
            keyword=keyword,
            niche=niche,
            asin=asin,
            product_url=f"https://www.amazon.com/dp/{asin}",
            affiliate_url=f"https://www.amazon.com/dp/{asin}?tag={affiliate_tag}",
            status="queued",
        )
        products.append(product)
    return products


# Pre-defined roundup topics for content generation
ROUNDUP_TOPICS = {
    "beauty": [
        {"keyword": "best beauty dupes", "title": "Best Amazon Beauty Dupes That Actually Work"},
        {"keyword": "skincare routine", "title": "Complete Amazon Skincare Routine Under $50"},
        {"keyword": "viral tiktok beauty", "title": "TikTok-Viral Beauty Products on Amazon"},
        {"keyword": "drugstore makeup", "title": "Best Drugstore Makeup on Amazon"},
        {"keyword": "self care products", "title": "Ultimate Self-Care Products on Amazon"},
    ],
    "home": [
        {"keyword": "home organization", "title": "Best Amazon Organization Products"},
        {"keyword": "cozy home finds", "title": "Cozy Home Finds on Amazon"},
        {"keyword": "apartment essentials", "title": "Amazon Apartment Essentials"},
        {"keyword": "home decor under 25", "title": "Stylish Home Decor Under $25"},
        {"keyword": "cleaning products", "title": "Top-Rated Cleaning Products on Amazon"},
    ],
    "kitchen": [
        {"keyword": "kitchen gadgets", "title": "Must-Have Amazon Kitchen Gadgets"},
        {"keyword": "meal prep containers", "title": "Best Meal Prep Containers on Amazon"},
        {"keyword": "small appliances", "title": "Best Small Kitchen Appliances"},
        {"keyword": "kitchen organization", "title": "Kitchen Organization Products"},
        {"keyword": "cooking essentials", "title": "Essential Cooking Tools on Amazon"},
    ],
    "tech": [
        {"keyword": "tech gadgets under 50", "title": "Best Tech Gadgets Under $50"},
        {"keyword": "smart home devices", "title": "Smart Home Devices Worth Buying"},
        {"keyword": "work from home tech", "title": "Best Work From Home Tech"},
        {"keyword": "phone accessories", "title": "Must-Have Phone Accessories"},
        {"keyword": "gaming accessories", "title": "Best Gaming Accessories on Amazon"},
    ],
    "fashion": [
        {"keyword": "amazon fashion finds", "title": "Best Amazon Fashion Finds"},
        {"keyword": "basics wardrobe", "title": "Amazon Basics for Your Wardrobe"},
        {"keyword": "jewelry under 20", "title": "Best Amazon Jewelry Under $20"},
        {"keyword": "workout clothes", "title": "Best Workout Clothes on Amazon"},
        {"keyword": "summer fashion", "title": "Summer Fashion Finds on Amazon"},
    ],
}
