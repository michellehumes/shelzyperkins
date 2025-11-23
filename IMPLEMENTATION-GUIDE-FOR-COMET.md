# ShelzyPerkins.com - Step-by-Step Implementation Guide

**For:** Comet (Developer/Implementer)
**Site:** shelzyperkins.com
**Platform:** WordPress on GoDaddy
**Repository:** https://github.com/michellehumes/shelzyperkins

---

## Overview

This guide walks you through setting up shelzyperkins.com, a money-saving/deals blog monetized through Amazon Associates. All content files are in the repository.

**Affiliate Tracking ID:** `shelzysdesigns-20`

---

## Phase 1: WordPress Setup & Theme Installation

### Step 1.1: Access WordPress Admin
1. Log into GoDaddy account
2. Navigate to WordPress admin (shelzyperkins.com/wp-admin)
3. Ensure WordPress is updated to the latest version

### Step 1.2: Install Theme
**Recommended Theme:** Astra (Free) or Kadence (Free)

1. Go to **Appearance → Themes → Add New**
2. Search for "Astra" or "Kadence"
3. Click **Install** then **Activate**
4. Install the companion plugin:
   - Astra: "Starter Templates" plugin
   - Kadence: "Kadence Starter Templates" plugin

### Step 1.3: Import Starter Template (Optional)
1. Go to **Appearance → Starter Templates**
2. Choose a clean blog/lifestyle template as a starting point
3. Import and customize from there

---

## Phase 2: Install Essential Plugins

### Step 2.1: Required Plugins
Go to **Plugins → Add New** and install:

| Plugin | Purpose |
|--------|---------|
| **Yoast SEO** or **RankMath** | SEO optimization |
| **WPForms Lite** | Contact forms |
| **MailerLite** or **Mailchimp for WP** | Email newsletter |
| **Social Warfare** or **Grow Social** | Social sharing buttons |
| **WP Super Cache** or **LiteSpeed Cache** | Site speed |
| **Smush** or **ShortPixel** | Image optimization |
| **Pretty Links** or **ThirstyAffiliates** | Affiliate link management |
| **UpdraftPlus** | Backups |

### Step 2.2: Plugin Configuration
1. **Yoast SEO:** Run the configuration wizard
2. **Cache Plugin:** Enable basic caching
3. **Image Optimizer:** Enable automatic optimization on upload

---

## Phase 3: Create Site Pages

### Step 3.1: Create Required Pages
Go to **Pages → Add New** and create these pages:

| Page | Content File in Repo |
|------|---------------------|
| Home | `/site-content/homepage-content.md` |
| About | `/site-content/about-page-content.md` |
| Blog | (WordPress default - just create empty page) |
| Start Here | `/site-content/other-pages-content.md` (Section 1) |
| Contact | `/site-content/other-pages-content.md` (Section 2) |
| Affiliate Disclosure | `/site-content/other-pages-content.md` (Section 3) |
| Privacy Policy | `/site-content/other-pages-content.md` (Section 4) |

### Step 3.2: Set Up Homepage
1. Go to **Settings → Reading**
2. Select "A static page"
3. Set **Homepage** to your Home page
4. Set **Posts page** to your Blog page

### Step 3.3: Homepage Layout
Using the theme's page builder or Gutenberg blocks, create:

```
┌─────────────────────────────────────────┐
│            HERO SECTION                 │
│  "Welcome to Shelzy Perkins"            │
│  Subheadline + CTA Button               │
├─────────────────────────────────────────┤
│         FEATURED POSTS (3-6)            │
│  [Post] [Post] [Post]                   │
├─────────────────────────────────────────┤
│         CATEGORY SHOWCASE               │
│  Beauty | Home | WFH | Travel | etc.    │
├─────────────────────────────────────────┤
│          ABOUT SNIPPET                  │
│  Photo + Brief intro + Link             │
├─────────────────────────────────────────┤
│        NEWSLETTER SIGNUP                │
│  Email form + Lead magnet mention       │
└─────────────────────────────────────────┘
```

---

## Phase 4: Set Up Blog Categories

### Step 4.1: Create Categories
Go to **Posts → Categories** and create:

| Category Name | Slug |
|---------------|------|
| Beauty & Skincare | beauty |
| Home & Organization | home-organization |
| Work From Home | work-from-home |
| Travel | travel |
| Fitness & Wellness | fitness-wellness |
| Pet Lovers | pets |
| Money Tips | money-tips |
| Seasonal Deals | deals |

---

## Phase 5: Publish Blog Posts

### Step 5.1: Blog Post Files Location
All 6 blog posts are in `/blog-posts/` folder as HTML files.

