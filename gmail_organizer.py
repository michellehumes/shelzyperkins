#!/usr/bin/env python3
"""
Gmail Inbox Organizer
Automatically organize your Gmail inbox with smart labels and archiving.
"""

import os
import pickle
import re

# Allow OAuth over localhost (desktop/development use only)
os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'
from datetime import datetime, timedelta
from pathlib import Path
from typing import List, Dict, Optional

from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

# Gmail API scopes
SCOPES = ['https://www.googleapis.com/auth/gmail.modify']

# Organization rules
ORGANIZATION_RULES = {
    'Finance': {
        'keywords': ['invoice', 'receipt', 'payment', 'bill', 'statement', 'bank',
                    'transaction', 'paypal', 'venmo', 'stripe', 'refund', 'charge'],
        'domains': ['paypal.com', 'stripe.com', 'square.com', 'venmo.com',
                   'intuit.com', 'bank.com'],
        'color': '#fb4c2f',  # Red
    },
    'Shopping': {
        'keywords': ['order', 'shipped', 'tracking', 'delivery', 'package',
                    'confirmation', 'your order', 'has shipped'],
        'domains': ['amazon.com', 'ebay.com', 'etsy.com', 'shopify.com',
                   'walmart.com', 'target.com'],
        'color': '#fad165',  # Yellow
    },
    'Newsletters': {
        'keywords': ['unsubscribe', 'newsletter', 'weekly digest', 'daily briefing',
                    'update', 'roundup'],
        'headers': ['list-unsubscribe', 'precedence: bulk'],
        'color': '#a479e2',  # Purple
    },
    'Notifications': {
        'keywords': ['notification', 'alert', 'reminder', 'you have a new',
                    'mentioned you', 'commented on', 'liked your'],
        'domains': ['facebook.com', 'twitter.com', 'linkedin.com', 'instagram.com',
                   'github.com', 'slack.com', 'discord.com'],
        'from_patterns': ['noreply@', 'no-reply@', 'donotreply@', 'notifications@'],
        'color': '#16a766',  # Green
    },
    'Automated': {
        'from_patterns': ['noreply@', 'no-reply@', 'donotreply@', 'automated@',
                         'mailer-daemon@', 'postmaster@'],
        'keywords': ['automated', 'do not reply'],
        'color': '#b3bdc1',  # Gray
    },
    'Action Required': {
        'keywords': ['action required', 'please respond', 'rsvp', 'confirm',
                    'verification', 'verify your', 'expires soon', 'deadline'],
        'color': '#ff6d00',  # Orange
    }
}


