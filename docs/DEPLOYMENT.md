# Deployment Guide for ShelzyPerkins.com

## Quick Deploy Options

### Option 1: Manual Upload (Easiest)

1. Download the theme package: `dist/shelzyperkins-theme.zip`
2. Log into your WordPress admin dashboard
3. Go to **Appearance > Themes > Add New > Upload Theme**
4. Upload `shelzyperkins-theme.zip`
5. Click **Install Now**, then **Activate**

### Option 2: FTP Upload

1. Connect to your hosting via FTP (FileZilla, Cyberduck, etc.)
2. Navigate to `/wp-content/themes/`
3. Upload the entire `shelzyperkins-theme` folder
4. Activate in WordPress admin under **Appearance > Themes**

### Option 3: cPanel File Manager

1. Log into cPanel
2. Open **File Manager**
3. Navigate to `public_html/wp-content/themes/`
4. Upload `shelzyperkins-theme.zip`
5. Extract the zip file
6. Activate in WordPress admin

---

## Automated Deployment (GitHub Actions)

### Setup for FTP Deployment

1. Go to your GitHub repository **Settings > Secrets and variables > Actions**
2. Add the following secrets:
   - `FTP_SERVER`: Your FTP server address (e.g., `ftp.yourdomain.com`)
   - `FTP_USERNAME`: Your FTP username
   - `FTP_PASSWORD`: Your FTP password
   - `FTP_PATH`: Path to WordPress installation (e.g., `/public_html`)
3. Add a repository variable:
   - `DEPLOY_METHOD`: Set to `ftp`
4. Push to `main` branch to trigger deployment

### Setup for SSH Deployment

1. Go to your GitHub repository **Settings > Secrets and variables > Actions**
2. Add the following secrets:
   - `SSH_HOST`: Your server IP or hostname
   - `SSH_USERNAME`: SSH username
   - `SSH_KEY`: Your private SSH key (full content)
   - `SSH_PATH`: Path to WordPress installation (e.g., `/var/www/html`)
3. Add a repository variable:
   - `DEPLOY_METHOD`: Set to `ssh`
4. Push to `main` branch to trigger deployment

---

## Recommended Hosting Providers

### Budget-Friendly
- **Bluehost** - $2.95/mo, free domain, WordPress pre-installed
- **Hostinger** - $1.99/mo, fast LiteSpeed servers
- **SiteGround** - $2.99/mo, excellent support

### Performance-Focused
- **Cloudways** - $14/mo, DigitalOcean/Vultr/AWS options
- **Kinsta** - $35/mo, Google Cloud Platform
- **WP Engine** - $20/mo, managed WordPress

### For Maximum Affiliate Earnings
Recommend **Cloudways** or **SiteGround** for:
- Fast page loads (better conversions)
- Built-in caching
- Free SSL certificates
- CDN support

---

## Post-Deployment Checklist

### 1. Install Required Plugins
```
- Rank Math SEO (free)
- LiteSpeed Cache OR WP Super Cache
- ShortPixel Image Optimizer
- WPForms Lite
- Mailchimp for WordPress
```

### 2. Configure Rank Math SEO
- Import settings from `seo/rank-math-settings.json`
- Set up XML sitemap
- Connect Google Search Console

### 3. Set Up Caching
- Enable page caching
- Enable browser caching
- Enable GZIP compression
- Set up CDN (Cloudflare free tier)

### 4. Import Content
Upload blog posts from `blog-posts/` directory:
1. Go to **Posts > Add New**
2. Copy markdown content
3. Add featured images
4. Set categories and tags
5. Publish

### 5. Configure Email Marketing
- Sign up for Mailchimp (free up to 500 contacts)
- Create signup forms for:
  - Newsletter popup
  - Footer signup
  - Deal alerts

### 6. Set Up Analytics
- Create Google Analytics 4 property
- Add GA4 tracking ID to theme customizer
- Set up conversion tracking for affiliate clicks

### 7. Verify Affiliate Links
Test all shortcodes are working:
```
[amazon_link asin="B08N5WRWNW" text="Test Link"]
[bounty_card program="prime"]
[product_card asin="..." title="..." price="..."]
```

### 8. Submit to Search Engines
- Submit sitemap to Google Search Console
- Submit sitemap to Bing Webmaster Tools
- Set up Pinterest verification

---

## Performance Optimization

### Target Metrics
- PageSpeed Score: 90+
- LCP (Largest Contentful Paint): < 2.5s
- Mobile Performance: 85+

### Speed Checklist
- [ ] Enable caching plugin
- [ ] Optimize images with ShortPixel
- [ ] Enable lazy loading
- [ ] Minify CSS/JS
- [ ] Use CDN (Cloudflare)
- [ ] Enable GZIP compression
- [ ] Reduce HTTP requests

---

## Troubleshooting

### Theme Not Appearing
- Check file permissions (755 for folders, 644 for files)
- Ensure `style.css` has valid theme header
- Check PHP errors in `wp-content/debug.log`

### Shortcodes Not Working
- Verify theme is activated
- Check `functions.php` for syntax errors
- Test with default theme to isolate issue

### Slow Performance
- Check hosting server response time
- Optimize database with WP-Optimize
- Remove unused plugins
- Use image compression

### Affiliate Links Not Tracking
- Verify affiliate tag: `shelzysdesigns-20`
- Check browser console for JavaScript errors
- Ensure GA4 is properly configured

---

## Support

For issues with:
- **WordPress hosting**: Contact your hosting provider
- **Theme customization**: Check `docs/shortcode-reference.md`
- **Amazon Associates**: Visit [Amazon Associates Help](https://affiliate-program.amazon.com/help)
