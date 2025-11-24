# Site Speed Optimization Guide for ShelzyPerkins

## Critical CSS

Add this to the `<head>` before the main stylesheet loads:

```css
/* Critical CSS - Above the fold styles */
:root{--sp-coral:#FF6B6B;--sp-yellow:#FFE66D;--sp-charcoal:#2D3436;--sp-white:#FFFFFF;--sp-off-white:#F8F9FA}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',sans-serif;background:#F8F9FA;color:#2D3436}
.sp-header{background:#fff;position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.sp-header__inner{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;max-width:1400px;margin:0 auto}
.sp-hero{background:linear-gradient(135deg,#FF6B6B,#FFE66D);padding:3rem 0;text-align:center;color:#fff}
.sp-container{max-width:1200px;margin:0 auto;padding:0 1.5rem}
h1,h2,h3{font-weight:700;line-height:1.2}
```

## Image Optimization

### WebP Conversion
All images should be served as WebP with fallbacks:

```html
<picture>
  <source srcset="image.webp" type="image/webp">
  <source srcset="image.jpg" type="image/jpeg">
  <img src="image.jpg" alt="Description" loading="lazy">
</picture>
```

### Responsive Images
```html
<img
  srcset="image-400.jpg 400w,
          image-800.jpg 800w,
          image-1200.jpg 1200w"
  sizes="(max-width: 600px) 400px,
         (max-width: 900px) 800px,
         1200px"
  src="image-800.jpg"
  alt="Description"
  loading="lazy"
>
```

### Image Size Guidelines
- Card images: 400x400px
- Featured images: 1200x630px
- Pinterest images: 735x1102px
- Product images: 500x500px max

## Lazy Loading

### Native Lazy Loading
```html
<img src="image.jpg" loading="lazy" alt="Description">
<iframe src="video.html" loading="lazy"></iframe>
```

### JavaScript Lazy Loading (for browsers without native support)
```javascript
// In main.js - already included in theme
if ('loading' in HTMLImageElement.prototype) {
  // Native lazy loading supported
} else {
  // Polyfill with Intersection Observer
}
```

## Caching Rules

### .htaccess Configuration
```apache
# Enable compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser caching
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
  ExpiresByType text/html "access plus 1 hour"
</IfModule>

# Keep-Alive
<IfModule mod_headers.c>
  Header set Connection keep-alive
</IfModule>
```

## Plugin Configuration

### LiteSpeed Cache Settings
```json
{
  "cache": {
    "enabled": true,
    "ttl_pub": 604800,
    "ttl_priv": 1800,
    "ttl_front": 604800,
    "cache_mobile": true,
    "cache_login": false
  },
  "purge": {
    "post_upgrade": true,
    "auto_purge": true
  },
  "optimization": {
    "css_minify": true,
    "css_combine": true,
    "js_minify": true,
    "js_combine": false,
    "html_minify": true,
    "lazy_load_imgs": true,
    "webp_replace": true
  }
}
```

## CDN Recommendations

### Cloudflare (Free Tier)
1. Sign up at cloudflare.com
2. Add your domain
3. Update nameservers
4. Enable these features:
   - Auto Minify (JS, CSS, HTML)
   - Brotli compression
   - Browser Cache TTL: 4 hours
   - Always Use HTTPS
   - Rocket Loader (test first)

### Alternative: BunnyCDN
- $0.01/GB - very affordable
- Great for image-heavy sites
- Easy WordPress integration

## Database Optimization

### Weekly Maintenance Tasks
1. Delete spam comments
2. Delete post revisions (keep last 3)
3. Delete transients
4. Optimize database tables

### Plugin: WP-Optimize
```json
{
  "scheduled_optimization": "weekly",
  "retain_revisions": 3,
  "remove_spam": true,
  "remove_transients": true,
  "optimize_tables": true
}
```

## Performance Testing

### Tools to Use
1. **Google PageSpeed Insights** - Core Web Vitals
2. **GTmetrix** - Detailed waterfall analysis
3. **WebPageTest** - Real device testing
4. **Chrome DevTools** - Lighthouse audit

### Target Metrics
| Metric | Target | Priority |
|--------|--------|----------|
| LCP | < 2.5s | High |
| FID | < 100ms | High |
| CLS | < 0.1 | High |
| TTFB | < 600ms | Medium |
| Speed Index | < 3.4s | Medium |

## Quick Wins Checklist

- [ ] Enable caching plugin
- [ ] Compress all images
- [ ] Convert to WebP format
- [ ] Enable lazy loading
- [ ] Minify CSS/JS
- [ ] Use CDN
- [ ] Optimize database
- [ ] Remove unused plugins
- [ ] Use lightweight theme (done!)
- [ ] Limit external fonts

## Monitoring

### Set Up Uptime Monitoring
- UptimeRobot (free)
- Pingdom (free tier)

### Speed Monitoring
- Weekly PageSpeed checks
- Track Core Web Vitals in Search Console
