"""
Airtable API client for the ShelzyPerkins Affiliate System.

Handles CRUD operations for Products and Distribution tables.
"""

import requests
from typing import Any, Dict, List, Optional
from dataclasses import dataclass, field, asdict
from datetime import datetime
import time
import json

from .config import Config, AirtableConfig


@dataclass
class Product:
    """
    Product data model matching the Airtable Products table schema.
    """
    keyword: str = ""
    niche: str = ""
    asin: str = ""
    product_url: str = ""
    title: str = ""
    price: float = 0.0
    rating: float = 0.0
    review_count: int = 0
    bullets: str = ""
    image_url: str = ""
    affiliate_url: str = ""
    post_slug: str = ""
    status: str = "queued"  # queued, ready_to_write, drafted, published, error
    published_url: str = ""
    notes: str = ""

    # Airtable metadata (populated when fetching)
    record_id: Optional[str] = None
    created_time: Optional[str] = None

    def to_airtable_fields(self) -> Dict[str, Any]:
        """Convert to Airtable fields format (excludes metadata)."""
        return {
            "keyword": self.keyword,
            "niche": self.niche,
            "asin": self.asin,
            "product_url": self.product_url,
            "title": self.title,
            "price": self.price,
            "rating": self.rating,
            "review_count": self.review_count,
            "bullets": self.bullets,
            "image_url": self.image_url,
            "affiliate_url": self.affiliate_url,
            "post_slug": self.post_slug,
            "status": self.status,
            "published_url": self.published_url,
            "notes": self.notes,
        }

    @classmethod
    def from_airtable_record(cls, record: Dict[str, Any]) -> "Product":
        """Create Product from Airtable record."""
        fields = record.get("fields", {})
        return cls(
            keyword=fields.get("keyword", ""),
            niche=fields.get("niche", ""),
            asin=fields.get("asin", ""),
            product_url=fields.get("product_url", ""),
            title=fields.get("title", ""),
            price=fields.get("price", 0.0),
            rating=fields.get("rating", 0.0),
            review_count=fields.get("review_count", 0),
            bullets=fields.get("bullets", ""),
            image_url=fields.get("image_url", ""),
            affiliate_url=fields.get("affiliate_url", ""),
            post_slug=fields.get("post_slug", ""),
            status=fields.get("status", "queued"),
            published_url=fields.get("published_url", ""),
            notes=fields.get("notes", ""),
            record_id=record.get("id"),
            created_time=record.get("createdTime"),
        )


@dataclass
class Distribution:
    """
    Distribution content data model matching the Airtable Distribution table schema.
    """
    post_slug: str = ""
    instagram_caption: str = ""
    tiktok_caption: str = ""
    pinterest_title_1: str = ""
    pinterest_desc_1: str = ""
    pinterest_title_2: str = ""
    pinterest_desc_2: str = ""
    pinterest_title_3: str = ""
    pinterest_desc_3: str = ""
    email_subject: str = ""
    email_blurb: str = ""

    # Airtable metadata
    record_id: Optional[str] = None
    created_time: Optional[str] = None

    def to_airtable_fields(self) -> Dict[str, Any]:
        """Convert to Airtable fields format."""
        return {
            "post_slug": self.post_slug,
            "Instagram_caption": self.instagram_caption,
            "TikTok_caption": self.tiktok_caption,
            "Pinterest_title_1": self.pinterest_title_1,
            "Pinterest_desc_1": self.pinterest_desc_1,
            "Pinterest_title_2": self.pinterest_title_2,
            "Pinterest_desc_2": self.pinterest_desc_2,
            "Pinterest_title_3": self.pinterest_title_3,
            "Pinterest_desc_3": self.pinterest_desc_3,
            "email_subject": self.email_subject,
            "email_blurb": self.email_blurb,
        }

    @classmethod
    def from_airtable_record(cls, record: Dict[str, Any]) -> "Distribution":
        """Create Distribution from Airtable record."""
        fields = record.get("fields", {})
        return cls(
            post_slug=fields.get("post_slug", ""),
            instagram_caption=fields.get("Instagram_caption", ""),
            tiktok_caption=fields.get("TikTok_caption", ""),
            pinterest_title_1=fields.get("Pinterest_title_1", ""),
            pinterest_desc_1=fields.get("Pinterest_desc_1", ""),
            pinterest_title_2=fields.get("Pinterest_title_2", ""),
            pinterest_desc_2=fields.get("Pinterest_desc_2", ""),
            pinterest_title_3=fields.get("Pinterest_title_3", ""),
            pinterest_desc_3=fields.get("Pinterest_desc_3", ""),
            email_subject=fields.get("email_subject", ""),
            email_blurb=fields.get("email_blurb", ""),
            record_id=record.get("id"),
            created_time=record.get("createdTime"),
        )