### Step 5.2: Publish Each Post

**For each blog post:**

1. Go to **Posts → Add New**
2. Copy content from the HTML file (the body content, not the HTML comments)
3. Use the **HTML block** or paste into **Visual editor**
4. Set the following for each post:

---

#### Post 1: Amazon Beauty Must-Haves
- **File:** `01-amazon-beauty-must-haves-under-30.html`
- **Title:** 12 Amazon Beauty Must-Haves Under $30 (2025)
- **Category:** Beauty & Skincare
- **Tags:** amazon finds, beauty, skincare, affordable beauty, beauty must-haves, under $30
- **Meta Description:** Discover 12 viral Amazon beauty must-haves under $30 that actually work! From skincare to makeup tools, these affordable finds are worth every penny.
- **Featured Image:** Flatlay of beauty products on marble background

---

#### Post 2: Amazon Organization Finds
- **File:** `02-viral-amazon-organization-finds.html`
- **Title:** 20 Viral Amazon Organization Finds (2025)
- **Category:** Home & Organization
- **Tags:** amazon finds, organization, home hacks, declutter, storage solutions, viral products
- **Meta Description:** Transform your home with these 20 viral Amazon organization finds! From pantry storage to closet solutions, get organized without breaking the bank.
- **Featured Image:** Organized pantry or closet with clear containers

---

#### Post 3: Work From Home Office
- **File:** `03-amazon-work-from-home-office-must-haves.html`
- **Title:** 15 Amazon Work From Home Office Must-Haves (2025)
- **Category:** Work From Home
- **Tags:** amazon finds, work from home, home office, productivity, WFH essentials, remote work
- **Meta Description:** Upgrade your WFH setup with these 15 Amazon work from home office must-haves! From ergonomic essentials to productivity boosters, work smarter (and comfier).
- **Featured Image:** Styled home office desk setup with plants and accessories

---

#### Post 4: Travel Essentials
- **File:** `04-amazon-travel-essentials.html`
- **Title:** 18 Amazon Travel Essentials (2025)
- **Category:** Travel
- **Tags:** amazon finds, travel essentials, packing tips, travel must-haves, vacation, travel hacks
- **Meta Description:** Pack like a pro with these 18 Amazon travel essentials! From packing cubes to in-flight comfort, these must-haves will upgrade every trip.
- **Featured Image:** Flatlay of suitcase with organized travel items

---

#### Post 5: Fitness & Wellness
- **File:** `05-amazon-fitness-wellness-finds.html`
- **Title:** 15 Amazon Fitness & Wellness Finds (2025)
- **Category:** Fitness & Wellness
- **Tags:** amazon finds, fitness, wellness, workout essentials, health, self-care
- **Meta Description:** Level up your fitness routine with these 15 Amazon fitness and wellness finds! From home gym essentials to recovery tools, get fit without the expensive gym membership.
- **Featured Image:** Yoga mat with dumbbells, water bottle, and fitness accessories

---

#### Post 6: Dog Mom Essentials
- **File:** `06-amazon-dog-mom-essentials.html`
- **Title:** 15 Amazon Dog Mom Essentials (2025)
- **Category:** Pet Lovers
- **Tags:** amazon finds, dog mom, pet essentials, dog products, pet lovers, fur baby
- **Meta Description:** Spoil your fur baby with these 15 Amazon dog mom essentials! From practical must-haves to adorable accessories, everything you need for your pup.
- **Featured Image:** Happy dog with owner and cute pet products

---

### Step 5.3: Featured Images
- Source royalty-free images from Unsplash, Pexels, or Canva
- Recommended size: 1200x628px (optimal for social sharing)
- Optimize images before uploading (compress to under 200KB)

---

## Phase 6: Navigation & Menus

### Step 6.1: Create Main Menu
Go to **Appearance → Menus**

1. Create new menu called "Main Navigation"
2. Add items in this order:

```
Home
Blog
Categories ▼
  ├── Beauty & Skincare
  ├── Home & Organization
  ├── Work From Home
  ├── Travel
  ├── Fitness & Wellness
  └── Pet Lovers
Start Here
About
Contact
```

3. Set as "Primary Menu" location

### Step 6.2: Create Footer Menu
Create another menu called "Footer Menu" with:
- Privacy Policy
- Affiliate Disclosure
- Contact

---

## Phase 7: Widgets & Sidebar

### Step 7.1: Configure Sidebar
Go to **Appearance → Widgets**

