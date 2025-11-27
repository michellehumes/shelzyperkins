"""
Distribution Content Generator for the ShelzyPerkins Affiliate System.

Generates social media and email content for promoting blog posts:
- Instagram captions
- TikTok captions
- Pinterest pins (3 variations)
- Email newsletter content
"""

from typing import Any, Dict, List, Optional
from dataclasses import dataclass
from datetime import datetime
import re

from .config import Config, NicheConfig
from .airtable_client import Product, Distribution


@dataclass
class DistributionContent:
    """
    Generated distribution content for all platforms.
    """
    post_slug: str
    post_title: str
    post_url: str

    # Instagram
    instagram_caption: str

    # TikTok
    tiktok_caption: str

    # Pinterest (3 variations)
    pinterest_title_1: str
    pinterest_desc_1: str
    pinterest_title_2: str
    pinterest_desc_2: str
    pinterest_title_3: str
    pinterest_desc_3: str

    # Email
    email_subject: str
    email_blurb: str

    def to_distribution(self) -> Distribution:
        """Convert to Distribution dataclass for Airtable."""
        return Distribution(
            post_slug=self.post_slug,
            instagram_caption=self.instagram_caption,
            tiktok_caption=self.tiktok_caption,
            pinterest_title_1=self.pinterest_title_1,
            pinterest_desc_1=self.pinterest_desc_1,
            pinterest_title_2=self.pinterest_title_2,
            pinterest_desc_2=self.pinterest_desc_2,
            pinterest_title_3=self.pinterest_title_3,
            pinterest_desc_3=self.pinterest_desc_3,
            email_subject=self.email_subject,
            email_blurb=self.email_blurb,
        )


