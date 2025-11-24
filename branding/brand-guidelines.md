# ShelzyPerkins Brand Guidelines

## Brand Overview
**ShelzyPerkins** is a trusted destination for smart shoppers seeking the best Amazon deals, product recommendations, and money-saving tips. Our brand voice is friendly, authentic, and helpful—like getting advice from a savvy friend who always finds the best deals.

---

## Color Palette

### Primary Colors
| Color Name | Hex Code | RGB | Usage |
|------------|----------|-----|-------|
| Coral Red | `#FF6B6B` | 255, 107, 107 | Primary brand color, CTAs, highlights |
| Sunny Yellow | `#FFE66D` | 255, 230, 109 | Accents, deals badges, sale tags |
| Charcoal | `#2D3436` | 45, 52, 54 | Primary text, headers |

### Secondary Colors
| Color Name | Hex Code | RGB | Usage |
|------------|----------|-----|-------|
| Soft Gray | `#636E72` | 99, 110, 114 | Body text, secondary text |
| Light Gray | `#B2BEC3` | 178, 190, 195 | Borders, dividers |
| Off White | `#F8F9FA` | 248, 249, 250 | Backgrounds |
| Pure White | `#FFFFFF` | 255, 255, 255 | Cards, content areas |

### Functional Colors
| Color Name | Hex Code | Usage |
|------------|----------|-------|
| Success Green | `#00B894` | Savings, positive indicators |
| Alert Orange | `#FDCB6E` | Limited time, warnings |
| Info Blue | `#74B9FF` | Information, links |
| Error Red | `#E17055` | Errors, expired deals |

---

## Typography

### Primary Font: Poppins
- **Headlines (H1-H3):** Poppins Bold (700)
- **Subheadlines (H4-H6):** Poppins SemiBold (600)
- **Body:** Poppins Regular (400)
- **Buttons:** Poppins Medium (500)

### Font Sizes
```css
--font-size-xs: 0.75rem;    /* 12px */
--font-size-sm: 0.875rem;   /* 14px */
--font-size-base: 1rem;     /* 16px */
--font-size-lg: 1.125rem;   /* 18px */
--font-size-xl: 1.25rem;    /* 20px */
--font-size-2xl: 1.5rem;    /* 24px */
--font-size-3xl: 1.875rem;  /* 30px */
--font-size-4xl: 2.25rem;   /* 36px */
--font-size-5xl: 3rem;      /* 48px */
```

### Line Heights
- Headlines: 1.2
- Body text: 1.6
- UI elements: 1.4

---

## Logo Usage

### Clear Space
Maintain minimum clear space equal to the height of the "S" in Shelzy around all sides of the logo.

### Minimum Size
- Digital: 120px width minimum
- Print: 1 inch width minimum

### Logo Variations
1. **Full Color** - Primary use on light backgrounds
2. **White** - For use on dark or colored backgrounds
3. **Monochrome** - For single-color applications

### Don'ts
- Don't stretch or distort the logo
- Don't change the logo colors
- Don't add effects (shadows, gradients)
- Don't place on busy backgrounds
- Don't rotate the logo

---

## Button Styles

### Primary Button
```css
.btn-primary {
  background: linear-gradient(135deg, #FF6B6B 0%, #FFE66D 100%);
  color: #FFFFFF;
  font-weight: 500;
  padding: 12px 24px;
  border-radius: 8px;
  border: none;
  box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
  transition: all 0.3s ease;
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
}
```

### Secondary Button
```css
.btn-secondary {
  background: transparent;
  color: #FF6B6B;
  font-weight: 500;
  padding: 12px 24px;
  border-radius: 8px;
  border: 2px solid #FF6B6B;
  transition: all 0.3s ease;
}
.btn-secondary:hover {
  background: #FF6B6B;
  color: #FFFFFF;
}
```

### Amazon CTA Button
```css
.btn-amazon {
  background: #FF9900;
  color: #111111;
  font-weight: 600;
  padding: 14px 28px;
  border-radius: 8px;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-amazon:hover {
  background: #E88B00;
}
```

---

## UI Components

### Cards
```css
.card {
  background: #FFFFFF;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  padding: 24px;
  transition: all 0.3s ease;
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}
```

### Deal Badge
```css
.deal-badge {
  background: linear-gradient(135deg, #FF6B6B, #FFE66D);
  color: #FFFFFF;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 4px 12px;
  border-radius: 20px;
  display: inline-block;
}
```

### Price Display
```css
.price-current {
  font-size: 1.5rem;
  font-weight: 700;
  color: #00B894;
}
.price-original {
  font-size: 1rem;
  color: #B2BEC3;
  text-decoration: line-through;
}
.price-savings {
  font-size: 0.875rem;
  font-weight: 600;
  color: #FF6B6B;
}
```

---

## Voice & Tone

### Brand Personality
- **Friendly** - Like a helpful friend sharing great finds
- **Trustworthy** - Honest reviews, real opinions
- **Enthusiastic** - Genuinely excited about great deals
- **Knowledgeable** - Expert-level product insights
- **Relatable** - Understands budget-conscious shopping

### Writing Guidelines
1. Use conversational language
2. Be specific about savings and benefits
3. Create urgency without being pushy
4. Always disclose affiliate relationships
5. Use active voice
6. Keep sentences concise

### Example Headlines
- "This $24 Amazon Find Changed My Morning Routine"
- "15 Kitchen Gadgets That Are Actually Worth It"
- "Today's Hidden Gems: Deals You Almost Missed"
- "Under $50 Finds That Look Way More Expensive"

---

## Affiliate Disclosure

Always include the following disclosure on pages with affiliate links:

> **Disclosure:** This post contains affiliate links. If you purchase through these links, I may earn a small commission at no extra cost to you. I only recommend products I genuinely love and use!

---

## Social Media

### Profile Image
Use the favicon/icon mark on a gradient background.

### Cover Images
- Use brand colors as background
- Feature current deals or seasonal themes
- Include tagline: "Smart Deals • Real Savings"

### Hashtags
Primary: #ShelzyPerkins #AmazonFinds #SmartShopping
Secondary: #DealsOfTheDay #BudgetFriendly #AmazonDeals

---

## File Naming Convention
- Logo files: `shelzyperkins-logo-[variant].[format]`
- Images: `[category]-[description]-[size].[format]`
- Posts: `[date]-[title-slug]`

---

*Last Updated: November 2024*
*Version: 1.0*
