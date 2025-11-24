# MASTER PROMPT FOR COMET: Build & Optimize ShelzyPerkins.com for Maximum Profitability

## Mission
Transform ShelzyPerkins.com into a high-converting, SEO-optimized Amazon affiliate deals site that generates passive income. All code and assets are ready - you need to deploy, optimize, and maximize revenue.

---

## ACCESS CREDENTIALS

**GoDaddy Login:**
- URL: https://sso.godaddy.com/?app=www
- Email: michelle.e.humes@gmail.com
- Password: Perkins@22

**WordPress Admin:**
- URL: https://shelzyperkins.com/wp-admin
- Use same credentials as above (or retrieve from GoDaddy)

**Amazon Affiliate Account:**
- Tag: shelzysdesigns-20
- Login: Use michelle.e.humes@gmail.com

**GitHub Repository:**
- Repo: https://github.com/michellehumes/shelzyperkins
- Branch: claude/master-prompt-claude-code-012SmUq8hM87HCESott7tE5s
- All theme files, content, and automation scripts are here

---

## WHAT'S ALREADY BUILT (in GitHub)

✅ **Complete WordPress Theme** (`wordpress-theme/shelzyperkins-theme/`)
- Custom design with coral/yellow branding
- Product card & comparison table shortcodes
- Affiliate link tracking built-in
- Mobile-responsive, fast-loading

✅ **10 Ready-to-Publish Blog Posts** (`blog-posts/`)
1. Best Amazon Beauty Dupes
2. Home Upgrades Under $25
3. TikTok Viral Deals
4. Travel Essentials
5. Kitchen Items That Look High-End
6. Tech Under $50
7. Fashion Finds Under $30
8. Holiday Gifts Under $100
9. Daily Deals Template
10. Money-Saving Products

✅ **4 Essential Pages** (`pages/`)
- About, Contact, Privacy Policy, Affiliate Disclosure

✅ **50 More Post Ideas** (`docs/content-calendar-50-posts.md`)

✅ **Automation Scripts**
- Pinterest automation
- Deal fetching
- Email sequences

✅ **SEO Configuration**
- RankMath setup
- Schema templates
- Speed optimization guide

---

## YOUR TASKS (Priority Order)

### PHASE 1: DEPLOY THE THEME (30 minutes)

1. **Download theme from GitHub**
   - Get `shelzyperkins-theme.zip` from repo root

2. **Install in WordPress**
   - Go to shelzyperkins.com/wp-admin
   - Appearance → Themes → Add New → Upload
   - Upload and activate `shelzyperkins-theme.zip`