class DistributionGenerator:
    """
    Generate social media and email content for blog post promotion.

    Usage:
        config = Config()
        generator = DistributionGenerator(config.niches)

        content = generator.generate_all(
            post_slug="best-amazon-beauty-dupes",
            post_title="10 Best Amazon Beauty Dupes",
            post_url="https://shelzyperkins.com/best-amazon-beauty-dupes/",
            niche="beauty",
            products=product_list,
        )

        # Access individual pieces
        print(content.instagram_caption)
        print(content.email_subject)
    """

    def __init__(self, niches: Dict[str, NicheConfig]):
        """
        Initialize distribution generator.

        Args:
            niches: Dictionary of niche configurations
        """
        self.niches = niches

    def generate_all(
        self,
        post_slug: str,
        post_title: str,
        post_url: str,
        niche: str,
        products: List[Product],
        custom_hook: str = "",
    ) -> DistributionContent:
        """
        Generate all distribution content for a post.

        Args:
            post_slug: Post URL slug
            post_title: Post title
            post_url: Full post URL
            niche: Content niche
            products: List of featured products
            custom_hook: Custom hook/tagline to use

        Returns:
            DistributionContent with all platform content
        """
        niche_config = self.niches.get(niche.lower())
        if not niche_config:
            niche_config = NicheConfig(name=niche.title(), keywords=[], hashtags=[], emoji="")

        return DistributionContent(
            post_slug=post_slug,
            post_title=post_title,
            post_url=post_url,
            instagram_caption=self.generate_instagram(post_title, post_url, niche_config, products, custom_hook),
            tiktok_caption=self.generate_tiktok(post_title, niche_config, products, custom_hook),
            pinterest_title_1=self.generate_pinterest_title(post_title, niche_config, 1),
            pinterest_desc_1=self.generate_pinterest_desc(post_title, post_url, niche_config, products, 1),
            pinterest_title_2=self.generate_pinterest_title(post_title, niche_config, 2),
            pinterest_desc_2=self.generate_pinterest_desc(post_title, post_url, niche_config, products, 2),
            pinterest_title_3=self.generate_pinterest_title(post_title, niche_config, 3),
            pinterest_desc_3=self.generate_pinterest_desc(post_title, post_url, niche_config, products, 3),
            email_subject=self.generate_email_subject(post_title, niche_config),
            email_blurb=self.generate_email_blurb(post_title, post_url, niche_config, products),
        )

    # ===========================================
    # Instagram Content
    # ===========================================

    def generate_instagram(
        self,
        title: str,
        url: str,
        niche_config: NicheConfig,
        products: List[Product],
        custom_hook: str = "",
    ) -> str:
        """
        Generate Instagram caption.

        Max length: ~2200 chars (but 125 chars shown before "more")
        """
        emoji = niche_config.emoji or "✨"
        hashtags = niche_config.hashtags[:15] or ["#amazonfinds", "#deals"]

        # Opening hook (shows before "more")
        hooks = [
            f"{emoji} SAVE THIS POST! {emoji}",
            f"{emoji} You NEED to see these!",
            f"{emoji} Amazon finds you'll love!",
            f"{emoji} Stop scrolling - you need these!",
        ]
        hook = custom_hook or hooks[hash(title) % len(hooks)]

        # Product highlights
        product_lines = []
        for i, product in enumerate(products[:5], 1):
            short_title = product.title[:40] + "..." if len(product.title) > 40 else product.title
            product_lines.append(f"{i}. {short_title}")

        products_text = "\n".join(product_lines)

        # Build caption
        caption = f"""{hook}

{title}

Featured in this roundup:
{products_text}

All links are in my bio! Tap the link to shop + see my full reviews {emoji}

Save this post for later! Double tap if you love finding deals {emoji}

---
{" ".join(hashtags)}
#amazonassociate #linkinbio #savemoney #budgetfriendly"""

        return caption[:2200]

    # ===========================================
    # TikTok Content
    # ===========================================

    def generate_tiktok(
        self,
        title: str,
        niche_config: NicheConfig,
        products: List[Product],
        custom_hook: str = "",
    ) -> str:
        """
        Generate TikTok caption.

        Max length: ~2200 chars
        """
        emoji = niche_config.emoji or "✨"
        hashtags = niche_config.hashtags[:10] or ["#amazonfinds"]

        # TikTok hooks (attention-grabbing)
        hooks = [
            f"{emoji} POV: You found the BEST Amazon finds",
            f"Things I found on Amazon this week {emoji}",
            f"Amazon finds that are actually worth it {emoji}",
            f"You're gonna want all of these {emoji}",
            f"My current Amazon favorites {emoji}",
        ]
        hook = custom_hook or hooks[hash(title) % len(hooks)]

        # Shorter format for TikTok
        caption = f"""{hook}

{title}

Full reviews + links in my bio!

{" ".join(hashtags)} #amazonmusthaves #tiktokmademebuyit #amazonhaul"""

        return caption[:2200]

    # ===========================================
    # Pinterest Content
    # ===========================================

    def generate_pinterest_title(
        self,
        title: str,
        niche_config: NicheConfig,
        variation: int,
    ) -> str:
        """
        Generate Pinterest pin title.

        Max length: 100 chars
        """
        year = datetime.now().year
        emoji = niche_config.emoji or ""

        # Different angles for each variation
        variations = {
            1: f"{title} | {year} Guide",
            2: f"Best {niche_config.name} Finds on Amazon",
            3: f"{emoji} Must-Have Amazon {niche_config.name} Products",
        }

        pin_title = variations.get(variation, title)
        return pin_title[:100]

    def generate_pinterest_desc(
        self,
        title: str,
        url: str,
        niche_config: NicheConfig,
        products: List[Product],
        variation: int,
    ) -> str:
        """
        Generate Pinterest pin description.

        Max length: 500 chars
        """
        keywords = niche_config.keywords[:5] or ["amazon finds", "deals"]
        product_count = len(products)

        # Different descriptions for each variation
        descriptions = {
            1: f"""Discover the best {niche_config.name.lower()} products on Amazon! This roundup features {product_count} handpicked items perfect for your next shopping spree.

Click through to see prices, reviews, and my honest recommendations for each product.

Keywords: {", ".join(keywords)}""",

            2: f"""Looking for amazing {niche_config.name.lower()} deals? I've curated {product_count} must-have Amazon products that deliver incredible value.

From budget-friendly options to splurge-worthy finds, there's something for everyone. Read the full roundup for all the details!

Tags: {", ".join(keywords)}""",

            3: f"""My favorite {niche_config.name.lower()} products from Amazon - all in one place! These {product_count} items have been tested and approved.

Whether you're treating yourself or shopping for gifts, these picks won't disappoint. Click to shop the full collection!

Related: {", ".join(keywords)}""",
        }

        return descriptions.get(variation, descriptions[1])[:500]

    # ===========================================
    # Email Content
    # ===========================================

    def generate_email_subject(
        self,
        title: str,
        niche_config: NicheConfig,
    ) -> str:
        """
        Generate email subject line.

        Max length: ~50 chars for best open rates
        """
        emoji = niche_config.emoji or ""

        subjects = [
            f"{emoji} New: {title}",
            f"You'll love these {niche_config.name.lower()} finds!",
            f"{emoji} Just dropped: My latest Amazon picks",
            f"The {niche_config.name.lower()} products I'm obsessed with",
        ]

        return subjects[hash(title) % len(subjects)][:60]

    def generate_email_blurb(
        self,
        title: str,
        url: str,
        niche_config: NicheConfig,
        products: List[Product],
    ) -> str:
        """
        Generate email newsletter blurb.

        This is designed to be dropped into an existing email template.
        """
        emoji = niche_config.emoji or "✨"
        product_count = len(products)

        # Highlight top 3 products
        highlights = []
        for product in products[:3]:
            short_title = product.title[:50]
            price_str = f"${product.price:.2f}" if product.price else "Check price"
            highlights.append(f"- {short_title} ({price_str})")

        highlights_text = "\n".join(highlights)

        blurb = f"""
{emoji} NEW ROUNDUP: {title}

Hey there!

I just published a new roundup featuring {product_count} amazing {niche_config.name.lower()} finds from Amazon. Here are some highlights:

{highlights_text}

Each product has been carefully selected for quality and value. I've included prices, ratings, and my honest thoughts on every item.

[READ THE FULL ROUNDUP]({url})

Happy shopping!

P.S. Did you find something you love? Hit reply and let me know - I love hearing from you!
"""

        return blurb.strip()

    # ===========================================
    # Bulk Generation
    # ===========================================

    def generate_batch(
        self,
        posts: List[Dict[str, Any]],
    ) -> List[DistributionContent]:
        """
        Generate distribution content for multiple posts.

        Args:
            posts: List of dicts with keys: slug, title, url, niche, products

        Returns:
            List of DistributionContent objects
        """
        results = []
        for post in posts:
            content = self.generate_all(
                post_slug=post["slug"],
                post_title=post["title"],
                post_url=post["url"],
                niche=post["niche"],
                products=post.get("products", []),
            )
            results.append(content)
        return results


