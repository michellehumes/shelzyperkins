# ShelzyPerkins.com - Quick Reference Guide

## Common Tasks

### How to Add a New Blog Post

1. **Find a product on Amazon**
   - Copy the ASIN (found in product URL: `/dp/B0XXXXXXXX/`)

2. **Create new post**
   - WordPress → Posts → Add New
   - Title: "[Number] Best [Product Category] Under $[Price]"

3. **Add product cards**
   ```
   [product_card asin="B0XXXXXXXX" title="Product Name" price="29.99" image="image-url.jpg"]
   Description of why this product is great...
   [/product_card]
   ```

4. **Add affiliate disclosure at top**
   ```
   [affiliate_disclosure]
   ```

5. **Set featured image**
   - Use Canva to create 1200x628px image
   - Or use product image from Amazon

6. **Configure SEO (Rank Math)**
   - Focus keyword: "best [product] under $[price]"
   - Meta description: 155 characters max
   - Add FAQ schema if applicable

7. **Publish!**

---

### How to Update Daily Deals

1. **Check Amazon for deals**
   - Go to: amazon.com/goldbox
   - Find products with 30%+ discount

2. **Update the Daily Deals post**
   - Edit: `Daily Deals - [Current Date]`
   - Replace old products with new deals
   - Update prices and savings percentages

3. **Create Pinterest pins**
   - Use templates in `automation/pinterest/pin-templates.json`
   - Create urgency: "TODAY ONLY" or "ENDS TONIGHT"

4. **Send email to subscribers**
   - Subject: "🔥 Today's Hot Deals - Up to [XX]% Off!"
   - Include top 5 deals with prices

---

### How to Create a Gift Guide

1. **Choose occasion**
   - Examples: Birthday, Mother's Day, Graduation, Christmas

2. **Pick price range**
   - Under $25 (impulse gifts)
   - Under $50 (thoughtful gifts)
   - Under $100 (special occasions)

3. **Find 10-15 products**
   - Mix of categories: beauty, tech, home, fashion
   - All within chosen price range

4. **Use comparison table**
   ```
   [comparison_table]
   [product_row asin="..." name="..." price="..." rating="4.8"]
   [product_row asin="..." name="..." price="..." rating="4.7"]
   [/comparison_table]
   ```

5. **Create Pinterest board**
   - Board name: "[Occasion] Gift Guide 2024"
   - Create vertical pins for each product
   - Schedule 3 pins per day

---

### How to Find Product ASINs

**Method 1: From Amazon URL**
```
https://www.amazon.com/dp/B0XXXXXXXX/
                          ^^^^^^^^^^^
                          This is the ASIN
```

**Method 2: From product page**
- Scroll to "Product Information" section
- Look for "ASIN: B0XXXXXXXX"

**Method 3: Browser extension**
- Install: "Amazon ASIN Scraper" extension
- Hover over product → ASIN appears

---

### How to Track Affiliate Performance

1. **Log into Amazon Associates**
   - URL: affiliate-program.amazon.com
   - Email: michelle.e.humes@gmail.com

2. **Check key metrics**
   - Dashboard → Reports → Earnings Report
   - Look for:
     * Total clicks
     * Conversion rate (target: 5%+)
     * Items ordered
     * Total earnings

3. **Identify top posts**
   - Reports → By Link
   - Sort by "Items Ordered"
   - Double down on what's working

4. **Update top posts monthly**
   - Refresh product listings
   - Add new products
   - Update prices
   - Check for expired links

---

### How to Create Pinterest Pins

**Pin Size:** 1000x1500px (2:3 ratio)

**Using Canva:**
1. Create new design → Custom size → 1000x1500
2. Use brand colors: #FF6B6B (coral), #FFE66D (yellow)
3. Add product image (600x600px, centered)
4. Add text overlay:
   - Product name (bold, 48px)
   - Price in large font (72px)
   - "SAVE X%" badge
   - Watermark: "ShelzyPerkins.com"

**Pin Description Formula:**
```
🔥 [Product Name] is [X]% OFF right now!

Perfect for [use case]. [Brief benefit].

Tap to shop → [Short URL]

#AmazonFinds #DealsOfTheDay #[Category] | ShelzyPerkins.com
```

**Scheduling:**
- Best times: 7am, 12pm, 3pm, 6pm, 9pm
- Use Tailwind or Pinterest scheduling
- 15 pins per day across all boards