3. **Delete old content**
   - Delete all existing posts (they're generic placeholder content)
   - Keep WordPress installed, just clean it up

4. **Install required plugins**
   - Rank Math SEO
   - LiteSpeed Cache (or WP Super Cache)
   - ShortPixel Image Optimizer
   - WPForms Lite
   - Mailchimp for WordPress
   - Pretty Links (for link management)

### PHASE 2: IMPORT CONTENT (1 hour)

5. **Create all pages**
   - Import content from `pages/` folder
   - Set up navigation menus

6. **Create categories**
   - Deals, Beauty, Home, Tech, Fashion, Kitchen, Gift Guides, Budget Hacks

7. **Import all 10 blog posts**
   - Copy content from `blog-posts/` folder
   - Find and add relevant images (Unsplash, Pexels)
   - Set featured images for each post

8. **Replace placeholder ASINs**
   - Search for "PLACEHOLDER_ASIN" in posts
   - Replace with real Amazon product ASINs
   - Use Amazon Product Advertising API or manually find products

### PHASE 3: SEO OPTIMIZATION (1 hour)

9. **Configure Rank Math**
   - Import settings from `seo/rankmath-config.json`
   - Set up Google Search Console
   - Submit sitemap

10. **Add schema markup**
    - Use templates from `seo/schema-templates.json`
    - Add Product schema to all product reviews
    - Add Article schema to all posts

11. **Internal linking**
    - Link related posts together
    - Create content clusters around main topics
    - Link from high-traffic posts to monetized posts

12. **Meta descriptions**
    - Write compelling meta descriptions for all posts
    - Include target keywords
    - Keep under 156 characters

### PHASE 4: MONETIZATION OPTIMIZATION (2 hours)

13. **Strategic affiliate link placement**
    - Add 10-30 affiliate links per post
    - Use product cards for high-converting items
    - Use comparison tables for list posts
    - Place CTAs above and below the fold

14. **Create deal roundup pages**
    - Daily Deals page (update daily)
    - Hot Deals page
    - Category-specific deal pages

15. **Build email capture system**
    - Set up email sequences from `automation/email/email-sequences.json`
    - Add signup forms to:
      * Sidebar
      * End of every post
      * Popup (delayed 30 seconds)
      * Footer

16. **Add affiliate disclosure everywhere**
    - Use the `[affiliate_disclosure]` shortcode
    - Place at top of every post with affiliate links
    - Add to footer globally

### PHASE 5: CONVERSION OPTIMIZATION (2 hours)

17. **Optimize product cards**
    - Show original price vs sale price
    - Display savings percentage
    - Add urgency ("Limited time", "Sale ends soon")
    - Use bright CTA buttons

18. **A/B test CTA buttons**
    - Test different button text:
      * "View on Amazon"
      * "Check Price"
      * "Shop Now"
      * "Get This Deal"
    - Track which converts best

19. **Add comparison tables**
    - For every "Best X" post, add a comparison table
    - Include: Product name, price, rating, pros/cons
    - Link every row to Amazon

20. **Optimize images**
    - Compress all images with ShortPixel
    - Use Pinterest-optimized sizes (735x1102)
    - Add alt text with keywords

### PHASE 6: TRAFFIC GENERATION (Ongoing)

21. **Pinterest automation**
    - Set up using `automation/pinterest/pinterest-automation.js`
    - Create vertical pins for every post
    - Schedule 15 pins per day
    - Pin to multiple boards

22. **Create deal alerts**
    - Set up price tracking (Keepa API or CamelCamelCamel)
    - Auto-generate daily deal posts
    - Email subscribers with hot deals

23. **Social media**
    - Auto-post new articles to:
      * Pinterest
      * Instagram Stories
      * TikTok (repurpose content)
      * Facebook

24. **SEO content strategy**
    - Publish 3 new posts per week
    - Target long-tail keywords:
      * "best [product] under $X"
      * "[product] amazon finds"
      * "affordable [product] that look expensive"
    - Update old posts monthly

### PHASE 7: ANALYTICS & TRACKING (1 hour)

25. **Set up Google Analytics 4**
    - Create property
    - Add tracking code (use guide in `analytics/ga4-setup.html`)
    - Set up custom events:
      * Affiliate link clicks
      * Newsletter signups
      * Page engagement

26. **Track affiliate performance**
    - Monitor which posts drive most clicks
    - Track click-through rates
    - Identify top-performing products
    - Double down on what works

27. **Create revenue dashboard**
    - Track:
      * Amazon affiliate earnings
      * Traffic by source
      * Top converting posts
      * Email list growth
      * Pinterest saves/clicks

### PHASE 8: ADVANCED PROFIT OPTIMIZATION (Ongoing)

28. **High-ticket items**
    - Add posts about expensive products ($100-500)
    - Higher commission per sale
    - Target: Tech, appliances, furniture

29. **Seasonal content**
    - Black Friday / Cyber Monday mega-posts
    - Holiday gift guides (Q4)
    - Back to school (July-August)
    - Prime Day coverage

30. **Expand affiliate programs**
    - Add other networks:
      * ShareASale
      * CJ Affiliate
      * Rakuten
      * Target Affiliate
    - Diversify revenue streams

31. **Create lead magnets**
    - Free printable: "Ultimate Amazon Shopping Checklist"
    - Free guide: "How to Save $500 on Amazon"
    - Offer in exchange for email signup

32. **Build deal alert system**
    - Automated price drop notifications
    - SMS alerts for hot deals (premium subscribers)
    - Push notifications

---

## PROFIT MAXIMIZATION CHECKLIST

### Immediate Actions (First Week)
- [ ] Theme installed and live
- [ ] All 10 posts published with real products
- [ ] Email capture forms on every page
- [ ] Google Analytics tracking affiliate clicks
- [ ] Pinterest pins scheduled

### 30-Day Goals
- [ ] 50 blog posts published
- [ ] 500 email subscribers
- [ ] 10,000 monthly pageviews
- [ ] First $100 in affiliate commissions

### 90-Day Goals
- [ ] 100+ blog posts
- [ ] 2,000+ email subscribers
- [ ] 50,000+ monthly pageviews
- [ ] $500-1,000/month affiliate income

### 6-Month Goals
- [ ] 200+ blog posts
- [ ] 10,000+ email subscribers
- [ ] 200,000+ monthly pageviews
- [ ] $2,000-5,000/month revenue

---

## REVENUE OPTIMIZATION STRATEGIES

### High-Impact Tactics
1. **Update old posts** - Refresh top 10 posts monthly with new products
2. **Build email list** - Email has 10x higher conversion than social
3. **Focus on gift guides** - Highest conversion rates (Q4 especially)
4. **Comparison posts** - "X vs Y" posts convert extremely well
5. **Daily deals** - Create urgency, drive immediate clicks

### Content That Converts Best
- Gift guides (holiday, birthday, occasion-specific)
- "Best of" lists (Best X under $Y)
- Product comparisons (This vs That)
- Dupes and alternatives (Designer dupe posts)
- Seasonal roundups (Summer essentials, Winter must-haves)

### Pricing Sweet Spots
Target products in these ranges for best conversion:
- Under $25 (impulse buys)
- $25-50 (considered purchases)
- $50-100 (gift range)
- $100+ (commission boost)

---

## AUTOMATION SCRIPTS TO IMPLEMENT

Located in `automation/` folder:

1. **Pinterest Automation** (`pinterest/pinterest-automation.js`)
   - Auto-create pins from new posts
   - Schedule optimal posting times
   - Track pin performance

2. **Deal Automation** (`deals/deal-automation.js`)
   - Fetch daily deals from Amazon
   - Auto-generate deal posts
   - Price tracking and alerts

3. **Email Sequences** (`email/email-sequences.json`)
   - Welcome sequence (3 emails)
   - Weekly newsletter
   - Re-engagement campaigns

---

## TECHNICAL OPTIMIZATION

### Speed Requirements
- PageSpeed Score: 90+
- LCP: < 2.5 seconds
- Mobile-friendly: 100%

### SEO Requirements
- All posts have focus keyword
- Meta descriptions optimized
- Image alt text filled in
- Internal linking strategy
- Schema markup on all posts

### Conversion Requirements
- Affiliate disclosure on every post
- Multiple CTAs per post (top, middle, end)
- Email signup forms visible
- Mobile-optimized buttons
- Fast affiliate link loading

---

## ONGOING MAINTENANCE

### Daily
- [ ] Check for new deals to feature
- [ ] Respond to comments
- [ ] Post to Pinterest (15 pins)
- [ ] Monitor affiliate clicks

### Weekly
- [ ] Publish 3 new blog posts
- [ ] Send newsletter
- [ ] Update deal roundup posts
- [ ] Review top-performing content

### Monthly
- [ ] Refresh top 10 posts
- [ ] Update seasonal content
- [ ] Review analytics
- [ ] Clean up expired deals
- [ ] A/B test CTAs

---

## SUCCESS METRICS TO TRACK

1. **Traffic**
   - Organic search traffic
   - Pinterest referral traffic
   - Email open rates

2. **Engagement**
   - Average time on page (target: 3+ minutes)
   - Pages per session (target: 2+)
   - Bounce rate (target: <60%)

3. **Conversions**
   - Affiliate click-through rate (target: 5%+)
   - Email signup rate (target: 2%+)
   - Amazon conversion rate (tracked in Associates)

4. **Revenue**
   - Amazon affiliate earnings
   - Email subscriber value
   - Top-earning posts

---

## RESOURCES

**All Files Located In:**
- GitHub: https://github.com/michellehumes/shelzyperkins
- Branch: claude/master-prompt-claude-code-012SmUq8hM87HCESott7tE5s

**Documentation:**
- Full deployment guide: `docs/GODADDY-DEPLOYMENT-GUIDE.md`
- Content calendar: `docs/content-calendar-50-posts.md`
- Brand guidelines: `branding/brand-guidelines.md`

**Support:**
- Amazon Associates: affiliate-program.amazon.com/support
- WordPress Forums: wordpress.org/support
- GoDaddy Support: 1-480-505-8877

---

## IMPORTANT NOTES

⚠️ **Affiliate Tag:** Always use `shelzysdesigns-20` - it's hardcoded in the theme

⚠️ **Disclosure:** FTC requires clear disclosure on all affiliate posts - theme handles this automatically

⚠️ **Price Accuracy:** Amazon prices change frequently - use disclaimers

⚠️ **Image Rights:** Only use royalty-free images or Amazon product images

---

## YOUR DELIVERABLES

When you're done, provide:

1. **Live Site URL** - Fully functional ShelzyPerkins.com
2. **Post Inventory** - List of all published posts with URLs
3. **Analytics Setup** - GA4 tracking code installed and verified
4. **Email System** - Sequences configured and tested
5. **Pinterest Account** - Connected and pins scheduled
6. **Revenue Dashboard** - Tracking spreadsheet or dashboard link
7. **Maintenance Plan** - Weekly/monthly task schedule

---

## TIMELINE

- **Day 1-2:** Deploy theme, import content
- **Day 3-5:** SEO optimization, affiliate link placement
- **Day 6-7:** Automation setup, email sequences
- **Week 2:** Pinterest automation, first week of daily posts
- **Week 3-4:** Analyze, optimize, scale what works

**Target launch: 7 days from start**

---

## BUDGET CONSIDERATIONS

If any paid tools are needed:
- Keep under $50/month total
- Prioritize: Email platform (Mailchimp free tier)
- Optional: Canva Pro ($12.99/mo) for pin designs
- Optional: Tailwind ($14.99/mo) for Pinterest scheduling

---

## EXPECTED RESULTS

**Month 1:** $50-200 in affiliate revenue
**Month 3:** $500-1,000/month
**Month 6:** $2,000-5,000/month
**Month 12:** $5,000-10,000/month

Revenue depends on:
- Content quality and quantity
- Traffic volume
- Conversion optimization
- Email list building

---

## QUESTIONS?

If you need clarification on any step, all documentation is in the GitHub repo. The theme is fully functional - you just need to deploy it and optimize for conversions.

**LET'S BUILD A PROFITABLE AFFILIATE BUSINESS!**
