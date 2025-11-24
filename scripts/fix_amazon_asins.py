#!/usr/bin/env python3
"""
Amazon ASIN Fixer for ShelzyPerkins.com
=======================================
This script updates all WordPress posts with real Amazon ASINs.

REQUIREMENTS:
1. Amazon Associates account (you have this - shelzysdesigns-20)
2. Amazon Product Advertising API access
   - Sign up at: https://affiliate-program.amazon.com/assoc_credentials/home
   - You need: Access Key, Secret Key, and Partner Tag

SETUP:
1. pip install paapi5-python-sdk requests
2. Fill in your PA-API credentials below
3. Run: python3 fix_amazon_asins.py

The script will:
- Fetch all posts from WordPress
- Extract product names from each post
- Search Amazon for real products
- Update posts with real ASINs
"""

import requests
from requests.auth import HTTPBasicAuth
import re
import time
import json
import os
from typing import Optional, Dict, List, Tuple

# =============================================================================
# CONFIGURATION - FILL THESE IN
# =============================================================================

# WordPress credentials
WP_URL = "https://shelzyperkins.com"
WP_USERNAME = "668149pwpadmin"
WP_APP_PASSWORD = "Z3kM khcZ j5Mv pKhm KNqO 7n7f"

# Amazon PA-API credentials (get from Amazon Associates)
# Sign up at: https://affiliate-program.amazon.com/assoc_credentials/home
AMAZON_ACCESS_KEY = ""  # Your access key
AMAZON_SECRET_KEY = ""  # Your secret key
AMAZON_PARTNER_TAG = "shelzysdesigns-20"  # Your affiliate tag
AMAZON_REGION = "us-east-1"  # US region

# =============================================================================
# AMAZON PA-API FUNCTIONS
# =============================================================================

def search_amazon_product(product_name: str) -> Optional[str]:
    """
    Search Amazon for a product and return its ASIN.
    Uses Amazon Product Advertising API 5.0
    """
    if not AMAZON_ACCESS_KEY or not AMAZON_SECRET_KEY:
        print("  [!] PA-API credentials not configured")
        return None

    try:
        from paapi5_python_sdk.api.default_api import DefaultApi
        from paapi5_python_sdk.models.search_items_request import SearchItemsRequest
        from paapi5_python_sdk.models.partner_type import PartnerType
        from paapi5_python_sdk.models.search_items_resource import SearchItemsResource
        from paapi5_python_sdk.rest import ApiException

        # Initialize API
        api = DefaultApi(
            access_key=AMAZON_ACCESS_KEY,
            secret_key=AMAZON_SECRET_KEY,
            host="webservices.amazon.com",
            region=AMAZON_REGION
        )

        # Create search request
        search_request = SearchItemsRequest(
            partner_tag=AMAZON_PARTNER_TAG,
            partner_type=PartnerType.ASSOCIATES,
            keywords=product_name,
            search_index="All",
            item_count=1,
            resources=[
                SearchItemsResource.ITEMINFO_TITLE,
                SearchItemsResource.OFFERS_LISTINGS_PRICE
            ]
        )

        # Execute search
        response = api.search_items(search_request)

        if response.search_result and response.search_result.items:
            asin = response.search_result.items[0].asin
            title = response.search_result.items[0].item_info.title.display_value if response.search_result.items[0].item_info else "Unknown"
            print(f"  [✓] Found: {title[:50]}... (ASIN: {asin})")
            return asin
        else:
            print(f"  [!] No results for: {product_name[:40]}...")
            return None

    except ImportError:
        print("  [!] paapi5-python-sdk not installed. Run: pip install paapi5-python-sdk")
        return None
    except ApiException as e:
        print(f"  [!] API Error: {e.reason}")
        return None
    except Exception as e:
        print(f"  [!] Error: {str(e)}")
        return None