Add to Blog Sidebar (in order):
1. **About Widget** - Photo + brief bio (2-3 sentences)
2. **Search** - Search box
3. **Popular Posts** - Or "Recent Posts" widget
4. **Categories** - List of blog categories
5. **Newsletter Signup** - Email signup form (from MailerLite/Mailchimp)
6. **Social Icons** - Links to Pinterest, Instagram, TikTok

### Step 7.2: Configure Footer Widgets
Set up 3-4 footer columns:
- Column 1: About text
- Column 2: Quick links
- Column 3: Categories
- Column 4: Newsletter signup

---

## Phase 8: Email Marketing Setup

### Step 8.1: Set Up Email Provider
**Recommended:** MailerLite (free up to 1,000 subscribers)

1. Create MailerLite account at mailerlite.com
2. Verify domain (shelzyperkins.com)
3. Create subscriber group called "Newsletter"

### Step 8.2: Create Signup Forms
1. Create embedded form in MailerLite
2. Copy embed code
3. Add to:
   - Homepage (newsletter section)
   - Sidebar widget
   - Footer
   - End of blog posts (optional)

### Step 8.3: Create Welcome Email
Set up automation for new subscribers:
1. Welcome email with lead magnet (if created)
2. Introduction email
3. Popular posts email

---

## Phase 9: SEO Configuration

### Step 9.1: Yoast SEO Settings
1. Go to **Yoast SEO → Settings**
2. Run configuration wizard
3. Set site title: "Shelzy Perkins"
4. Set tagline: "Smart Savings, Simple Living"

### Step 9.2: Configure Each Page/Post
For every page and post:
1. Set focus keyphrase
2. Write custom meta description
3. Ensure green "SEO score" light

### Step 9.3: Submit to Google
1. Go to Google Search Console
2. Add and verify shelzyperkins.com
3. Submit sitemap (yourdomain.com/sitemap_index.xml)

---

## Phase 10: Analytics Setup

### Step 10.1: Google Analytics 4
1. Create GA4 property at analytics.google.com
2. Get Measurement ID (G-XXXXXXXX)
3. Install using:
   - MonsterInsights plugin (easiest), OR
   - Add code to theme header

### Step 10.2: Pinterest Tag (Optional)
If using Pinterest for marketing:
1. Create Pinterest Business account
2. Install Pinterest tag for conversion tracking

---

## Phase 11: Final Checklist

### Technical Checks
- [ ] Site loads quickly (test with GTmetrix or PageSpeed Insights)
- [ ] Mobile responsive (test on phone)
- [ ] SSL certificate active (https://)
- [ ] All links working (no 404s)
- [ ] Forms submitting correctly

### Content Checks
- [ ] All 6 blog posts published
- [ ] All pages have content
- [ ] Featured images on all posts
- [ ] Affiliate disclosure visible
- [ ] Privacy policy linked in footer

### SEO Checks
- [ ] Meta descriptions on all pages
- [ ] Sitemap submitted to Google
- [ ] Alt text on images
- [ ] Internal links between related posts

### Legal/Compliance
- [ ] Affiliate disclosure page exists and is linked
- [ ] Privacy policy includes cookie notice
- [ ] Amazon Associates badge visible (optional but recommended)

---

## Phase 12: Post-Launch Tasks

### Immediate (Week 1)
- [ ] Test all contact forms
- [ ] Check site on multiple devices/browsers
- [ ] Set up automatic backups
- [ ] Share first posts on social media

### Ongoing
- [ ] Publish 1-2 new posts per week
- [ ] Create Pinterest pins for each post
- [ ] Monitor Google Analytics
- [ ] Respond to comments
- [ ] Update posts annually with fresh products

---

## File Reference

| File | Purpose |
|------|---------|
| `SITE-RECOMMENDATIONS.md` | Complete brand/strategy guide |
| `blog-posts/*.html` | 6 blog posts ready to publish |
| `site-content/homepage-content.md` | Homepage copy |
| `site-content/about-page-content.md` | About page copy |
| `site-content/other-pages-content.md` | All other page copy |

---

## Brand Quick Reference

**Colors (suggestions):**
- Primary: Soft sage green (#9CAF88) or dusty rose (#D4A5A5)
- Secondary: Warm cream (#F5F5DC)
- Accent: Gold (#D4AF37)
- Text: Charcoal (#36454F)

**Fonts:**
- Headlines: Playfair Display or DM Serif Display
- Body: Open Sans or Nunito

**Tagline Options:**
- "Smart Savings, Simple Living"
- "Your Guide to Smarter Spending"

---

## Support & Questions

For questions about:
- **Content:** Contact site owner
- **Technical WordPress:** GoDaddy support or WordPress documentation
- **Affiliate links:** All use tracking ID `shelzysdesigns-20`

---

*Document created: January 2025*