---

### How to Build Email Sequences

**Welcome Sequence (3 emails):**

**Email 1 - Immediate:**
- Subject: "Welcome! Here's your Amazon deals cheat sheet 📋"
- Content: Thanks for joining + link to free printable
- CTA: "Browse Today's Deals"

**Email 2 - Day 3:**
- Subject: "The 5 Amazon products I can't live without"
- Content: Personal story + 5 favorite products with affiliate links
- CTA: "See All My Favorites"

**Email 3 - Day 7:**
- Subject: "How to never miss a deal again"
- Content: Explain deal alerts, weekly newsletter
- CTA: "Update Your Preferences"

**Weekly Newsletter Formula:**
- Catchy subject with emoji
- Personal intro (2 sentences)
- Top 3-5 deals of the week
- Featured post link
- P.S. with urgency

---

### How to Optimize Conversion Rates

**High-Converting Elements:**
1. **Multiple CTAs per post** - Top, middle, bottom
2. **Urgency language** - "Limited time", "While supplies last"
3. **Social proof** - Star ratings, review counts
4. **Price anchoring** - Show original price crossed out
5. **Savings percentage** - "SAVE 45%!" in red badge

**Button Text That Converts:**
- ✅ "Check Price on Amazon" (best)
- ✅ "View Deal →"
- ✅ "Shop Now"
- ❌ "Click here"
- ❌ "Learn more"

**Image Best Practices:**
- Use product images on white background
- Show product in use (lifestyle shots)
- Multiple angles in gallery
- Compress with ShortPixel (under 100KB)

---

### How to Handle Seasonal Content

**Q4 (Oct-Dec) - Holiday Season:**
- Gift guides (Halloween, Thanksgiving, Christmas, Hanukkah)
- Black Friday mega-posts
- Cyber Monday deals
- Last-minute gift roundups
- Stocking stuffers under $10

**Q1 (Jan-Mar) - New Year:**
- Organization products
- Fitness gear
- Meal prep tools
- Valentine's Day gifts
- Spring cleaning

**Q2 (Apr-Jun) - Spring/Summer:**
- Outdoor gear
- Travel essentials
- Father's Day gifts
- Graduation gifts
- Summer fashion

**Q3 (Jul-Sep) - Back to School:**
- Dorm room essentials
- School supplies
- Lunch boxes
- Teacher gifts
- Fall fashion preview

---

## WordPress Shortcodes Reference

### Product Card
```
[product_card asin="B0XXXXXXXX" title="Product Name" price="29.99" image="url" rating="4.5"]
Product description here...
[/product_card]
```

### Comparison Table
```
[comparison_table]
[product_row asin="..." name="..." price="..." rating="..." pros="Pro 1, Pro 2" cons="Con 1"]
[product_row asin="..." name="..." price="..." rating="..." pros="Pro 1, Pro 2" cons="Con 1"]
[/comparison_table]
```

### Amazon Link Button
```
[amazon_link asin="B0XXXXXXXX"]Shop Now[/amazon_link]
```

### Deal Badge
```
[deal_badge text="50% OFF" type="hot"]
```

### Affiliate Disclosure
```
[affiliate_disclosure]
```

### Email Signup Form
```
[email_signup title="Get Daily Deals" cta="Subscribe"]
```

---

## Quick Wins for More Revenue

### Week 1:
- [ ] Publish 3 "Best of" list posts
- [ ] Create Pinterest account + 5 boards
- [ ] Set up email welcome sequence
- [ ] Add email signup to every post

### Week 2:
- [ ] Publish daily deal updates (7 posts)
- [ ] Create 21 Pinterest pins
- [ ] Send first newsletter
- [ ] Update homepage with latest deals

### Week 3:
- [ ] Create holiday gift guide
- [ ] Start tracking top-performing posts
- [ ] Double down on what's working
- [ ] Refresh old posts with new products

### Week 4:
- [ ] Analyze Google Analytics
- [ ] Check Amazon Associates earnings
- [ ] Optimize low-performing posts
- [ ] Plan next month's content

---

## Tools & Resources

### Free Tools:
- **Canva** - Pin design (free tier: 250k+ templates)
- **Unsplash** - Stock photos (free, no attribution required)
- **Google Analytics** - Traffic tracking
- **Google Search Console** - SEO monitoring
- **Amazon Associates** - Affiliate earnings tracking

