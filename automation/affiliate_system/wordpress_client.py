"""
WordPress REST API client for the ShelzyPerkins Affiliate System.

Handles post creation, media uploads, and category management.
"""

import requests
from requests.auth import HTTPBasicAuth
from typing import Any, Dict, List, Optional
from dataclasses import dataclass
from datetime import datetime
import base64
import time
import json

from .config import WordPressConfig


@dataclass
class WPPost:
    """WordPress post data structure."""
    title: str
    content: str
    slug: str = ""
    status: str = "publish"  # publish, draft, pending, private
    excerpt: str = ""
    categories: List[int] = None
    tags: List[int] = None
    featured_media: int = 0
    meta: Dict[str, Any] = None

    # SEO fields (for Rank Math / Yoast)
    meta_title: str = ""
    meta_description: str = ""
    focus_keyword: str = ""

    def __post_init__(self):
        if self.categories is None:
            self.categories = []
        if self.tags is None:
            self.tags = []
        if self.meta is None:
            self.meta = {}

    def to_wp_payload(self) -> Dict[str, Any]:
        """Convert to WordPress REST API payload."""
        payload = {
            "title": self.title,
            "content": self.content,
            "status": self.status,
        }

        if self.slug:
            payload["slug"] = self.slug
        if self.excerpt:
            payload["excerpt"] = self.excerpt
        if self.categories:
            payload["categories"] = self.categories
        if self.tags:
            payload["tags"] = self.tags
        if self.featured_media:
            payload["featured_media"] = self.featured_media

        # Add meta fields for SEO plugins
        meta = dict(self.meta)
        if self.meta_title:
            meta["_yoast_wpseo_title"] = self.meta_title
            meta["rank_math_title"] = self.meta_title
        if self.meta_description:
            meta["_yoast_wpseo_metadesc"] = self.meta_description
            meta["rank_math_description"] = self.meta_description
        if self.focus_keyword:
            meta["_yoast_wpseo_focuskw"] = self.focus_keyword
            meta["rank_math_focus_keyword"] = self.focus_keyword

        if meta:
            payload["meta"] = meta

        return payload