class GmailOrganizer:
    """Organize Gmail inbox with smart labels and rules."""

    def __init__(self, credentials_path: str = 'credentials.json'):
        """Initialize Gmail organizer.

        Args:
            credentials_path: Path to OAuth credentials JSON file
        """
        self.credentials_path = credentials_path
        self.token_path = 'token.pickle'
        self.service = None
        self.stats = {
            'total_processed': 0,
            'labeled': {},
            'archived': 0,
            'errors': 0
        }

    def authenticate(self) -> None:
        """Authenticate with Gmail API."""
        creds = None

        # Load existing token
        if os.path.exists(self.token_path):
            with open(self.token_path, 'rb') as token:
                creds = pickle.load(token)

        # Refresh or get new credentials
        if not creds or not creds.valid:
            if creds and creds.expired and creds.refresh_token:
                creds.refresh(Request())
            else:
                if not os.path.exists(self.credentials_path):
                    raise FileNotFoundError(
                        f"Credentials file not found: {self.credentials_path}\n"
                        "Please download OAuth credentials from Google Cloud Console."
                    )
                import webbrowser
                has_browser = True
                try:
                    webbrowser.get()
                except webbrowser.Error:
                    has_browser = False

                flow = InstalledAppFlow.from_client_secrets_file(
                    self.credentials_path, SCOPES)

                if has_browser:
                    creds = flow.run_local_server(port=0)
                else:
                    # Headless: explicit redirect_uri so Google doesn't reject the request
                    redirect_uri = 'http://localhost:8080'
                    flow.redirect_uri = redirect_uri
                    auth_url, state = flow.authorization_url(
                        access_type='offline',
                        prompt='consent'
                    )
                    print("\n🌐 Open this URL in your browser:")
                    print(f"\n  {auth_url}\n")
                    print("Sign in, grant access, then copy the URL your browser")
                    print("lands on (it will show http://localhost:8080/?code=...)")
                    redirect_response = input("\nPaste that URL here: ").strip()
                    flow.fetch_token(authorization_response=redirect_response)
                    creds = flow.credentials

            # Save credentials
            with open(self.token_path, 'wb') as token:
                pickle.dump(creds, token)

        self.service = build('gmail', 'v1', credentials=creds)
        print("✓ Successfully authenticated with Gmail")

    def create_label(self, name: str, color: Optional[str] = None) -> str:
        """Create a Gmail label if it doesn't exist.

        Args:
            name: Label name
            color: Label color (hex)

        Returns:
            Label ID
        """
        try:
            # Check if label exists
            results = self.service.users().labels().list(userId='me').execute()
            labels = results.get('labels', [])

            for label in labels:
                if label['name'] == name:
                    return label['id']

            # Create new label
            label_object = {
                'name': name,
                'labelListVisibility': 'labelShow',
                'messageListVisibility': 'show'
            }

            if color:
                label_object['color'] = {
                    'backgroundColor': color,
                    'textColor': '#000000'
                }

            created_label = self.service.users().labels().create(
                userId='me',
                body=label_object
            ).execute()

            print(f"✓ Created label: {name}")
            return created_label['id']

        except HttpError as error:
            print(f"✗ Error creating label {name}: {error}")
            return None

    def get_messages(self, query: str, max_results: int = 500) -> List[Dict]:
        """Get messages matching query.

        Args:
            query: Gmail search query
            max_results: Maximum number of messages to fetch

        Returns:
            List of message objects
        """
        try:
            messages = []
            page_token = None

            while len(messages) < max_results:
                results = self.service.users().messages().list(
                    userId='me',
                    q=query,
                    maxResults=min(100, max_results - len(messages)),
                    pageToken=page_token
                ).execute()

                messages.extend(results.get('messages', []))
                page_token = results.get('nextPageToken')

                if not page_token:
                    break

            return messages[:max_results]

        except HttpError as error:
            print(f"✗ Error fetching messages: {error}")
            return []

    def get_message_details(self, msg_id: str) -> Optional[Dict]:
        """Get full message details.

        Args:
            msg_id: Message ID

        Returns:
            Message object with headers and metadata
        """
        try:
            message = self.service.users().messages().get(
                userId='me',
                id=msg_id,
                format='metadata',
                metadataHeaders=['From', 'Subject', 'List-Unsubscribe']
            ).execute()
            return message
        except HttpError as error:
            print(f"✗ Error fetching message {msg_id}: {error}")
            return None

    def categorize_message(self, message: Dict) -> List[str]:
        """Determine which categories a message belongs to.

        Args:
            message: Message object

        Returns:
            List of category names
        """
        categories = []

        # Extract headers
        headers = {h['name'].lower(): h['value'].lower()
                  for h in message.get('payload', {}).get('headers', [])}

        from_address = headers.get('from', '')
        subject = headers.get('subject', '')

        # Check each rule
        for category, rules in ORGANIZATION_RULES.items():
            matched = False

            # Check keywords in subject
            if 'keywords' in rules:
                for keyword in rules['keywords']:
                    if keyword.lower() in subject:
                        matched = True
                        break

            # Check sender domains
            if not matched and 'domains' in rules:
                for domain in rules['domains']:
                    if domain.lower() in from_address:
                        matched = True
                        break

            # Check from patterns
            if not matched and 'from_patterns' in rules:
                for pattern in rules['from_patterns']:
                    if pattern.lower() in from_address:
                        matched = True
                        break

            # Check headers
            if not matched and 'headers' in rules:
                for header_check in rules['headers']:
                    if header_check.lower() in str(headers.values()).lower():
                        matched = True
                        break

            if matched:
                categories.append(category)

        return categories

    def apply_label(self, msg_id: str, label_id: str) -> bool:
        """Apply a label to a message.

        Args:
            msg_id: Message ID
            label_id: Label ID to apply

        Returns:
            Success status
        """
        try:
            self.service.users().messages().modify(
                userId='me',
                id=msg_id,
                body={'addLabelIds': [label_id]}
            ).execute()
            return True
        except HttpError as error:
            print(f"✗ Error applying label: {error}")
            return False

    def archive_message(self, msg_id: str) -> bool:
        """Archive a message (remove from inbox).

        Args:
            msg_id: Message ID

        Returns:
            Success status
        """
        try:
            self.service.users().messages().modify(
                userId='me',
                id=msg_id,
                body={'removeLabelIds': ['INBOX']}
            ).execute()
            return True
        except HttpError as error:
            print(f"✗ Error archiving message: {error}")
            return False

    def organize_inbox(self,
                       max_emails: int = 500,
                       auto_archive: bool = True,
                       archive_categories: List[str] = None,
                       archive_days: int = 30) -> None:
        """Organize inbox with labels and archiving.

        Args:
            max_emails: Maximum emails to process
            auto_archive: Whether to auto-archive old emails
            archive_categories: Categories to auto-archive (default: Newsletters, Notifications)
            archive_days: Archive emails older than this many days
        """
        if archive_categories is None:
            archive_categories = ['Newsletters', 'Notifications', 'Automated']

        print(f"\n🚀 Starting Gmail organization...")
        print(f"Processing up to {max_emails} emails\n")

        # Create labels
        label_ids = {}
        for category, rules in ORGANIZATION_RULES.items():
            label_id = self.create_label(category, rules.get('color'))
            if label_id:
                label_ids[category] = label_id
                self.stats['labeled'][category] = 0

        # Get inbox messages
        print("\n📬 Fetching inbox messages...")
        messages = self.get_messages('in:inbox', max_results=max_emails)
        print(f"Found {len(messages)} messages to process\n")

        # Process each message
        for i, msg in enumerate(messages, 1):
            try:
                # Get message details
                details = self.get_message_details(msg['id'])
                if not details:
                    continue

                # Categorize
                categories = self.categorize_message(details)

                # Apply labels
                for category in categories:
                    if category in label_ids:
                        if self.apply_label(msg['id'], label_ids[category]):
                            self.stats['labeled'][category] += 1

                # Check if should archive
                if auto_archive and categories:
                    # Get message date
                    internal_date = int(details.get('internalDate', 0)) / 1000
                    msg_date = datetime.fromtimestamp(internal_date)
                    age_days = (datetime.now() - msg_date).days

                    # Archive if in archivable category and old enough
                    should_archive = any(cat in archive_categories for cat in categories)
                    if should_archive and age_days > archive_days:
                        if self.archive_message(msg['id']):
                            self.stats['archived'] += 1

                self.stats['total_processed'] += 1

                # Progress indicator
                if i % 50 == 0:
                    print(f"Progress: {i}/{len(messages)} messages processed...")

            except Exception as e:
                print(f"✗ Error processing message: {e}")
                self.stats['errors'] += 1

        self.print_stats()

    def print_stats(self) -> None:
        """Print organization statistics."""
        print("\n" + "="*50)
        print("📊 Organization Complete!")
        print("="*50)
        print(f"\nTotal processed: {self.stats['total_processed']}")
        print(f"Total archived: {self.stats['archived']}")
        print(f"Errors: {self.stats['errors']}")
        print("\nLabels applied:")
        for category, count in sorted(self.stats['labeled'].items()):
            if count > 0:
                print(f"  • {category}: {count}")
        print("\n")