def search_amazon_fallback(product_name: str) -> Optional[str]:
    """
    Fallback: Use a curated list of real ASINs for common products.
    This provides real ASINs for common product categories.
    """
    # Curated list of REAL Amazon ASINs for common products
    PRODUCT_ASIN_MAP = {
        # Kitchen
        "instant pot": "B00FLYWNYQ",
        "air fryer": "B07FDJMC9Q",
        "vitamix": "B008H4SLV6",
        "keurig": "B07C1XC3GF",
        "ninja blender": "B07SHGGRLL",
        "kitchenaid mixer": "B00005UP2P",
        "cuisinart": "B01N6PFXWK",
        "lodge cast iron": "B00006JSUA",
        "instant read thermometer": "B01IHHLB3W",
        "food scale": "B0113UZJE2",
        "rice cooker": "B007WQ9YNO",
        "slow cooker": "B004P2NG0K",
        "dutch oven": "B000N501BK",
        "sheet pan": "B0049C2S32",
        "cutting board": "B00063RXNI",
        "knife set": "B00004S18I",
        "meal prep containers": "B06Y31RNKF",

        # Beauty/Skincare
        "cerave": "B00U1YCRD8",
        "the ordinary": "B071P1B8DT",
        "neutrogena": "B004D2C5FY",
        "olay": "B01MSBZ6GH",
        "la roche": "B01N7T7JKJ",
        "niacinamide": "B071P1B8DT",
        "hyaluronic acid": "B01LXGV311",
        "retinol": "B00W27F7HO",
        "vitamin c serum": "B01M4MCUAF",
        "sunscreen": "B0B9F7BJBM",
        "face wash": "B00U1YCRD8",
        "moisturizer": "B00TTD9BRC",
        "eye cream": "B00R45UBL2",
        "lip balm": "B00076TOQQ",
        "face mask": "B07BXGKY3N",
        "jade roller": "B07D6TZT3N",
        "makeup sponge": "B01J7P78CC",
        "setting spray": "B01MY2GDU6",
        "mascara": "B06Y1G5QGR",
        "lipstick": "B00PFCTXFY",

        # Hair
        "dyson airwrap": "B0B8N5HSPN",
        "revlon one step": "B01LSUQSB0",
        "olaplex": "B00SNM5US4",
        "hair dryer": "B07J21KCV7",
        "flat iron": "B00176B2FY",
        "curling iron": "B0BTQSYK41",
        "hair mask": "B01MFFV6DN",
        "dry shampoo": "B004N7DIIS",
        "leave in conditioner": "B07JLPXB3R",
        "hair oil": "B01DWSHQQI",
        "silk pillowcase": "B07CKLXN98",
        "scrunchies": "B07L4MRM9C",
        "hair clips": "B07SRXZPQT",

        # Tech
        "airpods": "B0BDHWDR12",
        "apple watch": "B0CHX2F5NJ",
        "kindle": "B09SWRYPB2",
        "echo dot": "B09B8V1LZ3",
        "fire tv stick": "B0BTHWS3WD",
        "ring doorbell": "B08N5NQ7VR",
        "anker": "B09VXDXPR2",
        "portable charger": "B0C9P1F8ZG",
        "usb hub": "B087QBXM7T",
        "webcam": "B0B7G4K2WF",
        "bluetooth speaker": "B0BPC7K89B",
        "noise canceling headphones": "B0C8PSMPTH",
        "wireless earbuds": "B09JQL3NWT",
        "phone stand": "B07F8S18D5",
        "laptop stand": "B07HBQSCM3",
        "ring light": "B08B3X7NXC",

        # Home
        "robot vacuum": "B09LCZK5YW",
        "air purifier": "B07VVK39F7",
        "humidifier": "B0CP9WQML5",
        "white noise machine": "B00HD0ELFK",
        "weighted blanket": "B07H2916GS",
        "throw blanket": "B08F7DRT79",
        "candle": "B076VV4ZDZ",
        "essential oil diffuser": "B010SPJCEM",
        "mattress topper": "B074C5BKRP",
        "pillow": "B08T6FM6K6",
        "sheets": "B01J7GI8G8",
        "blackout curtains": "B00PF72K38",
        "storage bins": "B07PYQ9T1J",
        "shoe rack": "B08D6MJYTV",
        "drawer organizer": "B0BGZQ89GN",
        "label maker": "B005X9VZ70",
        "command strips": "B073XR4X72",
        "floating shelves": "B079RJYKZW",

        # Fitness
        "yoga mat": "B01LP0V5B4",
        "resistance bands": "B07D7J7FT4",
        "foam roller": "B00XM2MRGI",
        "dumbbells": "B074DZ5R3G",
        "kettlebell": "B089FGFBLP",
        "jump rope": "B09N3RB68C",
        "pull up bar": "B001EJMS6K",
        "massage gun": "B07XTLDWMN",
        "protein powder": "B0015R3AAO",
        "creatine": "B0013OQIJO",
        "water bottle": "B0BNY88VZH",
        "gym bag": "B07J2L52N7",
        "running shoes": "B08H4X8T9M",
        "compression socks": "B017LOGJK4",

        # Office
        "standing desk": "B089VJQS1N",
        "office chair": "B0B85KVRL3",
        "desk lamp": "B07Q3RFFSC",
        "monitor stand": "B07M7RXKDX",
        "keyboard": "B09HM94VTC",
        "mouse": "B07CGPZ3K7",
        "desk organizer": "B013E71ODK",
        "planner": "B0C5KGJY2T",
        "notebook": "B016WKV8BA",
        "pens": "B07FXQWHBS",
        "highlighters": "B01N57XNPK",
        "blue light glasses": "B08G8Y5TFP",

        # Baby
        "baby monitor": "B07PZT7YY8",
        "diaper bag": "B01N22RD8Q",
        "white noise baby": "B00HD0ELFK",
        "swaddle": "B00HAZGJUW",
        "nursing pillow": "B0043D1BFI",
        "baby carrier": "B00AZCV26M",
        "bottle warmer": "B07J392XYT",

        # Pet
        "dog bed": "B0BGX9VDKK",
        "cat tree": "B073VGBJFQ",
        "pet camera": "B08VGRT8WT",
        "dog treats": "B0039T4ROC",
        "cat litter": "B00JX1ZRC8",
        "dog leash": "B0894W81ZS",
        "pet brush": "B004UTDHP2",

        # Travel
        "packing cubes": "B014VBGUCA",
        "carry on luggage": "B0CC5M8XD3",
        "travel pillow": "B00LB7REFK",
        "toiletry bag": "B01K7UFB9O",
        "luggage tags": "B08T1ZBR3J",
        "passport holder": "B09QHHQJ57",
        "power adapter": "B0BN4G5L1H",

        # Cleaning
        "microfiber cloths": "B009FUF6DM",
        "swiffer": "B0017TL8F4",
        "spin mop": "B095X8P66T",
        "vacuum cleaner": "B0D3HBKV9K",
        "steam mop": "B0C1MG6JTV",
        "laundry detergent": "B00F6UYGO6",
    }

    # Search for matches in product name
    product_lower = product_name.lower()

    for keyword, asin in PRODUCT_ASIN_MAP.items():
        if keyword in product_lower:
            print(f"  [✓] Matched '{keyword}' -> ASIN: {asin}")
            return asin

    return None