class AirtableClient:
    """
    Airtable REST API client for Products and Distribution tables.

    Usage:
        config = Config()
        client = AirtableClient(config.airtable)

        # Get all products
        products = client.get_products()

        # Get products by status
        queued = client.get_products(filter_formula="{status}='queued'")

        # Create a new product
        product = Product(keyword="beauty dupes", asin="B07GXQ3KMP", ...)
        record_id = client.create_product(product)

        # Update product status
        client.update_product(record_id, {"status": "published"})
    """

    BASE_URL = "https://api.airtable.com/v0"

    def __init__(self, config: AirtableConfig):
        """
        Initialize Airtable client.

        Args:
            config: AirtableConfig with API key and base ID
        """
        self.config = config
        self.session = requests.Session()
        self.session.headers.update({
            "Authorization": f"Bearer {config.api_key}",
            "Content-Type": "application/json",
        })

    def _get_table_url(self, table_name: str) -> str:
        """Get the full API URL for a table."""
        return f"{self.BASE_URL}/{self.config.base_id}/{table_name}"

    def _handle_response(self, response: requests.Response) -> Dict[str, Any]:
        """Handle API response and raise appropriate errors."""
        if response.status_code == 429:
            # Rate limited - wait and retry
            retry_after = int(response.headers.get("Retry-After", 30))
            print(f"Rate limited. Waiting {retry_after} seconds...")
            time.sleep(retry_after)
            raise Exception("Rate limited - please retry")

        if not response.ok:
            error_msg = f"Airtable API error: {response.status_code}"
            try:
                error_data = response.json()
                error_msg += f" - {error_data.get('error', {}).get('message', response.text)}"
            except json.JSONDecodeError:
                error_msg += f" - {response.text}"
            raise Exception(error_msg)

        return response.json()

    # ===========================================
    # Generic CRUD Operations
    # ===========================================

    def get_records(
        self,
        table: str,
        filter_formula: Optional[str] = None,
        view: Optional[str] = None,
        sort: Optional[List[Dict[str, str]]] = None,
        max_records: Optional[int] = None,
        fields: Optional[List[str]] = None,
    ) -> List[Dict[str, Any]]:
        """
        Get records from a table with optional filtering.

        Args:
            table: Table name
            filter_formula: Airtable filter formula (e.g., "{status}='queued'")
            view: View name to fetch from
            sort: List of sort objects (e.g., [{"field": "created", "direction": "desc"}])
            max_records: Maximum number of records to return
            fields: List of field names to return (returns all if None)

        Returns:
            List of records
        """
        url = self._get_table_url(table)
        all_records = []
        offset = None

        while True:
            params = {}
            if filter_formula:
                params["filterByFormula"] = filter_formula
            if view:
                params["view"] = view
            if sort:
                for i, s in enumerate(sort):
                    params[f"sort[{i}][field]"] = s["field"]
                    params[f"sort[{i}][direction]"] = s.get("direction", "asc")
            if max_records:
                params["maxRecords"] = max_records
            if fields:
                params["fields[]"] = fields
            if offset:
                params["offset"] = offset

            response = self.session.get(url, params=params)
            data = self._handle_response(response)

            all_records.extend(data.get("records", []))

            offset = data.get("offset")
            if not offset:
                break

            # Respect rate limits
            time.sleep(0.2)

        return all_records

    def create_record(self, table: str, fields: Dict[str, Any]) -> str:
        """
        Create a new record in a table.

        Args:
            table: Table name
            fields: Record fields to create

        Returns:
            Created record ID
        """
        url = self._get_table_url(table)
        payload = {"fields": fields}

        response = self.session.post(url, json=payload)
        data = self._handle_response(response)

        return data["id"]

    def create_records(self, table: str, records: List[Dict[str, Any]]) -> List[str]:
        """
        Create multiple records in a table (batch operation).

        Args:
            table: Table name
            records: List of field dictionaries

        Returns:
            List of created record IDs
        """
        url = self._get_table_url(table)
        created_ids = []

        # Airtable allows up to 10 records per batch
        batch_size = 10
        for i in range(0, len(records), batch_size):
            batch = records[i:i + batch_size]
            payload = {"records": [{"fields": r} for r in batch]}

            response = self.session.post(url, json=payload)
            data = self._handle_response(response)

            created_ids.extend([r["id"] for r in data.get("records", [])])
            time.sleep(0.2)  # Rate limit

        return created_ids

    def update_record(self, table: str, record_id: str, fields: Dict[str, Any]) -> Dict[str, Any]:
        """
        Update an existing record.

        Args:
            table: Table name
            record_id: Record ID to update
            fields: Fields to update

        Returns:
            Updated record data
        """
        url = f"{self._get_table_url(table)}/{record_id}"
        payload = {"fields": fields}

        response = self.session.patch(url, json=payload)
        return self._handle_response(response)

    def delete_record(self, table: str, record_id: str) -> bool:
        """
        Delete a record from a table.

        Args:
            table: Table name
            record_id: Record ID to delete

        Returns:
            True if deleted successfully
        """
        url = f"{self._get_table_url(table)}/{record_id}"
        response = self.session.delete(url)
        self._handle_response(response)
        return True

    # ===========================================
    # Products Table Operations
    # ===========================================

    def get_products(
        self,
        filter_formula: Optional[str] = None,
        view: Optional[str] = None,
        status: Optional[str] = None,
        niche: Optional[str] = None,
    ) -> List[Product]:
        """
        Get products from the Products table.

        Args:
            filter_formula: Custom Airtable filter formula
            view: View name to fetch from
            status: Filter by status (queued, ready_to_write, drafted, published, error)
            niche: Filter by niche

        Returns:
            List of Product objects
        """
        # Build filter formula
        filters = []
        if status:
            filters.append(f"{{status}}='{status}'")
        if niche:
            filters.append(f"{{niche}}='{niche}'")
        if filter_formula:
            filters.append(filter_formula)

        final_filter = None
        if filters:
            final_filter = "AND(" + ",".join(filters) + ")" if len(filters) > 1 else filters[0]

        records = self.get_records(
            self.config.products_table,
            filter_formula=final_filter,
            view=view,
        )

        return [Product.from_airtable_record(r) for r in records]

    def create_product(self, product: Product) -> str:
        """
        Create a new product record.

        Args:
            product: Product object to create

        Returns:
            Created record ID
        """
        return self.create_record(
            self.config.products_table,
            product.to_airtable_fields()
        )

    def update_product(self, record_id: str, fields: Dict[str, Any]) -> Dict[str, Any]:
        """
        Update a product record.

        Args:
            record_id: Product record ID
            fields: Fields to update

        Returns:
            Updated record data
        """
        return self.update_record(self.config.products_table, record_id, fields)

    def update_product_status(
        self,
        record_id: str,
        status: str,
        published_url: Optional[str] = None,
        notes: Optional[str] = None,
    ) -> Dict[str, Any]:
        """
        Update product status and optionally set published URL.

        Args:
            record_id: Product record ID
            status: New status
            published_url: URL of published post
            notes: Additional notes

        Returns:
            Updated record data
        """
        fields = {"status": status}
        if published_url:
            fields["published_url"] = published_url
        if notes:
            fields["notes"] = notes
        return self.update_product(record_id, fields)

    def get_products_by_status(self, status: str) -> List[Product]:
        """Get all products with a specific status."""
        return self.get_products(status=status)

    def get_queued_products(self) -> List[Product]:
        """Get all products in the queue."""
        return self.get_products_by_status("queued")

    def get_ready_to_write_products(self) -> List[Product]:
        """Get all products ready for content generation."""
        return self.get_products_by_status("ready_to_write")

    # ===========================================
    # Distribution Table Operations
    # ===========================================

    def get_distributions(
        self,
        filter_formula: Optional[str] = None,
        post_slug: Optional[str] = None,
    ) -> List[Distribution]:
        """
        Get distribution records.

        Args:
            filter_formula: Custom Airtable filter formula
            post_slug: Filter by post slug

        Returns:
            List of Distribution objects
        """
        filters = []
        if post_slug:
            filters.append(f"{{post_slug}}='{post_slug}'")
        if filter_formula:
            filters.append(filter_formula)

        final_filter = None
        if filters:
            final_filter = "AND(" + ",".join(filters) + ")" if len(filters) > 1 else filters[0]

        records = self.get_records(
            self.config.distribution_table,
            filter_formula=final_filter,
        )

        return [Distribution.from_airtable_record(r) for r in records]

    def create_distribution(self, distribution: Distribution) -> str:
        """
        Create a new distribution record.

        Args:
            distribution: Distribution object to create

        Returns:
            Created record ID
        """
        return self.create_record(
            self.config.distribution_table,
            distribution.to_airtable_fields()
        )

    def update_distribution(self, record_id: str, fields: Dict[str, Any]) -> Dict[str, Any]:
        """
        Update a distribution record.

        Args:
            record_id: Distribution record ID
            fields: Fields to update

        Returns:
            Updated record data
        """
        return self.update_record(self.config.distribution_table, record_id, fields)

    def get_distribution_by_slug(self, post_slug: str) -> Optional[Distribution]:
        """Get distribution record for a specific post slug."""
        distributions = self.get_distributions(post_slug=post_slug)
        return distributions[0] if distributions else None