### Paid Tools (Optional):
- **Tailwind** - Pinterest scheduling ($14.99/mo)
- **Canva Pro** - More templates ($12.99/mo)
- **Keepa** - Amazon price tracking ($19/mo)
- **ConvertKit** - Email marketing ($29/mo for 1k subs)

### Image Sources:
- Unsplash.com (free)
- Pexels.com (free)
- Amazon product images (allowed for affiliates)
- Canva stock library (millions of images)

### Keyword Research:
- Google Autocomplete ("best amazon...")
- Pinterest search suggestions
- Amazon search bar
- AnswerThePublic.com (free tier)
- Google Trends

---

## Troubleshooting Common Issues

**Affiliate links not tracking:**
- Check that ASIN is correct
- Verify affiliate tag: shelzysdesigns-20
- Test in incognito window
- Check Amazon Associates dashboard

**Images not loading:**
- Compress with ShortPixel
- Use absolute URLs (not relative)
- Check file permissions
- Clear cache (LiteSpeed)

**Low conversion rate:**
- Add more CTAs
- Use urgency language
- Show savings percentage
- Add social proof (ratings)
- Check mobile experience

**Email signups low:**
- Offer lead magnet (free printable)
- Add popup (delayed 30 seconds)
- Improve form copy
- Test different placements

---

## Content Checklist

Before publishing any post:
- [ ] Affiliate disclosure at top
- [ ] 10+ affiliate links
- [ ] Featured image (1200x628px)
- [ ] Meta description (155 char max)
- [ ] Focus keyword in title and H2s
- [ ] At least 1500 words
- [ ] 3+ images optimized
- [ ] Internal links to 2-3 related posts
- [ ] Product cards for main products
- [ ] Email signup form at end
- [ ] Social sharing buttons
- [ ] Mobile-friendly check
- [ ] Test all affiliate links

---

## Monthly Maintenance

**First of Month:**
- [ ] Update monthly revenue tracker
- [ ] Review top-performing posts
- [ ] Plan seasonal content
- [ ] Check affiliate program status

**Weekly:**
- [ ] Publish 3 new posts
- [ ] Send newsletter
- [ ] Update deal roundups
- [ ] Schedule 105 Pinterest pins (15/day)

**Daily:**
- [ ] Check for hot deals
- [ ] Monitor comments
- [ ] Track affiliate clicks
- [ ] Engage on Pinterest

---

## Emergency Contacts

**Technical Issues:**
- GoDaddy Support: 1-480-505-8877
- WordPress Forums: wordpress.org/support

**Affiliate Issues:**
- Amazon Associates: affiliate-program.amazon.com/support
- Email: associates@amazon.com

**Legal Questions:**
- FTC Disclosure Guidelines: ftc.gov/business-guidance
- Amazon Associates Operating Agreement: Check dashboard

---

## Success Metrics to Watch

**Traffic (Google Analytics):**
- Pageviews: 50,000+/month by Month 3
- Bounce rate: <60%
- Time on page: 3+ minutes
- Pages per session: 2+

**Conversions (Amazon Associates):**
- Click-through rate: 5%+
- Conversion rate: 3%+
- Average order value: $35+

**Engagement:**
- Email open rate: 25%+
- Email click rate: 3%+
- Pinterest saves: 100+ per pin
- Comments: 5+ per post

**Revenue:**
- Month 1: $50-200
- Month 3: $500-1,000
- Month 6: $2,000-5,000
- Month 12: $5,000-10,000

---

## Pro Tips

💡 **Update old posts monthly** - Google loves fresh content

💡 **Email subscribers are 10x more valuable** than social followers

💡 **Gift guides convert best** - Create one for every occasion

💡 **Pinterest drives 80% of affiliate traffic** - Invest time here

💡 **High-ticket items = higher commissions** - Mix in $100+ products

💡 **Urgency drives clicks** - "Today only", "Limited time", "While supplies last"

💡 **Test everything** - A/B test button text, headlines, images

💡 **Mobile optimization is critical** - 70% of traffic is mobile

💡 **Build an email list first** - It's your most valuable asset

💡 **Consistency wins** - 3 posts per week beats 10 posts once

---

**Last Updated:** 2024-11-24
**Questions?** Check the full documentation or reach out!