def main():
    """Main entry point."""
    import argparse

    parser = argparse.ArgumentParser(
        description='Organize your Gmail inbox with smart labels and archiving'
    )
    parser.add_argument(
        '--max-emails',
        type=int,
        default=500,
        help='Maximum number of emails to process (default: 500)'
    )
    parser.add_argument(
        '--no-archive',
        action='store_true',
        help='Disable auto-archiving of old emails'
    )
    parser.add_argument(
        '--archive-days',
        type=int,
        default=30,
        help='Archive emails older than this many days (default: 30)'
    )
    parser.add_argument(
        '--credentials',
        default='credentials.json',
        help='Path to Google OAuth credentials file (default: credentials.json)'
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Show what would be done without making changes'
    )

    args = parser.parse_args()

    try:
        organizer = GmailOrganizer(credentials_path=args.credentials)
        organizer.authenticate()

        if args.dry_run:
            print("\n⚠️  DRY RUN MODE - No changes will be made\n")

        organizer.organize_inbox(
            max_emails=args.max_emails,
            auto_archive=not args.no_archive,
            archive_days=args.archive_days
        )

    except KeyboardInterrupt:
        print("\n\n⚠️  Operation cancelled by user")
    except Exception as e:
        print(f"\n✗ Error: {e}")
        raise


if __name__ == '__main__':
    main()
