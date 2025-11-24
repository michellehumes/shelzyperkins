# ShelzyPerkins.com - Amazon Affiliate Deals Site

Complete WordPress theme, content, and automation system for ShelzyPerkins.com - a deals and Amazon affiliate site.

## Project Structure

```
shelzyperkins/
├── branding/                    # Brand assets
│   ├── logo.svg                 # Main logo
│   ├── favicon.svg              # Favicon
│   ├── brand-guidelines.md      # Complete brand guide
│   ├── colors.json              # Color palette
│   └── typography.css           # Typography system
│
├── wordpress-theme/             # WordPress Theme
│   └── shelzyperkins-theme/
│       ├── style.css            # Main stylesheet
│       ├── functions.php        # Theme functions & shortcodes
│       ├── header.php           # Site header
│       ├── footer.php           # Site footer
│       ├── index.php            # Blog index
│       ├── front-page.php       # Homepage
│       ├── single.php           # Single post
│       ├── page.php             # Single page
│       ├── sidebar.php          # Sidebar widgets
│       ├── templates/           # Page templates
│       ├── template-parts/      # Reusable components
│       ├── inc/                 # PHP includes
│       └── assets/              # CSS & JS
│
├── pages/                       # Static page content
├── blog-posts/                  # 10 ready-to-publish posts
├── automation/                  # Pinterest, deals, email automation
├── analytics/                   # GA4 setup & tracking
├── seo/                         # SEO configuration
├── config/                      # Plugin & speed configs
└── docs/                        # Content calendar (50 posts)
```

## Quick Start

### 1. Install Theme
Upload `wordpress-theme/shelzyperkins-theme` to `wp-content/themes/` and activate.

### 2. Install Plugins
- Rank Math SEO
- LiteSpeed Cache
- ShortPixel Image Optimizer
- WPForms Lite
- Mailchimp for WordPress

### 3. Import Content
Create pages and posts from the `pages/` and `blog-posts/` directories.

## Amazon Affiliate Integration

**Affiliate Tag:** `shelzysdesigns-20`

### Shortcodes
```
[amazon_link asin="B08N5WRWNW" text="Buy Now"]
[product_card asin="..." title="..." price="..." original_price="..."]
[comparison_table]...[/comparison_table]
[affiliate_disclosure]
[email_signup title="..." description="..."]
```

## Brand Colors

| Color | Hex | Usage |
|-------|-----|-------|
| Coral Red | #FF6B6B | Primary |
| Sunny Yellow | #FFE66D | Accents |
| Charcoal | #2D3436 | Text |
| Amazon Orange | #FF9900 | Amazon CTAs |

## Performance Targets

- PageSpeed: 90+
- LCP: < 2.5s
- Mobile: Fully responsive

---

**Affiliate Tag:** shelzysdesigns-20
