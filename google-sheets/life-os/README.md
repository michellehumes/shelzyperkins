# Life OS - Google Sheets Auto-Build System

A complete personal operating system built in Google Sheets with automated API integrations.

## Features

### 12 Interconnected Tabs

| Tab | Purpose | Key Features |
|-----|---------|--------------|
| **DASHBOARD** | Overview & quick metrics | Sparklines, live calculations, today's focus |
| **GOALS** | Annual/quarterly goals | Progress tracking, key results, status |
| **PROJECTS** | Active project management | Linked to goals, priorities, next actions |
| **HABITS** | Daily habit tracker | Weekly grid, streaks, completion rates |
| **HEALTH** | Health & wellness data | Oura Ring sync, mood, energy tracking |
| **FINANCES** | Income & expense tracking | Shopify sync, categorization, summaries |
| **RELATIONSHIPS** | Contact management | Circles, contact frequency, birthdays |
| **LEARNING** | Books, courses, skills | Progress, ratings, key takeaways |
| **CONTENT** | Content calendar | Multi-platform, engagement metrics |
| **INTEL** | AI-powered insights | Weekly analysis, patterns, predictions |
| **JOURNAL** | Daily reflection | Gratitude, wins, lessons, mood tracking |
| **ARCHIVE** | Completed items | Historical record with outcomes |
| **SETTINGS** | Configuration | API keys, automation settings |

### Automation Features

- **Oura Ring Integration**: Daily health data sync
- **Shopify Integration**: Automatic revenue tracking
- **Weekly AI Reports**: Optional Claude/OpenAI analysis
- **Custom Menu**: Quick actions for common tasks

## Quick Start

### Step 1: Create the Sheet

1. Go to [Google Sheets](https://sheets.google.com)
2. Create a new blank spreadsheet
3. Name it "Life OS" (or your preferred name)

### Step 2: Add the Script

1. Click **Extensions** > **Apps Script**
2. Delete any existing code in the editor
3. Copy the entire contents of `LifeOS-AutoBuild.gs`
4. Paste into the Apps Script editor
5. Press **Ctrl+S** (or Cmd+S) to save
6. Name the project "Life OS"

### Step 3: Build Your Life OS

1. In the Apps Script editor, select `buildLifeOS` from the function dropdown
2. Click the **Run** button (play icon)
3. Click **Review permissions** when prompted
4. Choose your Google account
5. Click **Advanced** > **Go to Life OS (unsafe)**
6. Click **Allow**
7. Wait 30-60 seconds for the build to complete

### Step 4: Configure Integrations (Optional)

Go to the **SETTINGS** tab to add your API keys:

#### Oura Ring
1. Visit [cloud.ouraring.com/personal-access-tokens](https://cloud.ouraring.com/personal-access-tokens)
2. Create a new Personal Access Token
3. Paste it in the Settings tab

#### Shopify
1. Go to your Shopify Admin > Settings > Apps and sales channels
2. Click **Develop apps** > **Create an app**
3. Configure Admin API scopes: `read_orders`
4. Install the app and copy credentials to Settings

#### AI Insights (Optional)
Add a Claude or OpenAI API key for weekly intelligent analysis.

### Step 5: Enable Automation

Run `setupAutomation()` from the Life OS menu to enable:
- Daily Oura sync at 6:00 AM
- Daily Shopify sync at midnight
- Weekly report generation on Sundays

## Tab Details

### Dashboard
The central hub showing:
- Quick stats (goals, projects, streaks)
- Financial snapshot (MTD revenue, expenses, net)
- Today's focus items
- Habits checklist
- Weekly progress sparkline

### Goals
Track annual and quarterly objectives:
- Categories: Business, Health, Personal, Financial, Relationships, Learning, Creative
- Timeframes: Q1-Q4 2026, Annual, 3-Year, 5-Year
- Progress percentage with visual indicators
- Key results tracking

### Projects
Manage active initiatives:
- Link projects to parent goals
- Priority levels: Critical, High, Medium, Low
- Status: Planning, Active, On Hold, Completed
- Next action tracking

### Habits
Daily habit tracking grid:
- Customizable habits with cues and rewards
- Weekly completion checkboxes
- Automatic streak calculation
- Category-based organization

### Health
Comprehensive wellness tracking:
- Auto-populated from Oura Ring
- Manual entries for weight, mood, energy
- Conditional formatting for score ranges
- Historical trend visibility

### Finances
Complete financial picture:
- Income and expense categorization
- Source tracking (Shopify, Amazon, etc.)
- Recurring transaction flagging
- Running totals and summaries

### Relationships
Personal CRM:
- Circle system (1-5) for prioritization
- Contact frequency reminders
- Birthday tracking
- Next action prompts

### Learning
Knowledge management:
- Books, courses, podcasts, workshops
- Progress tracking
- 5-star rating system
- Key takeaways capture

### Content
Content calendar:
- Multi-platform support
- Status pipeline: Idea > Draft > Review > Scheduled > Published
- Engagement metrics
- Revenue attribution

### Intel
AI-powered insights:
- Weekly analysis summaries
- Pattern detection across data
- Predictions and recommendations
- Trend comparisons

### Journal
Daily reflection:
- Morning intention setting
- 3 gratitudes practice
- Win/challenge documentation
- Lessons learned capture

### Archive
Historical record:
- Completed goals and projects
- Outcomes documentation
- Key learnings preservation
- Searchable history

## Customization

### Adding New Tabs
1. Duplicate an existing tab structure
2. Modify headers and data validation
3. Update Dashboard formulas to reference new tab

### Modifying Categories
Edit the data validation lists in the Apps Script:
```javascript
const categoryRule = SpreadsheetApp.newDataValidation()
  .requireValueInList(['Your', 'Custom', 'Categories'], true)
  .build();
```

### Changing Colors
Modify the `CONFIG.colors` object at the top of the script.

## Troubleshooting

### Script Authorization
If you see "This app isn't verified":
1. Click **Advanced**
2. Click **Go to Life OS (unsafe)**
3. Click **Allow**

### Trigger Errors
Check execution logs:
1. Apps Script editor > **Executions** (left sidebar)
2. Review any failed runs
3. Common issues: expired API tokens, rate limits

### Data Not Syncing
1. Verify API keys in Settings tab
2. Check API token hasn't expired
3. Run sync manually to see error messages

## Support

For issues with this Life OS system:
- Check the [ShelzyPerkins GitHub Issues](https://github.com/shelzyperkins/shelzyperkins/issues)
- Review Google Apps Script [documentation](https://developers.google.com/apps-script)

## Version History

- **1.0.0** (January 2026): Initial release with all 12 tabs, Oura/Shopify integration, AI insights
