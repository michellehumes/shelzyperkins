# Gmail Inbox Organizer - Setup Guide

Automatically organize your Gmail inbox with smart labels and archiving.

## Features

✨ **Smart Auto-Labeling**
- 💰 Finance (bills, receipts, banking)
- 🛍️ Shopping (orders, tracking, deliveries)
- 📰 Newsletters (subscriptions, digests)
- 🔔 Notifications (social media, app alerts)
- 🤖 Automated (no-reply emails)
- 📋 Action Required (urgent items)

✨ **Auto-Archive**
- Archives old newsletters and notifications (30+ days by default)
- Keeps your inbox clean automatically

✨ **Pattern Recognition**
- Analyzes sender, subject, and content
- Smart categorization based on keywords and domains
- Learns from common email patterns

## Quick Start

### 1. Install Dependencies

```bash
pip install -r gmail_requirements.txt
```

### 2. Set Up Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project (or select existing one)
3. Enable the Gmail API:
   - Go to "APIs & Services" > "Library"
   - Search for "Gmail API"
   - Click "Enable"

### 3. Create OAuth Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth client ID"
3. Configure OAuth consent screen (if not already done):
   - User Type: External
   - App name: "Gmail Organizer"
   - Add your email as test user
   - Scopes: Just click Save and Continue (we'll add scopes in code)
4. Create OAuth Client ID:
   - Application type: "Desktop app"
   - Name: "Gmail Organizer"
   - Click "Create"
5. Download the credentials:
   - Click the download button (⬇️) next to your OAuth client
   - Save as `credentials.json` in the project root directory

### 4. Run the Organizer

```bash
# Basic usage - organize up to 500 emails
python gmail_organizer.py

# Process more emails
python gmail_organizer.py --max-emails 1000

# Disable auto-archiving
python gmail_organizer.py --no-archive

# Custom archive threshold
python gmail_organizer.py --archive-days 60

# Dry run (see what would happen without making changes)
python gmail_organizer.py --dry-run
```

### 5. First Run Authentication

On first run:
1. A browser window will open
2. Sign in to your Google account
3. Click "Advanced" if you see a warning (this is normal for test apps)
4. Click "Go to Gmail Organizer (unsafe)" (it's safe - it's your own app)
5. Click "Allow" to grant permissions
6. The organizer will start running

Your credentials are saved in `token.pickle` for future runs.

## Command Line Options

```bash
python gmail_organizer.py [OPTIONS]

Options:
  --max-emails N         Process up to N emails (default: 500)
  --no-archive          Don't auto-archive old emails
  --archive-days N      Archive emails older than N days (default: 30)
  --credentials PATH    Path to credentials.json (default: credentials.json)
  --dry-run            Show what would be done without making changes
  --help               Show help message
```

## Usage Examples

### Organize with default settings
```bash
python gmail_organizer.py
```

### Process entire inbox (be patient, this takes a while!)
```bash
python gmail_organizer.py --max-emails 5000
```

### Label only, don't archive anything
```bash
python gmail_organizer.py --no-archive
```

### Archive newsletters older than 7 days
```bash
python gmail_organizer.py --archive-days 7
```

### See what would happen without making changes
```bash
python gmail_organizer.py --dry-run
```

### Use custom credentials file
```bash
python gmail_organizer.py --credentials /path/to/credentials.json
```

## Organization Rules

The organizer uses smart pattern matching to categorize emails:

### 💰 Finance
- Keywords: invoice, receipt, payment, bill, statement, bank, transaction
- Domains: paypal.com, stripe.com, venmo.com, bank websites
- Auto-archive: No (important financial records)

### 🛍️ Shopping
- Keywords: order, shipped, tracking, delivery, package, confirmation
- Domains: amazon.com, ebay.com, etsy.com, shopify.com
- Auto-archive: No (you may need tracking info)

### 📰 Newsletters
- Keywords: newsletter, unsubscribe, weekly digest, daily briefing
- Headers: list-unsubscribe, precedence: bulk
- Auto-archive: Yes (after 30 days)

### 🔔 Notifications
- Keywords: notification, alert, mentioned you, commented on
- Domains: facebook.com, twitter.com, linkedin.com, github.com
- From: notifications@, noreply@
- Auto-archive: Yes (after 30 days)

### 🤖 Automated
- From patterns: noreply@, no-reply@, donotreply@, mailer-daemon@
- Keywords: automated, do not reply
- Auto-archive: Yes (after 30 days)

### 📋 Action Required
- Keywords: action required, please respond, RSVP, confirm, verification, expires soon
- Auto-archive: No (needs your attention)

## Customization

Want to customize the rules? Edit the `ORGANIZATION_RULES` dictionary in `gmail_organizer.py`:

```python
ORGANIZATION_RULES = {
    'Your Category': {
        'keywords': ['word1', 'word2'],
        'domains': ['example.com'],
        'from_patterns': ['sender@'],
        'color': '#ff0000',  # Hex color code
    }
}
```

## Troubleshooting

### "Credentials file not found"
- Make sure `credentials.json` is in the same directory as the script
- Or use `--credentials /path/to/credentials.json`

### "Invalid grant" or authentication errors
- Delete `token.pickle` and run again
- You may need to re-authenticate

### "Access blocked: Gmail Organizer has not completed the Google verification process"
- This is normal for personal projects
- Click "Advanced" and "Go to Gmail Organizer (unsafe)"
- This is YOUR app, so it's safe

### "Insufficient Permission"
- Make sure you granted all requested permissions during OAuth
- Delete `token.pickle` and re-authenticate

### Slow performance
- Gmail API has rate limits
- Process fewer emails at a time: `--max-emails 100`
- Run periodically (e.g., daily) instead of processing thousands at once

## Automation

### Run Daily with Cron

Add to your crontab (`crontab -e`):

```bash
# Run every day at 8 AM
0 8 * * * cd /home/user/shelzyperkins && python gmail_organizer.py --max-emails 200
```

### Run Weekly for Deep Cleaning

```bash
# Run every Sunday at 9 AM
0 9 * * 0 cd /home/user/shelzyperkins && python gmail_organizer.py --max-emails 1000
```

## Privacy & Security

- Your credentials are stored locally in `credentials.json` and `token.pickle`
- No data is sent anywhere except to Google's Gmail API
- The script only reads email metadata (sender, subject, date)
- It does NOT read email content/body
- You can revoke access anytime at https://myaccount.google.com/permissions

## Tips for Best Results

1. **Start with a dry run**: Use `--dry-run` to see what will happen
2. **Process incrementally**: Start with 100-500 emails, not thousands
3. **Run regularly**: Daily or weekly runs keep your inbox clean
4. **Customize rules**: Edit the rules to match your email patterns
5. **Manual review**: Review the auto-created labels and adjust as needed
6. **Unsubscribe**: Use this as an opportunity to unsubscribe from unwanted newsletters

## What Gets Archived?

By default, only these categories are auto-archived after 30 days:
- Newsletters
- Notifications
- Automated emails

Financial and shopping emails are NEVER auto-archived to preserve important records.

## Support

Having issues? Check:
1. Python version (3.7+ required)
2. Dependencies installed: `pip install -r gmail_requirements.txt`
3. Valid `credentials.json` from Google Cloud Console
4. Gmail API is enabled in your Google Cloud project

---

**Happy organizing!** 📧✨
