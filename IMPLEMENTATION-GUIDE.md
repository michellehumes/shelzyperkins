# ShelzyPerkins.com Implementation Guide

## Site Overview

**Domain:** shelzyperkins.com
**Niche:** Deals, savings, Amazon affiliate recommendations, money-saving guides
**Target Audience:** Busy women seeking curated, trustworthy product recommendations
**Affiliate Tag:** `shelzysdesigns-20`

---

## ✅ What's Been Built

### Pages Created (10 pages)
- `/` - Homepage
- `/shop/` - Shop My Amazon Finds
- `/deals/` - Deals of the Day
- `/best-of/` - Best-of Lists Hub
- `/budget-hacks/` - Budget Hacks & Money Tips
- `/beauty-deals/` - Beauty Deals
- `/home-kitchen-deals/` - Home & Kitchen Deals
- `/fashion-deals/` - Fashion Deals
- `/tech-travel-deals/` - Tech & Travel Deals
- `/gift-guides/` - Gift Guides Hub
- `/about-shelzy/` - About Page

### Categories Created
- Deals, Lightning Deals, Under $20 Finds
- Beauty Deals, Home Deals, Fashion Deals, Tech Deals
- Best-of Lists, Budget Hacks, Gift Guides, Quick Picks
- TikTok Trending, Home & Kitchen, Fashion, Tech & Travel
- Wedding, Money Tips

### Posts Created (70+ posts with affiliate links)
- 20 Pillar Posts (Best-of Lists, Guides)
- 30+ Daily Deals Posts
- 20+ Seasonal Gift Guide Posts
- 30+ Quick Picks Posts

### Navigation
- Full mega-menu with dropdowns
- Home | Shop Amazon | Deals (dropdown) | Best-of | Hacks (dropdown) | Gift Guides (dropdown) | About

---

## 🔧 Manual Setup Required

### 1. Theme Installation
**Recommended themes (lightweight, SEO-optimized):**
- Kadence (Free/Pro)
- Astra (Free/Pro)
- Blocksy (Free/Pro)

**Steps:**
1. WordPress Admin → Appearance → Themes → Add New
2. Search for "Kadence" or "Astra"
3. Install & Activate
4. Run theme wizard for basic setup

### 2. Essential Plugins to Install

| Plugin | Purpose | Priority |
|--------|---------|----------|
| RankMath SEO | SEO optimization, schema | High |
| WP Rocket or LiteSpeed Cache | Speed optimization | High |
| ShortPixel | Image compression | High |
| WPForms Lite | Contact forms | Medium |
| UpdraftPlus | Backups | High |
| Wordfence | Security | Medium |
| MonsterInsights | Analytics | Medium |

### 3. SEO Configuration (RankMath)

After installing RankMath:

1. **General Settings:**
   - Enable breadcrumbs
   - Enable schema markup
   - Set homepage title: "ShelzyPerkins - Best Amazon Deals & Money-Saving Finds"
   - Set homepage description: "Your trusted source for curated Amazon deals, money-saving tips, and honest product reviews. Shop smarter, save more."

2. **Schema Setup:**
   - Set default schema type: "Article"
   - For product posts: Use "Product Review" schema

3. **Sitemap:**
   - Enable XML sitemap
   - Submit to Google Search Console

### 4. Speed Optimization

1. **Install caching plugin** (WP Rocket or LiteSpeed Cache)
2. **Enable:**
   - Page caching
   - Browser caching
   - CSS/JS minification
   - Lazy loading for images

3. **Image optimization:**
   - Install ShortPixel
   - Compress all existing images
   - Enable auto-optimization for uploads

### 5. Analytics Setup

1. **Google Analytics 4:**
   - Create GA4 property
   - Install MonsterInsights plugin
   - Connect GA4

2. **Google Search Console:**
   - Verify site ownership
   - Submit sitemap

3. **Amazon Associates:**
   - Ensure affiliate tag `shelzysdesigns-20` is in all links
   - Check Associates Central for conversion reports

---

## 📧 Email Marketing Setup

### Recommended Platforms
- **ConvertKit** (best for creators)
- **Mailchimp** (free tier)
- **Klaviyo** (e-commerce focused)

### Email Sequences to Build

1. **Welcome Series (5 emails):**
   - Email 1: Welcome + best posts
   - Email 2: About me + shopping philosophy
   - Email 3: Top 10 Amazon finds
   - Email 4: Budget hacks guide
   - Email 5: Invite to follow socials

2. **Daily Deals Digest:**
   - Automated daily send
   - Feature 5-10 best deals
   - Include affiliate links

3. **Weekly Roundup:**
   - Best posts of the week
   - Trending products
   - Money-saving tip

---

## 📌 Pinterest Automation

### Setup Steps

1. **Create Business Account:**
   - Convert to Pinterest Business
   - Claim website
   - Enable Rich Pins