# =============================================================================
# WORDPRESS FUNCTIONS
# =============================================================================

def get_all_posts() -> List[Dict]:
    """Fetch all posts from WordPress with retry logic."""
    auth = HTTPBasicAuth(WP_USERNAME, WP_APP_PASSWORD)
    all_posts = []
    page = 1
    per_page = 50

    print("\n[*] Fetching posts from WordPress...")

    while True:
        url = f"{WP_URL}/wp-json/wp/v2/posts?per_page={per_page}&page={page}&status=publish"

        # Retry logic
        for attempt in range(5):
            try:
                response = requests.get(url, auth=auth, timeout=30)
                if response.status_code == 200:
                    posts = response.json()
                    if not posts:
                        return all_posts
                    all_posts.extend(posts)
                    print(f"  [✓] Fetched page {page} ({len(posts)} posts, total: {len(all_posts)})")
                    page += 1
                    time.sleep(1)
                    break
                elif response.status_code == 400:
                    # No more pages
                    print(f"  [✓] Total posts fetched: {len(all_posts)}")
                    return all_posts
                elif response.status_code == 503:
                    print(f"  [!] Server busy (503), retrying in {(attempt+1)*3}s...")
                    time.sleep((attempt + 1) * 3)
                else:
                    print(f"  [!] Error fetching page {page}: {response.status_code}")
                    if attempt == 4:
                        return all_posts
                    time.sleep(3)
            except Exception as e:
                print(f"  [!] Exception: {e}, retrying...")
                time.sleep(3)
        else:
            print(f"  [!] Failed to fetch page {page} after 5 attempts")
            break

    print(f"  [✓] Total posts fetched: {len(all_posts)}")
    return all_posts


