# ShelzyPerkins Shortcode Quick Reference

Use these shortcodes in your WordPress posts to add affiliate links, bounty CTAs, and other revenue-generating components.

---

## Basic Product Shortcodes

### Simple Amazon Link
```
[amazon_link asin="B08N5WRWNW" text="Buy Now"]
```
Creates a simple button link to Amazon product.

### Product Card
```
[product_card asin="B08N5WRWNW" title="Product Name" price="29.99" original_price="49.99" image="IMAGE_URL" badge="% OFF"]
```
Creates a visual product card with image, price, and CTA.

### Enhanced Product Card (with Price History)
```
[product_card_enhanced
  asin="B08N5WRWNW"
  title="Product Name"
  price="29.99"
  original_price="49.99"
  lowest_price="24.99"
  highest_price="59.99"
  rating="4.5"
  reviews="1234"
  image="IMAGE_URL"
  badge="Near Lowest!"]
```
Shows price history indicator: "Near Lowest!", "Good Price", "Average", or "Wait for Drop"

---

## Comparison Shortcodes

### Comparison Table
```
[comparison_table]
[product_row asin="B08N5WRWNW" title="Product 1" price="29.99" note="Best Value" image="URL"]
[product_row asin="B07XYZ1234" title="Product 2" price="39.99" note="Premium Pick" image="URL"]
[/comparison_table]
```

---

## Bounty Program Shortcodes (Flat-Rate Commissions!)

### Bounty Card
```
[bounty_card program="prime"]
[bounty_card program="audible"]
[bounty_card program="kindle_unlimited"]
[bounty_card program="amazon_music"]
[bounty_card program="amazon_fresh"]
[bounty_card program="amazon_business"]
```
**Styles:**
- `style="card"` - Full card (default)
- `style="banner"` - Horizontal banner
- `style="inline"` - Inline button

### Bounty Grid (All Programs)
```
[bounty_grid columns="3"]
[bounty_grid columns="2" exclude="amazon_fresh,amazon_business"]
```

### Bounty Commission Rates:
| Program | Bounty |
|---------|--------|
| Prime | $3.00 |
| Audible | $5.00 |
| Kindle Unlimited | $3.00 |
| Amazon Music | $3.00 |
| Amazon Fresh | $3.00 |
| Amazon Business | $15.00 |

---

## CTA Shortcodes

### Quick Buy (Above-the-Fold)
```
[quick_buy asin="B08N5WRWNW" title="Our Top Pick" price="29.99" badge="Editor's Choice"]
```
High-visibility CTA for the top of posts.

### Deal Alert (with Countdown)
```
[deal_alert title="Flash Sale!" type="lightning" expires="2024-12-31" asin="B08N5WRWNW" description="Limited time only!"]
```
**Types:** `default`, `flash`, `lightning`, `prime`

### Seasonal CTA
```
[seasonal_cta season="christmas"]
[seasonal_cta season="black_friday"]
[seasonal_cta season="prime_day"]
[seasonal_cta season="holiday" category="tech"]
```
**Seasons:** `christmas`, `holiday`, `valentines`, `mothers_day`, `fathers_day`, `back_to_school`, `summer`, `black_friday`, `prime_day`

---

## Content Shortcodes

### Affiliate Disclosure
```
[affiliate_disclosure]
[affiliate_disclosure short="true"]
```

### Email Signup
```
[email_signup title="Never Miss a Deal!" description="Get deals in your inbox"]
```

### Deal Badge
```
[deal_badge text="HOT DEAL" type="hot"]
```
**Types:** `default`, `hot`, `limited`

---

## Best Practices for Maximum Revenue

### Post Structure Template
```markdown
[affiliate_disclosure]

[quick_buy asin="..." title="Top Pick" price="..." badge="Editor's Choice"]

## Introduction paragraph...

### Product 1
[product_card_enhanced asin="..." ...]

### Product 2
[product_card_enhanced asin="..." ...]

## Comparison
[comparison_table]
[product_row ...]
[product_row ...]
[/comparison_table]

## Bounty CTA (inserted automatically, or add manually)
[bounty_card program="prime" style="banner"]

[seasonal_cta season="holiday"]

[email_signup ...]
```

### Placement Tips
1. **Quick Buy** - Always above the fold
2. **Product Cards** - After each product review section
3. **Comparison Table** - Near the end for scanners
4. **Bounty Banners** - After 3rd and 6th paragraphs (auto-inserted)
5. **Seasonal CTAs** - Before email signup
6. **Email Signup** - End of every post

### High-Converting Content Types
1. **"Best X for Y"** - "Best laptop for students"
2. **"X vs Y"** - Comparison posts
3. **"X Under $Y"** - Budget roundups
4. **"X Dupes"** - High-end alternatives
5. **"X Review"** - Single product deep-dives

---

## Auto-Inserted CTAs

These are automatically added to long posts (5+ paragraphs):
- Prime bounty banner after 3rd paragraph
- Audible bounty banner after 6th paragraph

To disable for a specific post, add to post content:
```
<!-- disable-auto-ctas -->
```

---

## Testing Your Shortcodes

Preview your post before publishing to ensure:
- [ ] All product cards render correctly
- [ ] Prices are current
- [ ] Images load properly
- [ ] Links include your affiliate tag (shelzysdesigns-20)
- [ ] Mobile layout looks good

---

## Need Help?

Check the comparison post template: `/blog-posts/comparison-post-template.md`