# ===========================================
# Utility Functions
# ===========================================

def format_hashtags(hashtags: List[str], max_count: int = 30) -> str:
    """Format hashtags for social media posts."""
    # Ensure hashtags start with #
    formatted = []
    for tag in hashtags[:max_count]:
        if not tag.startswith("#"):
            tag = f"#{tag}"
        formatted.append(tag.lower().replace(" ", ""))
    return " ".join(formatted)


def truncate_for_platform(text: str, platform: str) -> str:
    """Truncate text to platform-specific limits."""
    limits = {
        "instagram": 2200,
        "tiktok": 2200,
        "twitter": 280,
        "pinterest_title": 100,
        "pinterest_desc": 500,
        "email_subject": 60,
    }
    limit = limits.get(platform, 500)

    if len(text) <= limit:
        return text

    return text[:limit - 3] + "..."


def generate_link_in_bio_text(url: str, platform: str = "instagram") -> str:
    """Generate call-to-action for link in bio."""
    ctas = {
        "instagram": "Link in bio to shop!",
        "tiktok": "Link in bio for all products!",
        "general": f"Shop now: {url}",
    }
    return ctas.get(platform, ctas["general"])


# ===========================================
# Content Templates
# ===========================================

INSTAGRAM_TEMPLATES = {
    "list_post": """
{emoji} {hook}

{title}

Here's what's inside:
{product_list}

Save this post & tap the link in bio to shop! {emoji}

{hashtags}
""",

    "single_product": """
{emoji} Must-have alert!

{product_title}

{product_desc}

Price: {price}

Link in bio to shop! {emoji}

{hashtags}
""",

    "deal_alert": """
{emoji} DEAL ALERT {emoji}

{title}

{savings_info}

This deal won't last! Link in bio to grab it.

{hashtags}
""",
}

TIKTOK_TEMPLATES = {
    "roundup": """
Amazon finds you didn't know you needed {emoji}

{title}

Full list + links in bio!

{hashtags}
""",

    "single": """
POV: You find the perfect {niche} product {emoji}

{product_title}

Link in bio!

{hashtags}
""",
}

EMAIL_TEMPLATES = {
    "newsletter_section": """
## {emoji} {title}

I just published a new roundup featuring the best {niche} products on Amazon!

**Quick highlights:**
{product_highlights}

[Read the full roundup and shop all products]({url})
""",

    "dedicated_email": """
Subject: {subject}

---

Hi there!

I'm excited to share my latest roundup with you: **{title}**

After hours of research and testing, I've curated {product_count} amazing products that I know you'll love.

**What's inside:**
{product_list}

Each product includes:
- Current price
- My honest review
- Quick buy links

[CHECK OUT THE FULL ROUNDUP]({url})

Happy shopping!
{author_name}

---

P.S. Know someone who would love these finds? Forward this email to share the deals!
""",
}
