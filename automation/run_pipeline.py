#!/usr/bin/env python3
"""
ShelzyPerkins Affiliate Content Automation Pipeline

Main entry point for running the affiliate content automation system.

Usage:
    # Run full pipeline for a single topic
    python run_pipeline.py --title "Best Amazon Beauty Dupes" --keyword "beauty dupes" --niche beauty

    # Generate daily deals post
    python run_pipeline.py --daily-deals

    # Batch process multiple topics
    python run_pipeline.py --batch

    # Test API connections
    python run_pipeline.py --test

    # Generate content without publishing (draft mode)
    python run_pipeline.py --title "..." --keyword "..." --niche ... --draft

    # Show configuration status
    python run_pipeline.py --config
"""

import argparse
import sys
import os
from datetime import datetime

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from affiliate_system import (
    Config,
    AffiliatePipeline,
)
from affiliate_system.config import create_env_template
from affiliate_system.pipeline import create_pipeline, daily_deals_post


def main():
    parser = argparse.ArgumentParser(
        description="ShelzyPerkins Affiliate Content Automation Pipeline",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  %(prog)s --title "Best Amazon Beauty Dupes" --keyword "beauty dupes" --niche beauty
  %(prog)s --daily-deals
  %(prog)s --batch
  %(prog)s --test
  %(prog)s --config
        """
    )

    # Main operation modes
    group = parser.add_mutually_exclusive_group()
    group.add_argument(
        "--title",
        help="Post title for single post mode"
    )
    group.add_argument(
        "--daily-deals",
        action="store_true",
        help="Generate daily deals post"
    )
    group.add_argument(
        "--batch",
        action="store_true",
        help="Process batch of predefined topics"
    )
    group.add_argument(
        "--process-queued",
        action="store_true",
        help="Process all queued products in Airtable"
    )
    group.add_argument(
        "--test",
        action="store_true",
        help="Test API connections"
    )
    group.add_argument(
        "--config",
        action="store_true",
        help="Show configuration status"
    )
    group.add_argument(
        "--generate-env",
        action="store_true",
        help="Generate template .env file"
    )

    # Single post options
    parser.add_argument(
        "--keyword",
        help="Search keyword for product discovery"
    )
    parser.add_argument(
        "--niche",
        choices=["beauty", "home", "kitchen", "tech", "fashion", "fitness", "baby", "pets"],
        help="Product niche"
    )
    parser.add_argument(
        "--products",
        type=int,
        default=10,
        help="Number of products to include (default: 10)"
    )
    parser.add_argument(
        "--draft",
        action="store_true",
        help="Save as draft instead of publishing"
    )

    # Configuration
    parser.add_argument(
        "--env-file",
        default=".env",
        help="Path to .env file (default: .env)"
    )
    parser.add_argument(
        "--no-airtable",
        action="store_true",
        help="Skip Airtable operations"
    )

    args = parser.parse_args()

    # Handle special modes first
    if args.generate_env:
        print(create_env_template())
        return 0

    if args.config:
        config = Config(env_file=args.env_file)
        config.print_status()
        return 0

    if args.test:
        return test_connections(args.env_file)

    if args.daily_deals:
        return run_daily_deals(args.env_file)

    if args.batch:
        return run_batch(args.env_file)

    if args.process_queued:
        return process_queued(args.env_file)

    # Single post mode
    if args.title:
        if not args.keyword or not args.niche:
            parser.error("--title requires --keyword and --niche")

        return run_single_post(
            title=args.title,
            keyword=args.keyword,
            niche=args.niche,
            product_count=args.products,
            draft=args.draft,
            env_file=args.env_file,
            skip_airtable=args.no_airtable,
        )

    # No valid mode selected
    parser.print_help()
    return 1


def run_single_post(
    title: str,
    keyword: str,
    niche: str,
    product_count: int,
    draft: bool,
    env_file: str,
    skip_airtable: bool,
) -> int:
    """Run pipeline for a single post."""
    print("\n" + "=" * 60)
    print("ShelzyPerkins Affiliate Pipeline - Single Post")
    print("=" * 60)

    pipeline = create_pipeline(env_file)

    status = "draft" if draft else "publish"

    result = pipeline.run_full_pipeline(
        keyword=keyword,
        niche=niche,
        title=title,
        product_count=product_count,
        publish_status=status,
        save_to_airtable=not skip_airtable,
    )

    if result.success:
        print(f"\n{'='*60}")
        print("SUCCESS!")
        print(f"Post URL: {result.post_url}")
        print(f"Products: {result.products_count}")
        print(f"{'='*60}\n")
        return 0
    else:
        print(f"\n{'='*60}")
        print("FAILED")
        print(f"Error: {result.error}")
        print(f"{'='*60}\n")
        return 1


def run_daily_deals(env_file: str) -> int:
    """Generate daily deals post."""
    print("\n" + "=" * 60)
    print("ShelzyPerkins Affiliate Pipeline - Daily Deals")
    print("=" * 60)

    result = daily_deals_post(env_file)

    if result.success:
        print(f"\nDaily deals post created!")
        print(f"URL: {result.post_url}")
        return 0
    else:
        print(f"\nFailed to create daily deals post")
        print(f"Error: {result.error}")
        return 1


def run_batch(env_file: str) -> int:
    """Run batch processing for predefined topics."""
    print("\n" + "=" * 60)
    print("ShelzyPerkins Affiliate Pipeline - Batch Mode")
    print("=" * 60)

    # Define batch topics
    topics = [
        {
            "title": "Best Amazon Beauty Dupes That Actually Work",
            "keyword": "beauty dupes",
            "niche": "beauty",
            "product_count": 10,
        },
        {
            "title": "Must-Have Home Organization Products",
            "keyword": "home organization",
            "niche": "home",
            "product_count": 10,
        },
        {
            "title": "Top Kitchen Gadgets Worth Buying",
            "keyword": "kitchen gadgets",
            "niche": "kitchen",
            "product_count": 10,
        },
        {
            "title": "Best Tech Gadgets Under $50",
            "keyword": "tech gadgets under 50",
            "niche": "tech",
            "product_count": 10,
        },
        {
            "title": "Amazon Fashion Finds You'll Love",
            "keyword": "fashion finds",
            "niche": "fashion",
            "product_count": 10,
        },
    ]

    pipeline = create_pipeline(env_file)
    results = pipeline.run_batch_pipeline(topics, delay_between=30)

    # Summary
    print("\n" + "=" * 60)
    print("BATCH RESULTS")
    print("=" * 60)

    success_count = sum(1 for r in results if r.success)
    print(f"\nSuccessful: {success_count}/{len(results)}")

    for i, result in enumerate(results):
        status = "OK" if result.success else "FAILED"
        print(f"  {i+1}. {topics[i]['title'][:40]}... [{status}]")
        if result.success:
            print(f"      URL: {result.post_url}")
        else:
            print(f"      Error: {result.error}")

    return 0 if success_count == len(results) else 1


def process_queued(env_file: str) -> int:
    """Process all queued products in Airtable."""
    print("\n" + "=" * 60)
    print("ShelzyPerkins Affiliate Pipeline - Process Queued")
    print("=" * 60)

    pipeline = create_pipeline(env_file)

    if not pipeline.config.airtable.is_configured:
        print("\nError: Airtable is not configured")
        return 1

    results = pipeline.process_queued_products()

    if not results:
        print("\nNo queued products to process")
        return 0

    success_count = sum(1 for r in results if r.success)
    print(f"\nProcessed {success_count}/{len(results)} batches successfully")

    return 0 if success_count == len(results) else 1


def test_connections(env_file: str) -> int:
    """Test API connections."""
    print("\n" + "=" * 60)
    print("ShelzyPerkins Affiliate Pipeline - Connection Test")
    print("=" * 60)

    config = Config(env_file=env_file)
    config.print_status()

    pipeline = AffiliatePipeline(config)
    status = pipeline.test_connections()

    print("\nConnection Tests:")
    all_ok = True
    for service, ok in status.items():
        icon = "OK" if ok else "FAILED"
        print(f"  {service}: {icon}")
        if not ok:
            all_ok = False

    return 0 if all_ok else 1


if __name__ == "__main__":
    sys.exit(main())