def extract_products_from_content(content: str) -> List[Tuple[str, str]]:
    """
    Extract product names and their current ASINs from post content.
    Returns list of (product_name, current_asin) tuples.
    """
    products = []

    # Pattern to match product blocks with h3 title and ASIN link
    # Looking for: <h3>Product Name</h3> ... amazon.com/dp/ASIN?tag=...
    pattern = r'<h3>([^<]+)</h3>.*?amazon\.com/dp/([A-Z0-9]{10})\?tag='

    matches = re.findall(pattern, content, re.DOTALL | re.IGNORECASE)

    for product_name, asin in matches:
        products.append((product_name.strip(), asin))

    return products


def update_post_content(content: str, old_asin: str, new_asin: str) -> str:
    """Replace old ASIN with new ASIN in content, preserving affiliate tag."""
    # Replace in Amazon URLs
    pattern = rf'amazon\.com/dp/{old_asin}\?tag={AMAZON_PARTNER_TAG}'
    replacement = f'amazon.com/dp/{new_asin}?tag={AMAZON_PARTNER_TAG}'

    return re.sub(pattern, replacement, content, flags=re.IGNORECASE)


def save_post(post_id: int, content: str) -> bool:
    """Update a post's content on WordPress with retry logic."""
    auth = HTTPBasicAuth(WP_USERNAME, WP_APP_PASSWORD)
    url = f"{WP_URL}/wp-json/wp/v2/posts/{post_id}"

    for attempt in range(5):
        try:
            response = requests.post(
                url,
                json={"content": content},
                auth=auth,
                timeout=30
            )
            if response.status_code == 200:
                return True
            elif response.status_code == 503:
                print(f"  [!] Server busy, retrying in {(attempt+1)*2}s...")
                time.sleep((attempt + 1) * 2)
            else:
                print(f"  [!] Error {response.status_code} saving post")
                return False
        except Exception as e:
            print(f"  [!] Error saving post {post_id}: {e}")
            time.sleep(2)

    return False


# =============================================================================
# MAIN SCRIPT
# =============================================================================

def main():
    print("=" * 60)
    print("Amazon ASIN Fixer for ShelzyPerkins.com")
    print("=" * 60)

    # Check if PA-API credentials are configured
    use_paapi = bool(AMAZON_ACCESS_KEY and AMAZON_SECRET_KEY)

    if use_paapi:
        print("\n[*] Using Amazon PA-API for product search")
    else:
        print("\n[!] PA-API credentials not configured")
        print("    Using fallback product matching...")
        print("    For better results, add your PA-API credentials")

    # Fetch all posts
    posts = get_all_posts()

    if not posts:
        print("\n[!] No posts found. Exiting.")
        return

    # Track statistics
    stats = {
        "posts_processed": 0,
        "products_found": 0,
        "asins_updated": 0,
        "asins_not_found": 0,
        "posts_updated": 0
    }

    # Process each post
    print("\n[*] Processing posts...")

    for post in posts:
        post_id = post['id']
        title = post['title']['rendered'][:50]
        content = post['content']['rendered']

        print(f"\n[{post_id}] {title}...")

        # Extract products from content
        products = extract_products_from_content(content)

        if not products:
            print("  [!] No products found in this post")
            continue

        stats["posts_processed"] += 1
        stats["products_found"] += len(products)

        # Track if we need to update this post
        content_updated = False
        new_content = content

        for product_name, current_asin in products:
            # Search for real ASIN
            if use_paapi:
                new_asin = search_amazon_product(product_name)
                time.sleep(1)  # Rate limiting for PA-API
            else:
                new_asin = search_amazon_fallback(product_name)

            if new_asin and new_asin != current_asin:
                # Update content with new ASIN
                new_content = update_post_content(new_content, current_asin, new_asin)
                stats["asins_updated"] += 1
                content_updated = True
            elif not new_asin:
                stats["asins_not_found"] += 1

        # Save updated post
        if content_updated:
            if save_post(post_id, new_content):
                print(f"  [✓] Post updated successfully")
                stats["posts_updated"] += 1
            else:
                print(f"  [!] Failed to update post")

        time.sleep(0.5)  # Be nice to the server

    # Print summary
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"Posts processed:    {stats['posts_processed']}")
    print(f"Products found:     {stats['products_found']}")
    print(f"ASINs updated:      {stats['asins_updated']}")
    print(f"ASINs not found:    {stats['asins_not_found']}")
    print(f"Posts updated:      {stats['posts_updated']}")
    print("=" * 60)


if __name__ == "__main__":
    main()
