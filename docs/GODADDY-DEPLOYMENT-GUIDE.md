# ShelzyPerkins.com - GoDaddy Deployment Guide

## Your Login Info
- **GoDaddy SSO:** https://sso.godaddy.com/?app=www
- **Email:** michelle.e.humes@gmail.com
- **WordPress Admin:** shelzyperkins.com/wp-admin

---

## STEP 1: Verify WordPress is Installed

1. Go to https://shelzyperkins.com/wp-admin
2. If you see a WordPress login screen, WordPress is already installed
3. If you see an error, we need to install WordPress first (see Step 1B below)

### Step 1B: Install WordPress (if needed)
1. Log into GoDaddy: https://sso.godaddy.com
2. Go to **My Products** → Find your hosting
3. Click **Manage** next to your hosting
4. Look for **WordPress** or **One-Click Install**
5. Click **Install WordPress**
6. Set your admin username/password (save these!)
7. Wait 5-10 minutes for installation

---

## STEP 2: Install the Theme

### Option A: Upload via WordPress Admin (Easiest)
1. Go to shelzyperkins.com/wp-admin
2. Navigate to **Appearance → Themes**
3. Click **Add New** → **Upload Theme**
4. Upload the file: `shelzyperkins-theme.zip` (I'll create this)
5. Click **Install Now**
6. Click **Activate**

### Option B: Upload via File Manager
1. Log into GoDaddy → **My Products** → **Manage Hosting**
2. Open **File Manager** (or cPanel)
3. Navigate to: `public_html/wp-content/themes/`
4. Upload and extract `shelzyperkins-theme.zip`
5. Go to WordPress Admin → Appearance → Themes
6. Activate "ShelzyPerkins"

---

## STEP 3: Install Required Plugins

Go to **Plugins → Add New** and install these:

### Essential (Install First)
1. **Rank Math SEO** - Search for "Rank Math", Install, Activate
2. **LiteSpeed Cache** OR **WP Super Cache** - For speed
3. **ShortPixel Image Optimizer** - Compress images
4. **UpdraftPlus** - Backups

### Recommended
5. **WPForms Lite** - Contact form
6. **Mailchimp for WP** - Email signups
7. **MonsterInsights** - Google Analytics

---

## STEP 4: Create Pages

Go to **Pages → Add New** for each page:

### 4.1 About Page
- Title: About
- Slug: about
- Copy content from: `pages/about.html`
- Publish

### 4.2 Contact Page
- Title: Contact
- Slug: contact
- Copy content from: `pages/contact.html`
- Publish

### 4.3 Privacy Policy
- Title: Privacy Policy
- Slug: privacy-policy
- Copy content from: `pages/privacy-policy.html`
- Publish

### 4.4 Affiliate Disclosure
- Title: Affiliate Disclosure
- Slug: affiliate-disclosure
- Copy content from: `pages/affiliate-disclosure.html`
- Publish

---

## STEP 5: Create Categories

Go to **Posts → Categories** and create:

| Name | Slug | Description |
|------|------|-------------|
| Deals | deals | Daily deals and sales |
| Beauty | beauty | Beauty and skincare finds |
| Home | home | Home decor and organization |
| Tech | tech | Tech gadgets and electronics |
| Fashion | fashion | Clothing and accessories |
| Kitchen | kitchen | Kitchen gadgets and tools |
| Gift Guides | gift-guides | Gift ideas for every occasion |
| Budget Hacks | budget-hacks | Money-saving tips |

---

## STEP 6: Import Blog Posts

Go to **Posts → Add New** for each post in the `blog-posts/` folder:

1. Copy the title (first line after #)
2. Paste the content (everything below the metadata)
3. Set the category
4. Add featured image (any relevant stock photo)
5. Publish

**Post order:**
1. Best Amazon Beauty Dupes (Category: Beauty)
2. Home Upgrades Under $25 (Category: Home)
3. TikTok Viral Deals (Category: Deals)
4. Travel Essentials (Category: Travel, Best-Of)
5. Kitchen Items That Look High-End (Category: Kitchen)
6. Tech Under $50 (Category: Tech)
7. Fashion Finds Under $30 (Category: Fashion)
8. Holiday Gifts Under $100 (Category: Gift Guides)
9. Daily Deals Template (Category: Deals)
10. Money Saving Products (Category: Budget Hacks)

---

## STEP 7: Set Up Menus

Go to **Appearance → Menus**

### Primary Menu
Create menu called "Primary" with these items:
- Home (link to homepage)
- Deals (category: deals)
- Best Of (create a page or use category)
- Gift Guides (category: gift-guides)
- About (page)

Check "Primary Menu" location → Save

### Footer Menu
Create menu called "Footer" with:
- About
- Contact
- Privacy Policy
- Affiliate Disclosure

Check "Footer Menu" location → Save

---

## STEP 8: Configure Homepage

Go to **Settings → Reading**
- Select "A static page"
- Homepage: (leave blank or create one)
- Posts page: Create a page called "Blog"
- Save

OR use the theme's built-in front page:
- The theme's `front-page.php` will automatically display

---

## STEP 9: Set Up Google Analytics

1. Go to https://analytics.google.com
2. Create account/property for shelzyperkins.com
3. Get your Measurement ID (starts with G-)
4. In WordPress: **Appearance → Customize → ShelzyPerkins Settings → Analytics**
5. Paste your GA4 ID
6. Save

---

## STEP 10: Configure Rank Math SEO

After installing Rank Math:
1. Run the Setup Wizard
2. Connect your Google account
3. Import settings from `seo/rankmath-config.json`
4. Set homepage title: "ShelzyPerkins | Smart Deals & Real Savings"
5. Set homepage description: "Discover the best Amazon deals..."

---

## STEP 11: Speed Optimization

In **LiteSpeed Cache** settings (or WP Super Cache):
- Enable Page Cache
- Enable Browser Cache
- Enable CSS/JS Minification
- Enable Lazy Load Images

---

## STEP 12: Final Checklist

- [ ] Theme installed and activated
- [ ] All plugins installed
- [ ] Pages created (About, Contact, Privacy, Disclosure)
- [ ] Categories created
- [ ] At least 3 posts published
- [ ] Menus configured
- [ ] Google Analytics connected
- [ ] Site loads fast (test at PageSpeed Insights)
- [ ] Mobile looks good

---

## Quick Fixes for Common Issues

### "Site not secure" warning
- In GoDaddy, enable free SSL certificate
- Go to GoDaddy → Manage Hosting → SSL → Enable
- Wait 10-15 minutes

### Theme not showing correctly
- Clear cache: LiteSpeed Cache → Purge All
- Check theme is activated

### Shortcodes showing as text
- Make sure theme is activated (not a different theme)

---

## Support

- GoDaddy Help: 1-480-505-8877
- WordPress Forums: wordpress.org/support
- Theme files are in your GitHub repo

---

**Affiliate Tag (already configured):** shelzysdesigns-20