2. **Pin Template Design:**
   - Use Canva for vertical pins (1000x1500px)
   - Brand colors: sage green (#9CAF88), white, charcoal
   - Font: Montserrat (headings), Inter (body)

3. **Automation Options:**
   - **Tailwind** - Schedule pins, SmartLoop, analytics
   - **Planoly** - Visual planning, auto-posting
   - **Later** - Multi-platform scheduling

4. **Pin Strategy:**
   - Create 3-5 pins per blog post
   - Pin daily (10-25 pins)
   - Use keyword-rich descriptions
   - Link each pin to relevant post

### Pinterest SEO Keywords
- "Amazon finds"
- "Amazon must haves"
- "Best Amazon products"
- "Amazon home finds"
- "TikTok Amazon finds"
- "Amazon beauty finds"
- "[Season] Amazon finds"

---

## 🤖 Content Automation Workflows

### Daily Deals Automation

**Option 1: Manual Daily Post**
1. Use `/templates/daily-deals-template.html`
2. Update with current deals each morning
3. Change date in title/slug
4. Publish at optimal time (7-9 AM)

**Option 2: Semi-Automated with IFTTT/Zapier**
1. Monitor Amazon deal feeds via API/RSS
2. Trigger notification when deals match criteria
3. Manual review and post

**Option 3: WordPress Automation Plugins**
- **Auto Post Scheduler** - Schedule posts in advance
- **PublishPress Future** - Auto-expire time-sensitive content

### Content Refresh Workflow

**Monthly Tasks:**
1. Update prices in top posts
2. Replace expired/out-of-stock products
3. Refresh outdated screenshots
4. Check for broken affiliate links

**Tools for Link Checking:**
- Broken Link Checker plugin
- Ahrefs (paid)
- Screaming Frog (free up to 500 URLs)

---

## 💰 Monetization Optimization

### Maximize Amazon Affiliate Revenue

1. **Link Placement:**
   - Multiple CTAs per post (top, middle, end)
   - Use buttons, not just text links
   - Add comparison tables

2. **Product Selection:**
   - Focus on $20-$100 price range (sweet spot)
   - Prioritize Prime-eligible products
   - Check commission rates by category

3. **Conversion Boosters:**
   - Add "Similar items" sections
   - Create bundle recommendations
   - Include "Save more" alternatives

### Additional Revenue Streams

1. **Display Ads:**
   - Apply to Mediavine (50k sessions/month)
   - Or Ezoic (10k sessions/month)
   - Or Google AdSense (any traffic)

2. **Sponsored Content:**
   - Create media kit
   - Reach out to brands in your niche
   - Price: $100-500+ per sponsored post

3. **Digital Products:**
   - Printable shopping lists
   - Budget trackers
   - Curated deal guides (PDF)

---

## 📊 Tracking & Analytics

### Key Metrics to Monitor

| Metric | Tool | Goal |
|--------|------|------|
| Traffic | GA4 | Growth month-over-month |
| Affiliate Clicks | Amazon Associates | 2-5% click rate |
| Conversions | Amazon Associates | 5-10% conversion |
| Email Subscribers | Email platform | 1-3% of visitors |
| Pinterest Traffic | Pinterest Analytics | 20%+ of referrals |

### Monthly Reporting

Create a simple spreadsheet tracking:
- Total pageviews
- Affiliate revenue
- Top performing posts
- New email subscribers
- Pinterest impressions
- Search rankings for target keywords

---

## 🚀 Growth Checklist

### Week 1-2: Foundation
- [ ] Choose and install theme
- [ ] Install essential plugins
- [ ] Configure SEO settings
- [ ] Set up analytics
- [ ] Create email opt-in form

### Week 3-4: Content
- [ ] Review all posts for quality
- [ ] Add internal links between posts
- [ ] Create 10 Pinterest pins
- [ ] Set up Pinterest scheduling

### Month 2: Scale
- [ ] Publish 2-3 new posts weekly
- [ ] Launch welcome email sequence
- [ ] Apply to additional affiliate programs
- [ ] Guest post on related blogs

### Month 3+: Optimize
- [ ] A/B test headlines
- [ ] Optimize top posts for conversions
- [ ] Expand to Instagram/TikTok
- [ ] Create first digital product

---

## 📁 File Structure

```
/shelzyperkins/
├── templates/
│   ├── deal-roundup-template.html
│   ├── top-10-list-template.html
│   ├── gift-guide-template.html
│   ├── quick-picks-template.html
│   ├── comparison-template.html
│   └── daily-deals-template.html
├── marketing-assets/
│   ├── pinterest/
│   ├── instagram/
│   ├── email/
│   └── homepage/
├── wordpress-content/
│   └── [archived content files]
├── IMPLEMENTATION-GUIDE.md (this file)
└── README.md
```

---

## 🎨 Brand Guidelines

### Colors
- **Primary:** Sage Green `#9CAF88`
- **Secondary:** Soft Beige `#F5F5DC`
- **Accent:** Amazon Orange `#FF9900`
- **Text:** Charcoal `#333333`
- **Background:** White `#FFFFFF`

### Typography
- **Headings:** Montserrat (Bold)
- **Body:** Inter or Lato
- **Buttons:** Montserrat (Semi-Bold)

### Button Styles
- Amazon CTAs: Orange `#FF9900`, white text, rounded corners
- Secondary: Sage green background or outline style

### Image Style
- Clean, bright product photography
- Collage-style roundups
- Minimal text overlay on images

---

## Support & Resources

- **WordPress Support:** wordpress.org/support
- **Amazon Associates Help:** affiliate-program.amazon.com
- **Theme Documentation:** Check specific theme docs
- **SEO Learning:** Moz.com, Ahrefs blog

---

*Last Updated: November 2025*
