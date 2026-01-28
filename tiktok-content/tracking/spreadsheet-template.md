# Content Tracking Spreadsheet

Use this Google Sheets template to track all your TikTok content and orders.

---

## Sheet 1: Video Tracking

Copy these headers into Google Sheets:

| Column | Header | Description |
|--------|--------|-------------|
| A | Video # | Video number (1, 2, 3...) |
| B | Title | Short title for reference |
| C | Format | tutorial/asmr/reveal/etc |
| D | Status | Filmed/Edited/Posted/Scheduled |
| E | Film Date | Date you filmed it |
| F | Post Date | Date posted or scheduled |
| G | Post Time | Time posted |
| H | Views | Update weekly |
| I | Likes | Update weekly |
| J | Comments | Update weekly |
| K | Shares | Update weekly |
| L | Orders | Orders attributed to this video |
| M | Notes | Any notes |

### Sample Data:

```
Video #, Title, Format, Status, Film Date, Post Date, Post Time, Views, Likes, Comments, Shares, Orders, Notes
1, Tutorial - How I Make These, tutorial, Posted, 1/28/26, 1/29/26, 12pm, 5420, 342, 87, 23, 3, Good engagement
2, Supplies Haul, supplies, Posted, 1/28/26, 2/2/26, 3pm, 2100, 156, 34, 12, 1, Need better lighting
3, ASMR Cutting, asmr, Posted, 1/28/26, 1/28/26, 7pm, 12500, 890, 156, 67, 8, WENT VIRAL!
```

---

## Sheet 2: Order Tracking

Copy these headers:

| Column | Header | Description |
|--------|--------|-------------|
| A | Order # | Sequential order number |
| B | Order Date | Date received |
| C | Customer Name | Customer's name |
| D | Bottle Name | Name to put on bottle |
| E | Color | Vinyl color chosen |
| F | Address | Shipping address |
| G | Email | Customer email |
| H | Phone | Customer phone |
| I | Payment Status | Paid/Pending |
| J | Payment Method | Venmo/PayPal/CashApp |
| K | Production Status | Not Started/In Progress/Done |
| L | Ship Date | Date shipped |
| M | Tracking # | Shipping tracking number |
| N | Price | Amount charged |
| O | Source | Which TikTok video |

### Sample Data:

```
Order #, Order Date, Customer Name, Bottle Name, Color, Address, Email, Phone, Payment, Method, Status, Ship Date, Tracking, Price, Source
001, 1/28/26, Sarah Johnson, Sarah, Hot Pink, 123 Main St..., sarah@email.com, 555-1234, Paid, Venmo, Done, 1/30/26, 1Z999..., $30, Video 3
002, 1/29/26, Jessica Smith, Jess, Gold, 456 Oak Ave..., jess@email.com, 555-5678, Paid, PayPal, In Progress, , , $30, Video 1
```

---

## Sheet 3: Weekly Analytics

Track weekly performance:

| Column | Header |
|--------|--------|
| A | Week Of |
| B | Videos Posted |
| C | Total Views |
| D | Total Likes |
| E | Total Comments |
| F | New Followers |
| G | Orders |
| H | Revenue |
| I | Best Performing Video |
| J | Notes |

---

## Sheet 4: Inventory

Track your supplies:

| Column | Header |
|--------|--------|
| A | Item |
| B | Quantity |
| C | Reorder At |
| D | Supplier |
| E | Cost |
| F | Last Ordered |

### Suggested Items:

```
Item, Quantity, Reorder At, Supplier, Cost, Last Ordered
White Flip-Top Bottles, 20, 5, Amazon, $12/ea, 1/15/26
Hot Pink Vinyl Roll, 3, 1, Cricut, $8/roll, 1/10/26
Baby Pink Vinyl Roll, 2, 1, Cricut, $8/roll, 1/10/26
Gold Vinyl Roll, 2, 1, Cricut, $8/roll, 1/10/26
Rose Gold Vinyl Roll, 2, 1, Cricut, $8/roll, 1/10/26
Teal Vinyl Roll, 2, 1, Cricut, $8/roll, 1/10/26
Black Vinyl Roll, 2, 1, Cricut, $8/roll, 1/10/26
Transfer Tape, 1, 0, Amazon, $15/roll, 1/10/26
Weeding Tools, 1 set, 0, Amazon, $10, 12/1/25
Shipping Boxes, 25, 10, Amazon, $0.75/ea, 1/20/26
Tissue Paper, 50, 20, Dollar Tree, $1/pack, 1/20/26
```

---

## Sheet 5: Content Ideas

Bank future video ideas:

| Column | Header |
|--------|--------|
| A | Idea |
| B | Format |
| C | Hook |
| D | Priority |
| E | Status |
| F | Notes |

### Starter Ideas:

```
Idea, Format, Hook, Priority, Status, Notes
Different fonts comparison, tutorial, "fonts that hit different", High, Not Started, Show 5 fonts
Troubleshooting bubbles, tutorial, "if this happens to you", Medium, Not Started, Common problem
Color trends for spring, reveal, "spring colors are here", High, Not Started, Time-sensitive
Customer reactions compilation, social proof, "their reactions", High, Need Content, Ask for permission
Behind the scenes workspace, small_biz, "my setup tour", Low, Not Started,
Mistakes I made starting out, tutorial, "don't do what I did", Medium, Not Started, Relatable
How I price my bottles, small_biz, "the math is mathing", High, Not Started, Shows value
Packaging evolution, small_biz, "glow up", Low, Not Started,
```

---

## Google Sheets Setup

1. Go to [sheets.google.com](https://sheets.google.com)
2. Click **Blank** to create new spreadsheet
3. Rename to "TikTok Water Bottle Business"
4. Create 5 tabs at bottom (rename by double-clicking):
   - Video Tracking
   - Order Tracking
   - Weekly Analytics
   - Inventory
   - Content Ideas
5. Copy headers from above into each sheet
6. Format as needed (freeze row 1, add filters)

---

## Formulas to Add

### Video Tracking - Engagement Rate (Column N)
```
=IF(H2>0,(I2+J2+K2)/H2*100,0)
```

### Order Tracking - Total Revenue (Bottom of Column N)
```
=SUM(N2:N100)
```

### Weekly Analytics - Average Views per Video
```
=IF(B2>0,C2/B2,0)
```

---

## Color Coding Suggestions

### Status Columns:
- **Not Started** = Red
- **In Progress** = Yellow
- **Done/Posted** = Green
- **Scheduled** = Blue

### Payment Status:
- **Pending** = Yellow
- **Paid** = Green

---

## Pro Tips

1. **Update daily**: Add new orders immediately
2. **Update weekly**: Analytics and video performance
3. **Review monthly**: What's working, what's not
4. **Backup**: Download as Excel monthly
5. **Share access**: If you have help, share specific sheets only