class WordPressClient:
    """
    WordPress REST API client for publishing posts.

    Usage:
        config = Config()
        wp = WordPressClient(config.wordpress)

        # Create a post
        post_url = wp.create_post(
            title="10 Best Amazon Beauty Dupes",
            slug="best-amazon-beauty-dupes",
            content="<p>Your HTML content here...</p>",
            meta_title="10 Best Amazon Beauty Dupes 2024 | Save Money Today",
            meta_description="Discover the best Amazon beauty dupes..."
        )

        # Get categories
        categories = wp.get_categories()

        # Upload media
        media_id = wp.upload_media_from_url("https://example.com/image.jpg")
    """

    def __init__(self, config: WordPressConfig):
        """
        Initialize WordPress client.

        Args:
            config: WordPressConfig with site URL and credentials
        """
        self.config = config
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(config.username, config.app_password)
        self.session.headers.update({
            "Content-Type": "application/json",
            "Accept": "application/json",
        })

        # Cache for categories and tags
        self._categories_cache: Dict[str, int] = {}
        self._tags_cache: Dict[str, int] = {}

    def _get_url(self, endpoint: str) -> str:
        """Get full API URL for an endpoint."""
        return f"{self.config.api_base}/{endpoint}"

    def _handle_response(self, response: requests.Response) -> Dict[str, Any]:
        """Handle API response and raise appropriate errors."""
        if not response.ok:
            error_msg = f"WordPress API error: {response.status_code}"
            try:
                error_data = response.json()
                error_msg += f" - {error_data.get('message', response.text)}"
            except json.JSONDecodeError:
                error_msg += f" - {response.text}"
            raise Exception(error_msg)

        return response.json()

    # ===========================================
    # Posts
    # ===========================================

    def create_post(
        self,
        title: str,
        content: str,
        slug: str = "",
        status: str = "publish",
        excerpt: str = "",
        categories: List[int] = None,
        tags: List[int] = None,
        featured_media: int = 0,
        meta_title: str = "",
        meta_description: str = "",
        focus_keyword: str = "",
        meta: Dict[str, Any] = None,
    ) -> str:
        """
        Create a new WordPress post.

        Args:
            title: Post title
            content: Full HTML content
            slug: URL slug (auto-generated if not provided)
            status: Post status (publish, draft, pending, private)
            excerpt: Post excerpt
            categories: List of category IDs
            tags: List of tag IDs
            featured_media: Featured image media ID
            meta_title: SEO meta title
            meta_description: SEO meta description
            focus_keyword: SEO focus keyword
            meta: Additional meta fields

        Returns:
            URL of the created post
        """
        post = WPPost(
            title=title,
            content=content,
            slug=slug,
            status=status,
            excerpt=excerpt,
            categories=categories or [],
            tags=tags or [],
            featured_media=featured_media,
            meta_title=meta_title,
            meta_description=meta_description,
            focus_keyword=focus_keyword,
            meta=meta or {},
        )

        url = self._get_url("posts")
        response = self.session.post(url, json=post.to_wp_payload())
        data = self._handle_response(response)

        return data.get("link", "")

    def update_post(self, post_id: int, fields: Dict[str, Any]) -> Dict[str, Any]:
        """
        Update an existing post.

        Args:
            post_id: Post ID to update
            fields: Fields to update

        Returns:
            Updated post data
        """
        url = self._get_url(f"posts/{post_id}")
        response = self.session.post(url, json=fields)
        return self._handle_response(response)

    def get_post(self, post_id: int) -> Dict[str, Any]:
        """Get a post by ID."""
        url = self._get_url(f"posts/{post_id}")
        response = self.session.get(url)
        return self._handle_response(response)

    def get_post_by_slug(self, slug: str) -> Optional[Dict[str, Any]]:
        """Get a post by slug."""
        url = self._get_url("posts")
        response = self.session.get(url, params={"slug": slug})
        data = self._handle_response(response)
        return data[0] if data else None

    def delete_post(self, post_id: int, force: bool = False) -> bool:
        """
        Delete a post.

        Args:
            post_id: Post ID to delete
            force: If True, permanently delete; otherwise move to trash

        Returns:
            True if deleted successfully
        """
        url = self._get_url(f"posts/{post_id}")
        params = {"force": force}
        response = self.session.delete(url, params=params)
        self._handle_response(response)
        return True

    # ===========================================
    # Categories
    # ===========================================

    def get_categories(self, per_page: int = 100) -> List[Dict[str, Any]]:
        """Get all categories."""
        url = self._get_url("categories")
        response = self.session.get(url, params={"per_page": per_page})
        categories = self._handle_response(response)

        # Update cache
        for cat in categories:
            self._categories_cache[cat["name"].lower()] = cat["id"]
            self._categories_cache[cat["slug"]] = cat["id"]

        return categories

    def get_category_id(self, name_or_slug: str) -> Optional[int]:
        """Get category ID by name or slug."""
        key = name_or_slug.lower()
        if key in self._categories_cache:
            return self._categories_cache[key]

        # Fetch and try again
        self.get_categories()
        return self._categories_cache.get(key)

    def create_category(self, name: str, slug: str = "", description: str = "") -> int:
        """
        Create a new category.

        Args:
            name: Category name
            slug: URL slug (auto-generated if not provided)
            description: Category description

        Returns:
            Created category ID
        """
        url = self._get_url("categories")
        payload = {"name": name}
        if slug:
            payload["slug"] = slug
        if description:
            payload["description"] = description

        response = self.session.post(url, json=payload)
        data = self._handle_response(response)
        return data["id"]

    def get_or_create_category(self, name: str) -> int:
        """Get category ID by name, creating it if it doesn't exist."""
        cat_id = self.get_category_id(name)
        if cat_id:
            return cat_id
        return self.create_category(name)

    # ===========================================
    # Tags
    # ===========================================

    def get_tags(self, per_page: int = 100) -> List[Dict[str, Any]]:
        """Get all tags."""
        url = self._get_url("tags")
        response = self.session.get(url, params={"per_page": per_page})
        tags = self._handle_response(response)

        # Update cache
        for tag in tags:
            self._tags_cache[tag["name"].lower()] = tag["id"]
            self._tags_cache[tag["slug"]] = tag["id"]

        return tags

    def get_tag_id(self, name_or_slug: str) -> Optional[int]:
        """Get tag ID by name or slug."""
        key = name_or_slug.lower()
        if key in self._tags_cache:
            return self._tags_cache[key]

        # Fetch and try again
        self.get_tags()
        return self._tags_cache.get(key)

    def create_tag(self, name: str, slug: str = "") -> int:
        """
        Create a new tag.

        Args:
            name: Tag name
            slug: URL slug (auto-generated if not provided)

        Returns:
            Created tag ID
        """
        url = self._get_url("tags")
        payload = {"name": name}
        if slug:
            payload["slug"] = slug

        response = self.session.post(url, json=payload)
        data = self._handle_response(response)
        return data["id"]

    def get_or_create_tag(self, name: str) -> int:
        """Get tag ID by name, creating it if it doesn't exist."""
        tag_id = self.get_tag_id(name)
        if tag_id:
            return tag_id
        return self.create_tag(name)

    def get_or_create_tags(self, names: List[str]) -> List[int]:
        """Get or create multiple tags."""
        return [self.get_or_create_tag(name) for name in names]

    # ===========================================
    # Media
    # ===========================================

    def upload_media(
        self,
        file_content: bytes,
        filename: str,
        mime_type: str = "image/jpeg",
        alt_text: str = "",
        caption: str = "",
    ) -> int:
        """
        Upload media to WordPress.

        Args:
            file_content: Raw file bytes
            filename: File name
            mime_type: MIME type
            alt_text: Image alt text
            caption: Image caption

        Returns:
            Uploaded media ID
        """
        url = self._get_url("media")

        # Use multipart form for file upload
        headers = {
            "Content-Disposition": f'attachment; filename="{filename}"',
            "Content-Type": mime_type,
        }

        response = self.session.post(
            url,
            data=file_content,
            headers=headers,
        )
        data = self._handle_response(response)
        media_id = data["id"]

        # Update alt text and caption if provided
        if alt_text or caption:
            update_url = self._get_url(f"media/{media_id}")
            update_payload = {}
            if alt_text:
                update_payload["alt_text"] = alt_text
            if caption:
                update_payload["caption"] = caption
            self.session.post(update_url, json=update_payload)

        return media_id

    def upload_media_from_url(
        self,
        image_url: str,
        filename: str = "",
        alt_text: str = "",
        caption: str = "",
    ) -> int:
        """
        Download an image from URL and upload to WordPress.

        Args:
            image_url: URL of the image to download
            filename: Optional filename (extracted from URL if not provided)
            alt_text: Image alt text
            caption: Image caption

        Returns:
            Uploaded media ID
        """
        # Download the image
        response = requests.get(image_url, timeout=30)
        response.raise_for_status()

        # Determine filename
        if not filename:
            from urllib.parse import urlparse
            parsed = urlparse(image_url)
            filename = parsed.path.split("/")[-1]
            if not filename or "." not in filename:
                filename = "image.jpg"

        # Determine MIME type
        content_type = response.headers.get("Content-Type", "image/jpeg")

        return self.upload_media(
            file_content=response.content,
            filename=filename,
            mime_type=content_type,
            alt_text=alt_text,
            caption=caption,
        )

    # ===========================================
    # Utility Methods
    # ===========================================

    def test_connection(self) -> bool:
        """
        Test the WordPress API connection.

        Returns:
            True if connection is successful
        """
        try:
            url = self._get_url("users/me")
            response = self.session.get(url)
            self._handle_response(response)
            return True
        except Exception as e:
            print(f"WordPress connection test failed: {e}")
            return False

    def get_site_info(self) -> Dict[str, Any]:
        """Get WordPress site information."""
        # Use the base WP REST API endpoint
        url = self.config.url.rstrip('/') + "/wp-json"
        response = self.session.get(url)
        return self._handle_response(response)


def create_wp_post(
    config: WordPressConfig,
    title: str,
    slug: str,
    html_content: str,
    meta_title: str = "",
    meta_description: str = "",
    category_ids: List[int] = None,
    tag_ids: List[int] = None,
    status: str = "publish",
) -> str:
    """
    Convenience function to create a WordPress post.

    This is the main helper function mentioned in the task requirements.

    Args:
        config: WordPress configuration
        title: Post title
        slug: Post slug
        html_content: Full HTML content
        meta_title: SEO meta title
        meta_description: SEO meta description
        category_ids: List of category IDs
        tag_ids: List of tag IDs
        status: Post status (default: publish)

    Returns:
        URL of the published post
    """
    client = WordPressClient(config)

    return client.create_post(
        title=title,
        content=html_content,
        slug=slug,
        status=status,
        categories=category_ids or [],
        tags=tag_ids or [],
        meta_title=meta_title,
        meta_description=meta_description,
    )