# ===========================================
# Utility Functions
# ===========================================

def create_products_table_schema() -> str:
    """
    Generate the Airtable table schema documentation for Products table.

    Returns:
        Markdown documentation of the schema
    """
    return """
## Products Table Schema

| Field Name | Type | Description |
|------------|------|-------------|
| keyword | Single line text | Search keyword used to find product |
| niche | Single line text | Product niche/category |
| asin | Single line text | Amazon ASIN |
| product_url | URL | Direct Amazon product URL |
| title | Long text | Product title |
| price | Number | Current price |
| rating | Number | Amazon rating (0-5) |
| review_count | Number | Number of reviews |
| bullets | Long text | Product bullet points |
| image_url | URL | Product image URL |
| affiliate_url | URL | Affiliate link with tag |
| post_slug | Single line text | WordPress post slug |
| status | Single select | queued, ready_to_write, drafted, published, error |
| published_url | URL | URL of published blog post |
| notes | Long text | Generated HTML content or notes |
"""


def create_distribution_table_schema() -> str:
    """
    Generate the Airtable table schema documentation for Distribution table.

    Returns:
        Markdown documentation of the schema
    """
    return """
## Distribution Table Schema

| Field Name | Type | Description |
|------------|------|-------------|
| post_slug | Single line text (linked) | Links to Products table |
| Instagram_caption | Long text | Instagram post caption |
| TikTok_caption | Long text | TikTok video caption |
| Pinterest_title_1 | Single line text | First Pinterest pin title |
| Pinterest_desc_1 | Long text | First Pinterest pin description |
| Pinterest_title_2 | Single line text | Second Pinterest pin title |
| Pinterest_desc_2 | Long text | Second Pinterest pin description |
| Pinterest_title_3 | Single line text | Third Pinterest pin title |
| Pinterest_desc_3 | Long text | Third Pinterest pin description |
| email_subject | Single line text | Email newsletter subject line |
| email_blurb | Long text | Email newsletter content blurb |
"""
