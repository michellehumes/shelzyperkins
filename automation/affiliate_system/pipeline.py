"""
Main Orchestration Pipeline for the ShelzyPerkins Affiliate System.

This module ties together all components to provide an end-to-end
automation pipeline for affiliate content:

1. Discover trending Amazon products
2. Store product data in Airtable
3. Generate SEO-optimized roundup posts
4. Publish to WordPress
5. Generate social + email content
6. Log distribution content to Airtable
"""

from typing import Any, Dict, List, Optional
from dataclasses import dataclass
from datetime import datetime
import time

from .config import Config
from .airtable_client import AirtableClient, Product, Distribution
from .wordpress_client import WordPressClient
from .product_discovery import ProductDiscovery, ROUNDUP_TOPICS
from .content_generator import ContentGenerator, GeneratedPost
from .distribution_generator import DistributionGenerator, DistributionContent


@dataclass
class PipelineResult:
    """Result of a pipeline run."""
    success: bool
    post_slug: str
    post_url: str = ""
    products_count: int = 0
    message: str = ""
    error: str = ""


class AffiliatePipeline:
    """
    End-to-end affiliate content automation pipeline.

    This is the main class that orchestrates the entire workflow:
    1. Discover products for a topic/niche
    2. Store product data in Airtable
    3. Generate SEO-optimized blog post
    4. Publish to WordPress
    5. Generate social media + email content
    6. Save distribution content to Airtable

    Usage:
        # Initialize with config
        config = Config(env_file=".env")
        pipeline = AffiliatePipeline(config)

        # Run full pipeline for a topic
        result = pipeline.run_full_pipeline(
            keyword="best beauty dupes",
            niche="beauty",
            title="10 Best Amazon Beauty Dupes That Actually Work"
        )

        if result.success:
            print(f"Published: {result.post_url}")
        else:
            print(f"Error: {result.error}")

        # Or run individual steps
        products = pipeline.discover_products("beauty dupes", "beauty", 10)
        pipeline.save_products_to_airtable(products)
    """

    def __init__(self, config: Config):
        """
        Initialize the affiliate pipeline.

        Args:
            config: Configuration object with all credentials
        """
        self.config = config

        # Initialize clients (lazy loading)
        self._airtable: Optional[AirtableClient] = None
        self._wordpress: Optional[WordPressClient] = None
        self._discovery: Optional[ProductDiscovery] = None
        self._content_gen: Optional[ContentGenerator] = None
        self._distribution_gen: Optional[DistributionGenerator] = None

    # ===========================================
    # Lazy-loaded Clients
    # ===========================================

    @property
    def airtable(self) -> AirtableClient:
        """Get Airtable client (lazy loaded)."""
        if self._airtable is None:
            self._airtable = AirtableClient(self.config.airtable)
        return self._airtable

    @property
    def wordpress(self) -> WordPressClient:
        """Get WordPress client (lazy loaded)."""
        if self._wordpress is None:
            self._wordpress = WordPressClient(self.config.wordpress)
        return self._wordpress

    @property
    def discovery(self) -> ProductDiscovery:
        """Get product discovery service (lazy loaded)."""
        if self._discovery is None:
            self._discovery = ProductDiscovery(self.config.amazon)
        return self._discovery

    @property
    def content_generator(self) -> ContentGenerator:
        """Get content generator (lazy loaded)."""
        if self._content_gen is None:
            self._content_gen = ContentGenerator(self.config.content, self.config.niches)
        return self._content_gen

    @property
    def distribution_generator(self) -> DistributionGenerator:
        """Get distribution generator (lazy loaded)."""
        if self._distribution_gen is None:
            self._distribution_gen = DistributionGenerator(self.config.niches)
        return self._distribution_gen

    # ===========================================
    # Full Pipeline
    # ===========================================

    def run_full_pipeline(
        self,
        keyword: str,
        niche: str,
        title: str,
        product_count: int = 10,
        custom_intro: str = "",
        publish_status: str = "publish",
        save_to_airtable: bool = True,
    ) -> PipelineResult:
        """
        Run the complete affiliate content pipeline.

        Steps:
        1. Discover products for the keyword/niche
        2. Save products to Airtable
        3. Generate SEO-optimized blog post
        4. Publish to WordPress
        5. Generate social/email content
        6. Save distribution content to Airtable
        7. Update product records with published URL

        Args:
            keyword: Search keyword for product discovery
            niche: Product niche (beauty, home, kitchen, tech, fashion, fitness)
            title: Blog post title
            product_count: Number of products to feature
            custom_intro: Custom introduction text
            publish_status: WordPress post status (publish, draft)
            save_to_airtable: Whether to save to Airtable

        Returns:
            PipelineResult with success status and details
        """
        print(f"\n{'='*60}")
        print(f"Running Affiliate Pipeline")
        print(f"Title: {title}")
        print(f"Niche: {niche}")
        print(f"{'='*60}\n")

        try:
            # Step 1: Discover Products
            print("Step 1: Discovering products...")
            products = self.discover_products(keyword, niche, product_count)
            if not products:
                return PipelineResult(
                    success=False,
                    post_slug="",
                    error="No products found for the given keyword/niche"
                )
            print(f"  Found {len(products)} products")

            # Step 2: Save to Airtable
            if save_to_airtable and self.config.airtable.is_configured:
                print("Step 2: Saving products to Airtable...")
                record_ids = self.save_products_to_airtable(products)
                print(f"  Created {len(record_ids)} records")
            else:
                print("Step 2: Skipping Airtable (not configured or disabled)")
                record_ids = []

            # Step 3: Generate Blog Post
            print("Step 3: Generating blog post content...")
            generated_post = self.generate_post(
                products=products,
                title=title,
                niche=niche,
                focus_keyword=keyword,
                custom_intro=custom_intro,
            )
            print(f"  Generated post: {generated_post.slug}")

            # Step 4: Publish to WordPress
            post_url = ""
            if self.config.wordpress.is_configured:
                print("Step 4: Publishing to WordPress...")
                post_url = self.publish_to_wordpress(generated_post, status=publish_status)
                print(f"  Published: {post_url}")
            else:
                print("Step 4: Skipping WordPress (not configured)")
                post_url = f"{self.config.content.site_url}/{generated_post.slug}/"

            # Step 5: Generate Distribution Content
            print("Step 5: Generating social/email content...")
            distribution = self.generate_distribution(
                post_slug=generated_post.slug,
                post_title=title,
                post_url=post_url,
                niche=niche,
                products=products,
            )
            print("  Generated distribution content")

            # Step 6: Save Distribution to Airtable
            if save_to_airtable and self.config.airtable.is_configured:
                print("Step 6: Saving distribution to Airtable...")
                dist_id = self.save_distribution_to_airtable(distribution)
                print(f"  Created distribution record: {dist_id}")
            else:
                print("Step 6: Skipping distribution save")

            # Step 7: Update Product Records
            if record_ids and post_url:
                print("Step 7: Updating product records...")
                self.update_products_with_url(record_ids, generated_post.slug, post_url)
                print("  Updated product statuses")

            print(f"\n{'='*60}")
            print("Pipeline completed successfully!")
            print(f"{'='*60}\n")

            return PipelineResult(
                success=True,
                post_slug=generated_post.slug,
                post_url=post_url,
                products_count=len(products),
                message="Pipeline completed successfully",
            )

        except Exception as e:
            print(f"\nError in pipeline: {str(e)}")
            return PipelineResult(
                success=False,
                post_slug="",
                error=str(e),
            )

    # ===========================================
    # Individual Pipeline Steps
    # ===========================================

    def discover_products(
        self,
        keyword: str,
        niche: str,
        count: int = 10,
    ) -> List[Product]:
        """
        Step 1: Discover products for a keyword/niche.

        Args:
            keyword: Search keyword
            niche: Product niche
            count: Number of products to find

        Returns:
            List of Product objects
        """
        return self.discovery.discover_products_for_roundup(keyword, niche, count)

    def save_products_to_airtable(self, products: List[Product]) -> List[str]:
        """
        Step 2: Save products to Airtable.

        Args:
            products: List of Product objects

        Returns:
            List of created record IDs
        """
        record_ids = []
        for product in products:
            record_id = self.airtable.create_product(product)
            record_ids.append(record_id)
            time.sleep(0.2)  # Rate limiting
        return record_ids

    def generate_post(
        self,
        products: List[Product],
        title: str,
        niche: str,
        focus_keyword: str = "",
        custom_intro: str = "",
    ) -> GeneratedPost:
        """
        Step 3: Generate SEO-optimized blog post.

        Args:
            products: List of Product objects
            title: Post title
            niche: Product niche
            focus_keyword: SEO focus keyword
            custom_intro: Custom introduction text

        Returns:
            GeneratedPost with HTML content and metadata
        """
        return self.content_generator.generate_roundup_post(
            products=products,
            title=title,
            niche=niche,
            focus_keyword=focus_keyword,
            intro_text=custom_intro,
        )

    def publish_to_wordpress(
        self,
        post: GeneratedPost,
        status: str = "publish",
    ) -> str:
        """
        Step 4: Publish post to WordPress.

        Args:
            post: GeneratedPost object
            status: Post status (publish, draft)

        Returns:
            URL of the published post
        """
        # Combine HTML content with schema
        full_content = post.html_content + "\n\n" + post.schema_json

        # Get or create category
        category_ids = []
        if post.category:
            cat_id = self.wordpress.get_or_create_category(post.category)
            category_ids.append(cat_id)

        # Get or create tags
        tag_ids = self.wordpress.get_or_create_tags(post.tags)

        return self.wordpress.create_post(
            title=post.title,
            content=full_content,
            slug=post.slug,
            status=status,
            categories=category_ids,
            tags=tag_ids,
            meta_title=post.meta_title,
            meta_description=post.meta_description,
            focus_keyword=post.focus_keyword,
        )

    def generate_distribution(
        self,
        post_slug: str,
        post_title: str,
        post_url: str,
        niche: str,
        products: List[Product],
    ) -> DistributionContent:
        """
        Step 5: Generate social media and email content.

        Args:
            post_slug: Post URL slug
            post_title: Post title
            post_url: Full post URL
            niche: Content niche
            products: List of featured products

        Returns:
            DistributionContent with all platform content
        """
        return self.distribution_generator.generate_all(
            post_slug=post_slug,
            post_title=post_title,
            post_url=post_url,
            niche=niche,
            products=products,
        )

    def save_distribution_to_airtable(self, distribution: DistributionContent) -> str:
        """
        Step 6: Save distribution content to Airtable.

        Args:
            distribution: DistributionContent object

        Returns:
            Created record ID
        """
        dist_record = distribution.to_distribution()
        return self.airtable.create_distribution(dist_record)

    def update_products_with_url(
        self,
        record_ids: List[str],
        post_slug: str,
        post_url: str,
    ) -> None:
        """
        Step 7: Update product records with published URL.

        Args:
            record_ids: List of product record IDs
            post_slug: Post slug
            post_url: Published post URL
        """
        for record_id in record_ids:
            self.airtable.update_product_status(
                record_id=record_id,
                status="published",
                published_url=post_url,
            )
            time.sleep(0.2)  # Rate limiting

    # ===========================================
    # Batch Operations
    # ===========================================

    def run_batch_pipeline(
        self,
        topics: List[Dict[str, str]],
        delay_between: int = 60,
    ) -> List[PipelineResult]:
        """
        Run pipeline for multiple topics.

        Args:
            topics: List of dicts with keys: keyword, niche, title
            delay_between: Seconds to wait between posts

        Returns:
            List of PipelineResult objects
        """
        results = []

        for i, topic in enumerate(topics):
            print(f"\n\nProcessing topic {i+1}/{len(topics)}: {topic.get('title', 'Unknown')}")

            result = self.run_full_pipeline(
                keyword=topic.get("keyword", ""),
                niche=topic.get("niche", ""),
                title=topic.get("title", ""),
                product_count=topic.get("product_count", 10),
            )
            results.append(result)

            # Wait between posts (except for last one)
            if i < len(topics) - 1:
                print(f"\nWaiting {delay_between} seconds before next post...")
                time.sleep(delay_between)

        return results

    def process_queued_products(self) -> List[PipelineResult]:
        """
        Process all products in 'queued' status.

        Groups products by niche and generates roundup posts.

        Returns:
            List of PipelineResult objects
        """
        # Get all queued products
        queued = self.airtable.get_queued_products()

        if not queued:
            print("No queued products found")
            return []

        # Group by niche
        by_niche: Dict[str, List[Product]] = {}
        for product in queued:
            niche = product.niche or "general"
            if niche not in by_niche:
                by_niche[niche] = []
            by_niche[niche].append(product)

        # Generate post for each niche
        results = []
        for niche, products in by_niche.items():
            title = f"Best {niche.title()} Finds on Amazon"
            keyword = f"best {niche} amazon"

            # Generate and publish
            post = self.generate_post(products, title, niche, keyword)

            if self.config.wordpress.is_configured:
                post_url = self.publish_to_wordpress(post)
            else:
                post_url = f"{self.config.content.site_url}/{post.slug}/"

            # Update product statuses
            for product in products:
                if product.record_id:
                    self.airtable.update_product_status(
                        product.record_id,
                        "published",
                        post_url,
                    )

            results.append(PipelineResult(
                success=True,
                post_slug=post.slug,
                post_url=post_url,
                products_count=len(products),
            ))

        return results

    # ===========================================
    # Utility Methods
    # ===========================================

    def test_connections(self) -> Dict[str, bool]:
        """
        Test all API connections.

        Returns:
            Dict with service names and connection status
        """
        status = {}

        # Test Airtable
        if self.config.airtable.is_configured:
            try:
                self.airtable.get_records(
                    self.config.airtable.products_table,
                    max_records=1
                )
                status["airtable"] = True
            except Exception as e:
                print(f"Airtable error: {e}")
                status["airtable"] = False
        else:
            status["airtable"] = False

        # Test WordPress
        if self.config.wordpress.is_configured:
            status["wordpress"] = self.wordpress.test_connection()
        else:
            status["wordpress"] = False

        return status

    def get_suggested_topics(self, niche: str) -> List[Dict[str, str]]:
        """
        Get suggested roundup topics for a niche.

        Args:
            niche: Product niche

        Returns:
            List of topic dictionaries
        """
        return ROUNDUP_TOPICS.get(niche.lower(), [])


