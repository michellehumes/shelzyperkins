# ShelzyPerkins Affiliate Content Automation System

A complete end-to-end automation system for affiliate content creation and publishing.

## Features

- **Product Discovery**: Find trending Amazon products by niche/keyword
- **Airtable Integration**: Store and manage product data
- **Content Generation**: SEO-optimized blog posts with schema markup
- **WordPress Publishing**: Automatic post creation via REST API
- **Social Media Content**: Instagram, TikTok, and Pinterest captions
- **Email Marketing**: Newsletter content generation

## Quick Start

### 1. Setup Environment

```bash
# Navigate to automation directory
cd automation

# Copy the environment template
cp .env.template .env

# Edit .env with your credentials
nano .env
```

### 2. Install Dependencies

```bash
pip install -r affiliate_system/requirements.txt
```

### 3. Run the Pipeline

```bash
# Generate a single post
python run_pipeline.py --title "Best Amazon Beauty Dupes" --keyword "beauty dupes" --niche beauty

# Generate daily deals post
python run_pipeline.py --daily-deals

# Test API connections
python run_pipeline.py --test
```

## Configuration

### Required Environment Variables

| Variable | Description |
|----------|-------------|
| `AIRTABLE_API_KEY` | Your Airtable API key |
| `AIRTABLE_BASE_ID` | Airtable base ID (appXXXXX) |
| `WP_URL` | WordPress site URL |
| `WP_USERNAME` | WordPress username |
| `WP_APP_PASSWORD` | WordPress application password |

### Optional Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `AMAZON_ASSOC_TAG` | Amazon Associates tag | shelzysdesigns-20 |
| `AIRTABLE_PRODUCTS_TABLE_NAME` | Products table name | Products |
| `AIRTABLE_DISTRIBUTION_TABLE_NAME` | Distribution table name | Distribution |

## Airtable Setup

### Products Table Schema

Create a table called "Products" with these fields:

| Field | Type |
|-------|------|
| keyword | Single line text |
| niche | Single line text |
| asin | Single line text |
| product_url | URL |
| title | Long text |
| price | Number |
| rating | Number |
| review_count | Number |
| bullets | Long text |
| image_url | URL |
| affiliate_url | URL |
| post_slug | Single line text |
| status | Single select (queued, ready_to_write, drafted, published, error) |
| published_url | URL |
| notes | Long text |

### Distribution Table Schema

Create a table called "Distribution" with these fields:

| Field | Type |
|-------|------|
| post_slug | Single line text |
| Instagram_caption | Long text |
| TikTok_caption | Long text |
| Pinterest_title_1 | Single line text |
| Pinterest_desc_1 | Long text |
| Pinterest_title_2 | Single line text |
| Pinterest_desc_2 | Long text |
| Pinterest_title_3 | Single line text |
| Pinterest_desc_3 | Long text |
| email_subject | Single line text |
| email_blurb | Long text |

## Usage Examples

### Command Line

```bash
# Single post with all options
python run_pipeline.py \
  --title "10 Best Kitchen Gadgets" \
  --keyword "kitchen gadgets" \
  --niche kitchen \
  --products 10 \
  --draft

# Batch processing
python run_pipeline.py --batch

# Process queued Airtable products
python run_pipeline.py --process-queued

# Show configuration status
python run_pipeline.py --config
```

### Python API

```python
from affiliate_system import Config, AffiliatePipeline

# Initialize
config = Config(env_file=".env")
pipeline = AffiliatePipeline(config)

# Run full pipeline
result = pipeline.run_full_pipeline(
    keyword="beauty dupes",
    niche="beauty",
    title="Best Amazon Beauty Dupes",
    product_count=10,
)

if result.success:
    print(f"Published: {result.post_url}")
```

### Individual Components

```python
from affiliate_system import (
    Config,
    AirtableClient,
    WordPressClient,
    ProductDiscovery,
    ContentGenerator,
    DistributionGenerator,
)

config = Config(env_file=".env")

# Use Airtable client
airtable = AirtableClient(config.airtable)
products = airtable.get_products(status="queued")

# Use WordPress client
wp = WordPressClient(config.wordpress)
post_url = wp.create_post(title="...", content="...", slug="...")

# Use product discovery
discovery = ProductDiscovery(config.amazon)
trending = discovery.get_trending_products("beauty", max_results=10)

# Use content generator
content_gen = ContentGenerator(config.content, config.niches)
post = content_gen.generate_roundup_post(products, "Title", "niche")

# Use distribution generator
dist_gen = DistributionGenerator(config.niches)
content = dist_gen.generate_all("slug", "title", "url", "niche", products)
```

## Available Niches

- beauty
- home
- kitchen
- tech
- fashion
- fitness
- baby
- pets

## File Structure

```
automation/
├── affiliate_system/
│   ├── __init__.py           # Package exports
│   ├── config.py             # Configuration management
│   ├── airtable_client.py    # Airtable CRUD operations
│   ├── wordpress_client.py   # WordPress REST API client
│   ├── product_discovery.py  # Amazon product discovery
│   ├── content_generator.py  # SEO content generation
│   ├── distribution_generator.py  # Social/email content
│   ├── pipeline.py           # Main orchestration
│   └── requirements.txt      # Python dependencies
├── run_pipeline.py           # CLI entry point
├── .env.template             # Environment template
└── README.md                 # This file
```

## Generated Content

### Blog Posts Include:
- SEO-optimized title and meta description
- Schema.org JSON-LD markup (Article, ItemList, Product)
- Affiliate disclosure
- Table of contents
- Product comparison table
- Individual product sections with images, pricing, CTAs
- FAQ section
- Email signup CTA

### Distribution Content Includes:
- Instagram caption with hashtags
- TikTok caption
- 3 Pinterest pin variations (titles + descriptions)
- Email subject line and newsletter blurb

## Troubleshooting

### Common Issues

1. **"Airtable API error: 401"**
   - Check your AIRTABLE_API_KEY is correct
   - Ensure the key has access to the base

2. **"WordPress API error: 401"**
   - Verify WP_USERNAME and WP_APP_PASSWORD
   - Application passwords are different from regular passwords

3. **"No products found"**
   - The curated product lists are used by default
   - Add products manually or configure Amazon PA-API

### Debug Mode

```bash
# Check configuration
python run_pipeline.py --config

# Test connections
python run_pipeline.py --test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is part of the ShelzyPerkins affiliate site.
