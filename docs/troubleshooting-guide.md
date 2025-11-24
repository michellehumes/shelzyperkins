# ShelzyPerkins.com - Troubleshooting Guide

## Common Issues & Solutions

---

## Affiliate Link Issues

### Problem: Affiliate links not tracking clicks

**Symptoms:**
- Zero clicks in Amazon Associates dashboard
- Links work but no tracking data
- Earnings not registering

**Solutions:**

1. **Verify affiliate tag**
   ```
   Check: https://www.amazon.com/dp/B0XXXXXXXX?tag=shelzysdesigns-20
                                                     ^^^^^^^^^^^^^^^^^^^
                                                     Must be present
   ```
   - Affiliate tag should be: `shelzysdesigns-20`
   - Check every link on the site
   - Use browser "Find" (Ctrl+F) to search for "tag="

2. **Test in incognito mode**
   - Open link in private/incognito window
   - Click through to Amazon
   - Verify URL still contains affiliate tag
   - Check Amazon Associates dashboard after 24 hours

3. **Check link format**
   - ✅ Correct: `https://www.amazon.com/dp/ASIN?tag=shelzysdesigns-20`
   - ✅ Correct: `https://amzn.to/XXXXX` (SiteStripe short links)
   - ❌ Wrong: `https://www.amazon.com/product-name/dp/ASIN/` (no tag)
   - ❌ Wrong: `amazon.com/dp/ASIN` (missing https://)

4. **Clear browser cache**
   - WordPress: LiteSpeed Cache → Purge All
   - Browser: Ctrl+Shift+Del → Clear cache
   - Test links again

5. **Check Amazon Associates account status**
   - Log in to affiliate-program.amazon.com
   - Verify account is active
   - Check for any warnings or suspensions
   - Ensure you've made 3 sales within first 180 days

**Prevention:**
- Use `[amazon_link]` shortcode (has affiliate tag built-in)
- Always test links after publishing
- Set up Google Analytics event tracking for clicks

---

### Problem: "This product is currently unavailable"

**Symptoms:**
- Affiliate link goes to Amazon but shows "unavailable"
- Product page says "Out of stock"
- 404 error on product page

**Solutions:**

1. **Find replacement product**
   - Search Amazon for similar product
   - Get new ASIN
   - Update post with new product

2. **Update product card**
   ```
   [product_card asin="NEW_ASIN" title="New Product Name" ...]
   ```

3. **Add note to post**
   - "Update: This product is currently unavailable. Here's a similar alternative..."

4. **Set up monitoring**
   - Use CamelCamelCamel or Keepa
   - Get alerts when product back in stock
   - Update post when available again

**Prevention:**
- Check product availability before publishing
- Favor products with high stock counts
- Review old posts monthly for dead links

---

### Problem: Earning $0 despite clicks

**Symptoms:**
- Clicks showing in dashboard
- Zero earnings after 30 days
- Conversion rate is 0%

**Solutions:**

1. **Check conversion window**
   - Amazon cookie lasts 24 hours
   - If customer doesn't buy within 24 hours, no commission
   - This is normal - not all clicks convert

2. **Analyze click quality**
   - Are you sending relevant traffic?
   - Is the product appealing to your audience?
   - Are prices competitive?

3. **Improve conversion rates**
   - Better product selection (focus on bestsellers)
   - Add urgency ("Limited time deal!")
   - Show social proof (ratings, reviews)
   - Target lower-priced items (easier impulse buys)

4. **Check for cookie blocking**
   - Some users block tracking cookies
   - Nothing you can do about this
   - This affects all affiliates

**Expected Timeline:**
- Week 1: Clicks, likely no earnings
- Week 2-4: First earnings start appearing
- Month 2-3: More consistent income
- Month 6+: Predictable revenue

---

## WordPress Issues

### Problem: Theme not displaying correctly

**Symptoms:**
- Layout broken
- CSS not loading
- Missing images or buttons

**Solutions:**

1. **Clear all caches**
   - WordPress: LiteSpeed Cache → Purge All
   - Browser: Hard refresh (Ctrl+Shift+R)
   - Cloudflare (if using): Purge Cache

2. **Check theme activation**
   - WordPress → Appearance → Themes
   - Ensure "ShelzyPerkins Theme" is active
   - Try deactivating and reactivating

3. **Verify file upload**
   - Re-upload `shelzyperkins-theme.zip`
   - Check all files uploaded correctly:
     ```
     /wp-content/themes/shelzyperkins-theme/
       ├── style.css
       ├── functions.php
       ├── header.php
       ├── footer.php
       └── ...
     ```

4. **Check for plugin conflicts**
   - Deactivate all plugins
   - Activate theme
   - Reactivate plugins one by one
   - Identify conflicting plugin

5. **Inspect browser console**
   - Right-click → Inspect → Console tab
   - Look for JavaScript errors
   - Fix or report errors

**Prevention:**
- Always backup before theme updates
- Test theme changes on staging site first
- Keep WordPress and plugins updated

---

### Problem: Shortcodes not working

**Symptoms:**
- `[product_card]` displays as text
- Shortcodes not rendering
- Just see code on page

**Solutions:**

1. **Check theme activation**
   - Shortcodes are defined in `functions.php`
   - Theme must be active for shortcodes to work

2. **Verify shortcode syntax**
   - ✅ Correct: `[product_card asin="B08XXX" title="Product"]...[/product_card]`
   - ❌ Wrong: `[productcard]` (no underscore)
   - ❌ Wrong: `[product_card asin=B08XXX]` (missing quotes)

3. **Check for extra spaces**
   ```
   ❌ [ product_card asin="..." ]  (spaces inside brackets)
   ✅ [product_card asin="..."]    (no spaces)
   ```

4. **Re-save functions.php**
   - FTP into site
   - Download `functions.php`
   - Re-upload to server
   - Clear cache

5. **Test with simple shortcode**
   - Try: `[affiliate_disclosure]`
   - If this works, problem is with specific shortcode
   - Check syntax of broken shortcode

**Available Shortcodes:**
- `[amazon_link asin="..."]Text[/amazon_link]`
- `[product_card asin="..." title="..." price="..." image="..." rating="..."]Description[/product_card]`
- `[comparison_table]...[/comparison_table]`
- `[product_row asin="..." name="..." price="..." rating="..."]`
- `[affiliate_disclosure]`
- `[email_signup title="..." cta="..."]`
- `[deal_badge text="..." type="..."]`

---

### Problem: Images not loading

**Symptoms:**
- Broken image icons
- Slow image loading
- Images missing on some pages

**Solutions:**

1. **Check image URL**
   - Verify image actually exists at URL
   - Test URL in browser address bar
   - Use absolute URLs, not relative

2. **Check file permissions**
   - Images folder: 755 permissions
   - Image files: 644 permissions
   - Fix via FTP client or cPanel

3. **Optimize images**
   - Install ShortPixel plugin
   - Bulk optimize all images
   - Target: under 100KB per image

4. **Check lazy loading**
   - LiteSpeed Cache → Image Optimization
   - Enable lazy loading
   - Exclude above-the-fold images

5. **Verify CDN (if using)**
   - Purge CDN cache
   - Check CDN settings
   - Test without CDN temporarily

**Prevention:**
- Always compress images before upload
- Use WebP format where possible
- Set up automatic image optimization

---

## SEO Issues

### Problem: Not ranking in Google

**Symptoms:**
- Posts published but no traffic
- Can't find site in search results
- Zero organic visitors

**Solutions:**

1. **Check if indexed**
   - Google: `site:shelzyperkins.com`
   - If no results, site not indexed yet
   - Submit sitemap in Google Search Console

2. **Submit to Search Console**
   - Go to: search.google.com/search-console
   - Add property: shelzyperkins.com
   - Verify ownership
   - Submit sitemap: shelzyperkins.com/sitemap.xml

3. **Check robots.txt**
   - Visit: shelzyperkins.com/robots.txt
   - Ensure it's not blocking Google
   - Should include:
     ```
     User-agent: *
     Allow: /
     Sitemap: https://shelzyperkins.com/sitemap.xml
     ```

4. **Verify Rank Math settings**
   - Rank Math → General Settings
   - Ensure "Discourage search engines" is OFF
   - Check sitemap is enabled

5. **Be patient**
   - New sites take 3-6 months to rank
   - Keep publishing quality content
   - Build backlinks

**Timeline Expectations:**
- Week 1-4: Site indexed
- Month 2-3: First organic traffic
- Month 4-6: Consistent rankings
- Month 6+: Page 1 rankings

---

### Problem: Rank Math score is low

**Symptoms:**
- Score below 80/100
- Red or orange indicators
- SEO warnings

**Solutions:**

1. **Fix common issues:**
   - ✅ Focus keyword in title
   - ✅ Focus keyword in first 100 words
   - ✅ Focus keyword in URL
   - ✅ Focus keyword in H2 subheadings
   - ✅ Meta description 145-155 characters
   - ✅ Title 55-60 characters
   - ✅ Content over 1500 words
   - ✅ At least 1 internal link
   - ✅ At least 1 external link
   - ✅ Image alt text includes keyword

2. **Optimize content length**
   - Minimum: 1500 words
   - Ideal: 2000-3000 words
   - Add more product descriptions
   - Add FAQ section
   - Add personal experience

3. **Improve keyword density**
   - Target: 1-2% keyword density
   - Use keyword naturally throughout
   - Don't stuff - sounds spammy
   - Use variations and synonyms

4. **Add schema markup**
   - Rank Math → Schema
   - Add Article schema
   - Add Product schema for products
   - Add FAQ schema if applicable

**Target Score:** 80-100 (green)

---

## Email Issues

### Problem: Email signup not working

**Symptoms:**
- Form displays but doesn't submit
- No confirmation emails sent
- Subscribers not added to list

**Solutions:**

1. **Check email provider connection**
   - Verify Mailchimp/ConvertKit connected
   - Test API key
   - Check for error messages

2. **Test form submission**
   - Fill out form yourself
   - Check spam folder for confirmation
   - Check email provider dashboard

3. **Verify form shortcode**
   ```
   [email_signup title="Get Daily Deals" cta="Subscribe"]
   ```

4. **Check form plugin**
   - Ensure WPForms or Mailchimp plugin active
   - Update to latest version
   - Check plugin settings

5. **Test email deliverability**
   - Send test email
   - Check inbox and spam
   - Verify sending domain configured

**Prevention:**
- Test forms after setup
- Monitor signup rate daily
- Set up form submission notifications

---

### Problem: Low email open rates

**Symptoms:**
- Open rate below 20%
- Clicks extremely low
- Unsubscribes high

**Solutions:**

1. **Improve subject lines**
   - ❌ Boring: "Weekly Newsletter #5"
   - ✅ Better: "🔥 5 Amazon Deals You Can't Miss This Week"
   - Use emojis (sparingly)
   - Create curiosity
   - Mention specific savings

2. **Optimize send time**
   - Best times: Tuesday-Thursday, 10am or 2pm
   - Avoid Monday mornings and weekends
   - Test different times

3. **Clean email list**
   - Remove inactive subscribers (90+ days)
   - Send re-engagement campaign first
   - Keep list engaged and fresh

4. **Improve preview text**
   - First 50 characters matter
   - Don't waste on "View in browser"
   - Tease the content

5. **Authenticate emails**
   - Set up SPF, DKIM, DMARC
   - Prevents spam folder
   - Improves deliverability

**Target Metrics:**
- Open rate: 25%+
- Click rate: 3%+
- Unsubscribe rate: <0.5%

---

## Performance Issues

### Problem: Slow page load speed

**Symptoms:**
- PageSpeed score below 80
- Pages take 5+ seconds to load
- High bounce rate

**Solutions:**

1. **Install caching plugin**
   - Install: LiteSpeed Cache (or WP Super Cache)
   - Enable page caching
   - Enable browser caching
   - Enable object caching

2. **Optimize images**
   - Install ShortPixel
   - Compress all images
   - Convert to WebP format
   - Enable lazy loading

3. **Minify CSS/JS**
   - LiteSpeed Cache → Optimization
   - Enable CSS minification
   - Enable JS minification
   - Combine files

4. **Use CDN** (optional)
   - Cloudflare (free tier)
   - Speeds up global delivery
   - Reduces server load

5. **Limit plugins**
   - Deactivate unused plugins
   - Delete plugins not in use
   - Each plugin adds overhead

6. **Optimize database**
   - Install: WP-Optimize plugin
   - Clean up revisions, spam, trash
   - Run weekly

**Target Scores:**
- Desktop: 90+
- Mobile: 80+
- LCP: <2.5 seconds

---

### Problem: High bounce rate

**Symptoms:**
- Bounce rate above 70%
- Users leave after one page
- Low pages per session

**Solutions:**

1. **Improve content quality**
   - Make content more engaging
   - Add images and formatting
   - Break up text with subheadings

2. **Add internal links**
   - Link to 3-5 related posts
   - Use "You might also like" section
   - Keep users on site longer

3. **Improve page speed**
   - Slow sites = high bounce
   - Optimize per section above

4. **Match user intent**
   - Deliver what title promises
   - Don't clickbait
   - Put best info at top

5. **Optimize for mobile**
   - 70% of traffic is mobile
   - Test on real mobile devices
   - Ensure buttons are tappable

**Target Bounce Rate:** <60%

---

## Monetization Issues

### Problem: Lots of traffic but low revenue

**Symptoms:**
- 10,000+ pageviews/month
- Under $100 revenue
- Conversion rate below 1%

**Solutions:**

1. **Add more affiliate links**
   - Target: 10-30 links per post
   - Link multiple times to top products
   - Add links in first 300 words

2. **Improve CTA placement**
   - Add CTA buttons, not just text links
   - Place CTAs above the fold
   - Use action words: "Check Price", "Shop Now"

3. **Target better products**
   - Choose bestsellers (4.5+ stars)
   - Pick products with good prices ($20-50)
   - Select items with Prime shipping

4. **Create urgency**
   - "Limited time deal"
   - "Only X left in stock"
   - "Sale ends tonight"

5. **Build email list**
   - Email converts 10x better than social
   - Add signup forms everywhere
   - Send weekly deal alerts

6. **Focus on high-intent keywords**
   - "Best [product] to buy" - high intent
   - "What is [product]" - low intent
   - Target buying keywords

**Expected Conversion:**
- Click-through rate: 5% (500 clicks per 10k pageviews)
- Amazon conversion rate: 3% (15 orders per 500 clicks)
- Average order value: $40
- Commission: 4%
- Revenue: $24 per 10k pageviews

---

## Security Issues

### Problem: Site hacked or showing warnings

**Symptoms:**
- Google warning: "This site may be hacked"
- Redirects to spam sites
- Unknown admin users
- Strange files on server

**Solutions:**

1. **Scan for malware**
   - Install: Wordfence Security plugin
   - Run full scan
   - Remove detected threats

2. **Change all passwords**
   - WordPress admin password
   - GoDaddy account password
   - FTP password
   - Database password

3. **Update everything**
   - WordPress core
   - All plugins
   - Theme

4. **Remove unknown users**
   - WordPress → Users
   - Delete any unknown accounts
   - Verify all admin accounts

5. **Contact GoDaddy**
   - Call: 1-480-505-8877
   - Ask for security scan
   - Request malware removal

6. **Restore from backup** (if severely compromised)
   - GoDaddy cPanel → Backups
   - Restore from clean backup
   - Then update everything

**Prevention:**
- Install Wordfence
- Enable 2FA on WordPress
- Keep everything updated
- Use strong passwords
- Regular backups

---

## Analytics Issues

### Problem: Google Analytics not tracking

**Symptoms:**
- Zero data in GA4
- No pageviews showing
- Real-time shows nothing

**Solutions:**

1. **Verify tracking code installed**
   - Check `header.php` or analytics plugin
   - Should be in `<head>` section
   - Format: `G-XXXXXXXXXX`

2. **Test real-time tracking**
   - GA4 → Real-time report
   - Visit your site in another tab
   - Should see yourself in real-time

3. **Check if AdBlocker blocking**
   - Disable browser extensions
   - Test in incognito mode
   - This won't affect real users

4. **Verify property setup**
   - Correct website URL in GA4
   - Data stream is active
   - Measurement ID matches

5. **Wait 24-48 hours**
   - Data can take time to populate
   - Real-time should work immediately
   - Historical data takes longer

**Must-Track Events:**
- Affiliate link clicks
- Email signups
- Page engagement
- Scroll depth

---

## Mobile Issues

### Problem: Site broken on mobile

**Symptoms:**
- Text too small
- Buttons not working
- Horizontal scrolling
- Images overlapping

**Solutions:**

1. **Test mobile responsiveness**
   - Use: developers.google.com/speed/pagespeed/insights
   - Test on real devices (iPhone, Android)
   - Check all breakpoints

2. **Fix common mobile issues:**
   - Increase font size (minimum 16px)
   - Make buttons 48x48px minimum
   - Remove horizontal scrolling
   - Optimize images for mobile

3. **Check viewport meta tag**
   - Should be in `header.php`:
   ```html
   <meta name="viewport" content="width=device-width, initial-scale=1">
   ```

4. **Test tap targets**
   - Buttons and links should be easy to tap
   - Adequate spacing between elements
   - No tiny links

5. **Optimize mobile speed**
   - Mobile users have slower connections
   - Compress images more aggressively
   - Lazy load everything below fold

**Target:**
- Mobile-friendly test: PASS
- Mobile PageSpeed: 80+
- All functions work on mobile

---

## Getting Help

### Official Support Channels

**WordPress:**
- Forums: wordpress.org/support
- Documentation: wordpress.org/documentation

**GoDaddy:**
- Phone: 1-480-505-8877 (24/7)
- Live chat: godaddy.com/help
- Knowledge base: godaddy.com/help

**Amazon Associates:**
- Contact: affiliate-program.amazon.com/support
- Email: associates@amazon.com
- Operating agreement: Check dashboard

**Rank Math:**
- Documentation: rankmath.com/kb
- Support: rankmath.com/support
- Community: facebook.com/groups/rankmathseopluginwp

### Self-Help Resources

**Check the GitHub Repo:**
- Full documentation: All docs in `/docs` folder
- Deployment guide: `GODADDY-DEPLOYMENT-GUIDE.md`
- Quick start: `QUICK-START.md`

**Test Tools:**
- PageSpeed: developers.google.com/speed/pagespeed/insights
- Mobile-Friendly: search.google.com/test/mobile-friendly
- Schema Validator: validator.schema.org
- Broken Link Checker: brokenlinkcheck.com

---

## Emergency Contacts & Credentials

**GoDaddy:**
- URL: https://sso.godaddy.com/?app=www
- Email: michelle.e.humes@gmail.com
- Password: Perkins@22
- Support: 1-480-505-8877

**WordPress Admin:**
- URL: https://shelzyperkins.com/wp-admin
- Same credentials as above

**Amazon Associates:**
- Affiliate tag: shelzysdesigns-20
- Login: michelle.e.humes@gmail.com

---

**Last Updated:** 2024-11-24
**For additional help:** Check full documentation in `/docs` folder