# ===========================================
# Convenience Functions
# ===========================================

def create_pipeline(env_file: str = ".env") -> AffiliatePipeline:
    """
    Create a pipeline instance with config from environment.

    Args:
        env_file: Path to .env file

    Returns:
        Configured AffiliatePipeline instance
    """
    config = Config(env_file=env_file)
    return AffiliatePipeline(config)


def quick_publish(
    title: str,
    keyword: str,
    niche: str,
    env_file: str = ".env",
) -> PipelineResult:
    """
    Quick publish a roundup post.

    Args:
        title: Post title
        keyword: Search keyword
        niche: Product niche
        env_file: Path to .env file

    Returns:
        PipelineResult
    """
    pipeline = create_pipeline(env_file)
    return pipeline.run_full_pipeline(
        keyword=keyword,
        niche=niche,
        title=title,
    )


def daily_deals_post(env_file: str = ".env") -> PipelineResult:
    """
    Generate and publish daily deals post.

    Args:
        env_file: Path to .env file

    Returns:
        PipelineResult
    """
    today = datetime.now()
    title = f"Today's Best Amazon Deals: {today.strftime('%B %d, %Y')}"

    pipeline = create_pipeline(env_file)

    # Get products from multiple niches
    all_products = []
    for niche in ["beauty", "home", "tech", "fashion"]:
        products = pipeline.discover_products("deals", niche, 3)
        all_products.extend(products)

    # Generate and publish
    post = pipeline.generate_post(
        products=all_products[:10],
        title=title,
        niche="deals",
        focus_keyword="amazon deals today",
    )

    if pipeline.config.wordpress.is_configured:
        post_url = pipeline.publish_to_wordpress(post)
    else:
        post_url = f"{pipeline.config.content.site_url}/{post.slug}/"

    return PipelineResult(
        success=True,
        post_slug=post.slug,
        post_url=post_url,
        products_count=len(all_products),
    )
