"""
SEO-optimized Content Generator for the ShelzyPerkins Affiliate System.

Generates affiliate roundup posts with:
- SEO-optimized titles and meta descriptions
- Schema.org structured data (JSON-LD)
- Compelling CTAs
- Product comparison tables
- FAQ sections
"""

from typing import Any, Dict, List, Optional
from dataclasses import dataclass
from datetime import datetime
import json
import re
import html

from .config import Config, ContentConfig, NicheConfig
from .airtable_client import Product


@dataclass
class GeneratedPost:
    """
    Generated blog post content.
    """
    title: str
    slug: str
    meta_title: str
    meta_description: str
    html_content: str
    schema_json: str
    excerpt: str
    tags: List[str]
    category: str
    focus_keyword: str


class ContentGenerator:
    """
    SEO-optimized content generator for affiliate roundup posts.

    Usage:
        config = Config()
        generator = ContentGenerator(config.content, config.niches)

        # Generate a roundup post
        products = [product1, product2, ...]  # List of Product objects
        post = generator.generate_roundup_post(
            products=products,
            title="10 Best Amazon Beauty Dupes",
            niche="beauty",
            focus_keyword="amazon beauty dupes"
        )

        print(post.html_content)  # Full HTML
        print(post.schema_json)   # JSON-LD schema
    """

    def __init__(self, content_config: ContentConfig, niches: Dict[str, NicheConfig]):
        """
        Initialize content generator.

        Args:
            content_config: Content configuration
            niches: Dictionary of niche configurations
        """
        self.config = content_config
        self.niches = niches

    def generate_roundup_post(
        self,
        products: List[Product],
        title: str,
        niche: str,
        focus_keyword: str = "",
        intro_text: str = "",
        include_faq: bool = True,
        include_comparison_table: bool = True,
    ) -> GeneratedPost:
        """
        Generate a complete SEO-optimized roundup post.

        Args:
            products: List of Product objects to feature
            title: Post title
            niche: Product niche (beauty, home, kitchen, tech, fashion, fitness)
            focus_keyword: SEO focus keyword
            intro_text: Custom introduction text (auto-generated if not provided)
            include_faq: Whether to include FAQ section
            include_comparison_table: Whether to include comparison table

        Returns:
            GeneratedPost with full HTML content and metadata
        """
        niche_config = self.niches.get(niche.lower())
        if not niche_config:
            niche_config = NicheConfig(name=niche.title(), keywords=[], hashtags=[], emoji="")

        # Generate slug
        slug = self._generate_slug(title)

        # Generate meta content
        meta_title = self._generate_meta_title(title, focus_keyword)
        meta_description = self._generate_meta_description(title, products, focus_keyword)

        # Generate excerpt
        excerpt = self._generate_excerpt(title, products)

        # Generate tags
        tags = self._generate_tags(niche_config, focus_keyword)

        # Build HTML content
        html_parts = []

        # Affiliate disclosure
        html_parts.append(self._generate_disclosure())

        # Introduction
        html_parts.append(self._generate_intro(title, products, intro_text, niche_config))

        # Table of contents
        html_parts.append(self._generate_toc(products))

        # Comparison table (if enabled)
        if include_comparison_table and len(products) >= 3:
            html_parts.append(self._generate_comparison_table(products[:5]))

        # Product sections
        for i, product in enumerate(products, 1):
            html_parts.append(self._generate_product_section(product, i, len(products)))

        # FAQ section (if enabled)
        if include_faq:
            html_parts.append(self._generate_faq(title, niche_config, focus_keyword))

        # Conclusion
        html_parts.append(self._generate_conclusion(title, products))

        # Email signup CTA
        html_parts.append(self._generate_email_cta(niche_config))

        # Combine HTML
        html_content = "\n\n".join(html_parts)

        # Generate schema
        schema_json = self._generate_schema(title, slug, products, meta_description)

        return GeneratedPost(
            title=title,
            slug=slug,
            meta_title=meta_title,
            meta_description=meta_description,
            html_content=html_content,
            schema_json=schema_json,
            excerpt=excerpt,
            tags=tags,
            category=niche_config.name,
            focus_keyword=focus_keyword or self._extract_focus_keyword(title),
        )

    # ===========================================
    # Meta Content Generation
    # ===========================================

    def _generate_slug(self, title: str) -> str:
        """Generate URL-friendly slug from title."""
        slug = title.lower()
        slug = re.sub(r'[^\w\s-]', '', slug)
        slug = re.sub(r'[-\s]+', '-', slug)
        slug = slug.strip('-')
        return slug[:60]

    def _generate_meta_title(self, title: str, focus_keyword: str) -> str:
        """Generate SEO meta title (max 60 chars)."""
        year = datetime.now().year
        base = f"{title} ({year})"

        if len(base) <= 60:
            return base

        return title[:57] + "..."

    def _generate_meta_description(
        self,
        title: str,
        products: List[Product],
        focus_keyword: str,
    ) -> str:
        """Generate SEO meta description (max 160 chars)."""
        product_count = len(products)
        year = datetime.now().year

        templates = [
            f"Discover the {product_count} best {focus_keyword} for {year}. Handpicked recommendations with honest reviews to help you find the perfect products!",
            f"Looking for the best {focus_keyword}? Check out our top {product_count} picks with prices, ratings, and honest reviews.",
            f"Our experts tested dozens of products to find the best {focus_keyword}. See our top {product_count} recommendations for {year}!",
        ]

        for template in templates:
            if len(template) <= 160:
                return template

        return templates[0][:157] + "..."

    def _generate_excerpt(self, title: str, products: List[Product]) -> str:
        """Generate post excerpt."""
        return f"Discover our handpicked selection of {len(products)} amazing products. Each item has been carefully selected for quality and value."

    def _generate_tags(self, niche_config: NicheConfig, focus_keyword: str) -> List[str]:
        """Generate post tags."""
        tags = ["amazon finds", "deals", niche_config.name.lower()]
        tags.extend(niche_config.keywords[:3])

        if focus_keyword:
            tags.append(focus_keyword)

        return list(set(tags))[:10]

    def _extract_focus_keyword(self, title: str) -> str:
        """Extract focus keyword from title."""
        title_lower = title.lower()
        # Remove common words
        stop_words = ["the", "best", "top", "amazon", "for", "and", "of", "in", "on"]
        words = [w for w in title_lower.split() if w not in stop_words and len(w) > 2]
        return " ".join(words[:3])

    # ===========================================
    # HTML Content Generation
    # ===========================================

    def _generate_disclosure(self) -> str:
        """Generate affiliate disclosure HTML."""
        return f'''<div class="affiliate-disclosure" style="background:#f8f9fa;padding:15px;border-radius:8px;margin-bottom:20px;font-size:14px;color:#666;">
<strong>Disclosure:</strong> {html.escape(self.config.affiliate_disclosure)}
</div>'''

    def _generate_intro(
        self,
        title: str,
        products: List[Product],
        custom_intro: str,
        niche_config: NicheConfig,
    ) -> str:
        """Generate introduction section."""
        if custom_intro:
            intro_text = custom_intro
        else:
            intro_text = self._auto_generate_intro(title, products, niche_config)

        return f'''<div class="post-intro">
<p>{html.escape(intro_text)}</p>

<p><strong>In this roundup:</strong></p>
<ul>
<li>{len(products)} handpicked products</li>
<li>Prices, ratings, and honest thoughts</li>
<li>Quick comparison table</li>
<li>Tips for choosing the right product</li>
</ul>
</div>'''

    def _auto_generate_intro(
        self,
        title: str,
        products: List[Product],
        niche_config: NicheConfig,
    ) -> str:
        """Auto-generate introduction based on title and niche."""
        templates = {
            "beauty": f"Finding the perfect beauty products shouldn't break the bank. After testing dozens of options, I've curated this list of {len(products)} must-have items that deliver real results without the luxury price tag.",
            "home": f"Transform your space without overspending! These {len(products)} home essentials have been handpicked for their quality, style, and value. Each item has earned its spot through rigorous testing.",
            "kitchen": f"Whether you're a cooking enthusiast or just starting out, the right tools make all the difference. Here are {len(products)} kitchen essentials that will upgrade your cooking game.",
            "tech": f"Stay connected and productive with these {len(products)} tech finds. From smart gadgets to everyday accessories, I've tested each one to bring you the best options available.",
            "fashion": f"Style doesn't have to cost a fortune. These {len(products)} fashion finds prove that you can look amazing on any budget. Each piece has been selected for quality and versatility.",
            "fitness": f"Achieve your fitness goals with the right gear. These {len(products)} products are perfect for home workouts or the gym, tested and approved for real results.",
        }

        niche_lower = niche_config.name.lower()
        return templates.get(niche_lower, f"Discover these {len(products)} amazing finds that deliver exceptional value. Each product has been carefully selected for quality and affordability.")

    def _generate_toc(self, products: List[Product]) -> str:
        """Generate table of contents."""
        toc_items = []
        for i, product in enumerate(products, 1):
            safe_title = html.escape(product.title[:50])
            anchor = self._generate_anchor(product.title)
            toc_items.append(f'<li><a href="#{anchor}">{i}. {safe_title}</a></li>')

        return f'''<div class="table-of-contents" style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<h3>Quick Navigation</h3>
<ol>
{"".join(toc_items)}
</ol>
</div>'''

    def _generate_comparison_table(self, products: List[Product]) -> str:
        """Generate product comparison table."""
        rows = []
        for product in products:
            safe_title = html.escape(product.title[:40])
            price_str = f"${product.price:.2f}" if product.price else "Check Price"
            rating_str = f"{product.rating}/5" if product.rating else "N/A"

            rows.append(f'''<tr>
<td><img src="{html.escape(product.image_url)}" alt="{safe_title}" width="60" height="60" style="object-fit:contain;"></td>
<td><strong>{safe_title}</strong></td>
<td>{price_str}</td>
<td>{rating_str}</td>
<td><a href="{html.escape(product.affiliate_url)}" target="_blank" rel="nofollow noopener" class="btn-cta">View Deal</a></td>
</tr>''')

        return f'''<div class="comparison-table" style="overflow-x:auto;margin:30px 0;">
<h2>Quick Comparison</h2>
<table style="width:100%;border-collapse:collapse;">
<thead>
<tr style="background:#f8f9fa;">
<th style="padding:12px;text-align:left;">Image</th>
<th style="padding:12px;text-align:left;">Product</th>
<th style="padding:12px;text-align:left;">Price</th>
<th style="padding:12px;text-align:left;">Rating</th>
<th style="padding:12px;text-align:left;">Action</th>
</tr>
</thead>
<tbody>
{"".join(rows)}
</tbody>
</table>
</div>'''

    def _generate_product_section(
        self,
        product: Product,
        position: int,
        total: int,
    ) -> str:
        """Generate individual product section."""
        anchor = self._generate_anchor(product.title)
        safe_title = html.escape(product.title)
        safe_bullets = html.escape(product.bullets) if product.bullets else ""

        # Format bullets as list if present
        bullets_html = ""
        if safe_bullets:
            bullet_items = safe_bullets.split("\n")
            bullets_list = "".join(f"<li>{b.strip('• ')}</li>" for b in bullet_items if b.strip())
            bullets_html = f"<ul class='product-bullets'>{bullets_list}</ul>"

        # Price display
        price_html = ""
        if product.price:
            price_html = f'<span class="product-price" style="font-size:24px;color:#B12704;font-weight:bold;">${product.price:.2f}</span>'

        # Rating display
        rating_html = ""
        if product.rating:
            stars = "★" * int(product.rating) + "☆" * (5 - int(product.rating))
            review_text = f"({product.review_count:,} reviews)" if product.review_count else ""
            rating_html = f'<div class="product-rating">{stars} {product.rating}/5 {review_text}</div>'

        # Badge for top pick
        badge_html = ""
        if position == 1:
            badge_html = '<span class="badge" style="background:#ff6b35;color:white;padding:5px 10px;border-radius:4px;font-size:12px;">TOP PICK</span>'
        elif position == 2:
            badge_html = '<span class="badge" style="background:#4ecdc4;color:white;padding:5px 10px;border-radius:4px;font-size:12px;">RUNNER UP</span>'
        elif position == 3:
            badge_html = '<span class="badge" style="background:#45b7d1;color:white;padding:5px 10px;border-radius:4px;font-size:12px;">BEST VALUE</span>'

        return f'''<div class="product-section" id="{anchor}" style="border:1px solid #e0e0e0;border-radius:12px;padding:25px;margin:30px 0;background:white;">
<div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;">
<h2 style="margin-top:0;">{position}. {safe_title}</h2>
{badge_html}
</div>

<div class="product-content" style="display:grid;grid-template-columns:250px 1fr;gap:25px;margin:20px 0;">
<div class="product-image">
<img src="{html.escape(product.image_url)}" alt="{safe_title}" style="max-width:100%;height:auto;border-radius:8px;">
</div>

<div class="product-details">
{price_html}
{rating_html}

{bullets_html}

<div class="product-cta" style="margin-top:20px;">
<a href="{html.escape(product.affiliate_url)}" target="_blank" rel="nofollow noopener sponsored" class="cta-button" style="display:inline-block;background:#ff9900;color:white;padding:15px 30px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px;">
{self.config.cta_templates.get("check_price", "Check Price on Amazon")}
</a>
</div>
</div>
</div>
</div>'''

    def _generate_faq(
        self,
        title: str,
        niche_config: NicheConfig,
        focus_keyword: str,
    ) -> str:
        """Generate FAQ section."""
        faqs = self._get_faqs_for_niche(niche_config.name.lower(), focus_keyword)

        faq_items = []
        for q, a in faqs:
            faq_items.append(f'''<div class="faq-item" style="margin-bottom:15px;">
<h4 style="margin-bottom:8px;">{html.escape(q)}</h4>
<p style="margin:0;color:#555;">{html.escape(a)}</p>
</div>''')

        return f'''<div class="faq-section" style="background:#f8f9fa;padding:25px;border-radius:12px;margin:30px 0;">
<h2>Frequently Asked Questions</h2>
{"".join(faq_items)}
</div>'''

    def _get_faqs_for_niche(self, niche: str, focus_keyword: str) -> List[tuple]:
        """Get FAQ questions and answers based on niche."""
        faqs = {
            "beauty": [
                ("Are these products safe for sensitive skin?", "Most of these products are formulated for all skin types. However, we recommend patch testing any new product before full application, especially if you have sensitive skin."),
                ("How long do these beauty products last?", "Most skincare products last 6-12 months after opening. Check the PAO (period after opening) symbol on each product for specific guidance."),
                ("Are these products cruelty-free?", "We prioritize featuring cruelty-free options when available. Each product listing notes if it's certified cruelty-free."),
            ],
            "home": [
                ("Are these products easy to assemble?", "Most products require minimal assembly. We've selected items that are user-friendly and include clear instructions."),
                ("What's the return policy on Amazon?", "Amazon typically offers a 30-day return window for most items. Prime members may have extended return options."),
                ("Do these products come with warranties?", "Warranty coverage varies by manufacturer. Check individual product listings for specific warranty information."),
            ],
            "kitchen": [
                ("Are these kitchen products dishwasher safe?", "Most products listed include dishwasher-safe components. Check individual product descriptions for specific care instructions."),
                ("Do these products come with recipes or guides?", "Many kitchen gadgets include recipe books or access to online recipe collections. This is noted in each product description."),
                ("What's the best way to maintain these products?", "Follow manufacturer care instructions for longevity. Most items require simple cleaning after use."),
            ],
            "tech": [
                ("Do these products come with warranties?", "Most tech products include manufacturer warranties ranging from 1-2 years. Extended protection plans are often available through Amazon."),
                ("Are these compatible with my existing devices?", "Compatibility information is listed in each product section. Most items work with popular ecosystems like iOS, Android, Alexa, and Google Home."),
                ("What if I need technical support?", "Manufacturers provide customer support, and Amazon offers assistance for order-related issues."),
            ],
            "fashion": [
                ("How do I find my correct size?", "Each product includes a size guide. We recommend measuring yourself and comparing to the size chart before ordering."),
                ("What's the quality like compared to designer brands?", "These products offer excellent quality for their price point. While not identical to luxury brands, they provide great value and style."),
                ("Are these products sustainable?", "We've included information about sustainability and materials where available."),
            ],
            "fitness": [
                ("Are these products suitable for beginners?", "Yes! We've selected items appropriate for all fitness levels. Start with lighter weights or lower resistance and progress gradually."),
                ("Do I need additional equipment?", "Most products work standalone, though some may be enhanced with additional accessories noted in the description."),
                ("Are these products durable for daily use?", "All featured products are selected for durability and have positive long-term reviews from regular users."),
            ],
        }

        return faqs.get(niche, [
            (f"What makes these {focus_keyword} worth buying?", "Each product is selected based on quality, value, and positive customer reviews."),
            ("How do you choose which products to feature?", "We research thoroughly, read reviews, and often test products ourselves before recommending them."),
            ("Are these products available for Prime shipping?", "Most products qualify for Prime shipping. Check individual listings for current availability."),
        ])

    def _generate_conclusion(self, title: str, products: List[Product]) -> str:
        """Generate conclusion section."""
        return f'''<div class="conclusion" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:30px;border-radius:12px;margin:30px 0;">
<h2 style="color:white;margin-top:0;">The Bottom Line</h2>
<p>We hope this roundup helps you find exactly what you're looking for! Each product has been carefully selected for quality and value. Remember to check current prices on Amazon as deals can change.</p>
<p><strong>Our top recommendation:</strong> {html.escape(products[0].title) if products else "See our featured picks above"}</p>
<p style="margin-bottom:0;">Happy shopping! If you found this helpful, please share it with friends who might benefit.</p>
</div>'''

    def _generate_email_cta(self, niche_config: NicheConfig) -> str:
        """Generate email signup CTA."""
        return f'''<div class="email-cta" style="text-align:center;background:#f8f9fa;padding:30px;border-radius:12px;margin:30px 0;">
<h3>Get Exclusive Deals in Your Inbox</h3>
<p>Subscribe for weekly roundups of the best {niche_config.name.lower()} finds and exclusive discount codes!</p>
<div style="margin-top:20px;">
<!-- Email signup form placeholder - integrate with your email service -->
<a href="#subscribe" class="email-btn" style="display:inline-block;background:#ff6b35;color:white;padding:15px 40px;border-radius:8px;text-decoration:none;font-weight:bold;">Subscribe Now</a>
</div>
</div>'''

    def _generate_anchor(self, title: str) -> str:
        """Generate HTML anchor from title."""
        anchor = re.sub(r'[^\w\s-]', '', title.lower())
        anchor = re.sub(r'[-\s]+', '-', anchor)
        return anchor[:30].strip('-')

    # ===========================================
    # Schema Generation
    # ===========================================

    def _generate_schema(
        self,
        title: str,
        slug: str,
        products: List[Product],
        description: str,
    ) -> str:
        """Generate JSON-LD schema markup."""
        now = datetime.now().isoformat()
        permalink = f"{self.config.site_url}/{slug}/"

        # Article schema
        article_schema = {
            "@context": "https://schema.org",
            "@type": "Article",
            "headline": title,
            "description": description,
            "author": {
                "@type": "Person",
                "name": self.config.author_name,
                "url": self.config.about_url
            },
            "publisher": {
                "@type": "Organization",
                "name": self.config.site_name,
                "logo": {
                    "@type": "ImageObject",
                    "url": self.config.logo_url
                }
            },
            "datePublished": now,
            "dateModified": now,
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": permalink
            }
        }

        # ItemList schema for the product list
        item_list_schema = {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": title,
            "description": description,
            "numberOfItems": len(products),
            "itemListElement": []
        }

        for i, product in enumerate(products, 1):
            item_list_schema["itemListElement"].append({
                "@type": "ListItem",
                "position": i,
                "name": product.title,
                "url": product.affiliate_url
            })

        # Product schemas for individual products
        product_schemas = []
        for product in products[:5]:  # Limit to top 5 for schema
            if product.price:
                product_schema = {
                    "@context": "https://schema.org",
                    "@type": "Product",
                    "name": product.title,
                    "image": product.image_url,
                    "offers": {
                        "@type": "Offer",
                        "url": product.affiliate_url,
                        "priceCurrency": "USD",
                        "price": product.price,
                        "availability": "https://schema.org/InStock",
                        "seller": {
                            "@type": "Organization",
                            "name": "Amazon"
                        }
                    }
                }
                if product.rating:
                    product_schema["aggregateRating"] = {
                        "@type": "AggregateRating",
                        "ratingValue": product.rating,
                        "bestRating": "5",
                        "reviewCount": product.review_count or 1
                    }
                product_schemas.append(product_schema)

        # Combine all schemas
        all_schemas = [article_schema, item_list_schema] + product_schemas

        # Return as script tags
        schema_scripts = []
        for schema in all_schemas:
            schema_scripts.append(
                f'<script type="application/ld+json">\n{json.dumps(schema, indent=2)}\n</script>'
            )

        return "\n".join(schema_scripts)


# ===========================================
# Utility Functions
# ===========================================

def generate_post_slug(title: str, date: datetime = None) -> str:
    """
    Generate a unique post slug from title.

    Args:
        title: Post title
        date: Optional date to append

    Returns:
        URL-friendly slug
    """
    slug = title.lower()
    slug = re.sub(r'[^\w\s-]', '', slug)
    slug = re.sub(r'[-\s]+', '-', slug)
    slug = slug.strip('-')[:50]

    if date:
        slug = f"{slug}-{date.strftime('%Y-%m')}"

    return slug


def generate_daily_deals_title(date: datetime = None) -> str:
    """Generate title for daily deals post."""
    if date is None:
        date = datetime.now()

    date_str = date.strftime("%B %d, %Y")
    return f"Today's Best Amazon Deals: {date_str}"


def create_product_card_shortcode(product: Product) -> str:
    """Create WordPress shortcode for a product card."""
    return f'''[product_card
    asin="{product.asin}"
    title="{product.title}"
    price="{product.price}"
    image="{product.image_url}"
    rating="{product.rating}"
    link="{product.affiliate_url}"]'''
