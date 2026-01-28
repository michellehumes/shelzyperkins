# Gmail Organizer - Quick Start 🚀

Get your Gmail inbox organized in 3 simple steps!

## Step 1: Install Dependencies

```bash
pip install -r gmail_requirements.txt
```

## Step 2: Get Google Credentials

1. Go to https://console.cloud.google.com/
2. Create a new project
3. Enable the **Gmail API**
4. Create **OAuth Client ID** credentials (Desktop app)
5. Download and save as `credentials.json` in this directory

**Need help?** See [GMAIL_ORGANIZER_SETUP.md](GMAIL_ORGANIZER_SETUP.md) for detailed instructions with screenshots.

## Step 3: Run the Organizer

```bash
./organize-gmail
```

That's it! 🎉

## What It Does

- 📧 Creates smart labels: Finance, Shopping, Newsletters, Notifications, etc.
- 🏷️ Auto-labels your emails based on content and sender
- 🗄️ Archives old newsletters and notifications (30+ days)
- ✨ Keeps your inbox clean and organized

## First Run

On your first run:
1. A browser opens asking you to sign in to Google
2. Grant permissions (it's your own app, totally safe!)
3. The organizer starts working automatically

## Common Commands

```bash
# Basic - organize 500 emails
./organize-gmail

# Organize more emails
./organize-gmail --max-emails 1000

# Just label, don't archive
./organize-gmail --no-archive

# See what would happen (no changes made)
./organize-gmail --dry-run

# Process older emails (90+ days)
./organize-gmail --archive-days 90
```

## Results You'll See

```
🚀 Starting Gmail organization...
Processing up to 500 emails

✓ Created label: Finance
✓ Created label: Shopping
✓ Created label: Newsletters
✓ Created label: Notifications

📬 Fetching inbox messages...
Found 487 messages to process

Progress: 50/487 messages processed...
Progress: 100/487 messages processed...
...

==================================================
📊 Organization Complete!
==================================================

Total processed: 487
Total archived: 156
Errors: 0

Labels applied:
  • Finance: 23
  • Shopping: 45
  • Newsletters: 189
  • Notifications: 167
  • Automated: 63
```

## Run Automatically

Want to keep your inbox organized daily? Add to crontab:

```bash
# Open crontab editor
crontab -e

# Add this line to run every morning at 8 AM
0 8 * * * cd /home/user/shelzyperkins && ./organize-gmail --max-emails 200
```

## Troubleshooting

**"credentials.json not found"**
- Follow Step 2 above to download credentials from Google Cloud Console

**"Authentication failed"**
- Delete `token.pickle` and run again
- Make sure you're using the correct Google account

**Need more help?**
- Full setup guide: [GMAIL_ORGANIZER_SETUP.md](GMAIL_ORGANIZER_SETUP.md)
- Organization rules and customization details inside

---

**Made with ❤️ to help you achieve inbox zero!**
