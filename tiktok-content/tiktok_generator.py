#!/usr/bin/env python3
"""
TikTok Faceless Content Generator for Personalized Water Bottles.

Generates video scripts, captions, voiceovers, and posting schedules
for faceless TikTok content promoting personalized water bottle business.

Usage:
    python tiktok_generator.py --type tutorial --output video-scripts/
    python tiktok_generator.py --type weekly --count 7
    python tiktok_generator.py --type batch --count 10
"""

import argparse
import json
import random
from datetime import datetime, timedelta
from dataclasses import dataclass, asdict
from typing import List, Optional
from pathlib import Path


# ===========================================
# Data Classes
# ===========================================

@dataclass
class VideoScript:
    """Complete video script with all elements."""
    video_number: int
    title: str
    format_type: str  # tutorial, asmr, meme, reveal, engagement, small_biz, pov
    hook: str
    filming_checklist: List[str]
    what_to_film: List[dict]
    voiceover_script: str
    text_overlays: List[dict]
    caption: str
    hashtags: List[str]
    sound_suggestions: List[str]
    post_time: str
    pro_tips: List[str]


@dataclass
class ContentCalendar:
    """Weekly content calendar."""
    week_number: int
    start_date: str
    videos: List[dict]


# ===========================================
# Content Templates
# ===========================================

HOOKS = {
    "tutorial": [
        "the girlies asked how I make these",
        "tutorial since y'all keep asking",
        "how I make personalized water bottles",
        "step by step because you asked",
        "finally showing my process",
        "beginner friendly tutorial",
        "the easiest way to make these",
    ],
    "asmr": [
        "the most satisfying part",
        "I could watch this all day",
        "cricut ASMR hits different",
        "POV: your order is being made",
        "watch me make your bottle",
        "oddly satisfying cricut cutting",
    ],
    "meme": [
        "if no one got me...",
        "me: I don't need more bottles",
        "my bank account watching me",
        "nobody: ... me:",
        "the way I gasped",
        "it's giving main character",
    ],
    "reveal": [
        "FIRST LOOK",
        "new drop alert",
        "sneak peek at new colors",
        "before you ask, yes these are available",
        "couldn't wait to show you",
    ],
    "engagement": [
        "which color are you?",
        "comment your name",
        "pick your personality",
        "tag someone who needs this",
        "this or that?",
    ],
    "small_biz": [
        "small business check",
        "things I made this week",
        "a week in my small business",
        "packing orders hits different",
        "POV: you're a small business owner",
    ],
    "pov": [
        "POV: you made bottles for the whole friend group",
        "POV: you take themes too seriously",
        "POV: you're the gift giving friend",
        "when you love personalized everything",
    ],
}

HASHTAG_SETS = {
    "tutorial": [
        "#cricuttutorial", "#personalizedwaterbottle", "#diytiktok",
        "#cricutprojects", "#vinylcrafts", "#howtomake", "#cricutforbeginners"
    ],
    "product": [
        "#personalizedwaterbottle", "#customgifts", "#aesthetic",
        "#giftideas", "#shopsmall", "#handmade", "#customdrinkware"
    ],
    "small_biz": [
        "#smallbusinesscheck", "#packingorders", "#cricutbusiness",
        "#smallbusinesstiktok", "#entrepreneur", "#sidehustle", "#shopsmall"
    ],
    "asmr": [
        "#cricutasmr", "#satisfyingvideos", "#oddlysatisfying",
        "#asmr", "#vinylcutting", "#cricuttok", "#satisfying"
    ],
    "engagement": [
        "#fyp", "#viral", "#aesthetic", "#coloraesthetic",
        "#personalizedgifts", "#waterbottle", "#pickone"
    ],
}

VOICEOVER_TEMPLATES = {
    "tutorial": [
        "So everyone keeps asking how I make these, and honestly it's so easy. Download the {font} font, type your name, size it to {size}, and send it to your Cricut. Use permanent outdoor vinyl so it's dishwasher safe. That's literally it.",
        "Here's the tutorial you've been asking for. The font is {font} from dafont.com, it's free. Size your name to about {size}, cut it on permanent vinyl, and apply with transfer tape. Simple as that.",
        "Okay let me show you my exact process. First, I use {font} font. Size it to {size}. Cut on permanent outdoor vinyl. Weed the letters. Apply with transfer tape. Done.",
    ],
    "supplies": [
        "Here's everything you need. White flip-top water bottle, {price1}. Permanent outdoor vinyl in whatever colors you want. Transfer tape. Weeding tools. And a Cricut. Total cost per bottle is around {cost}, sell for {sell_price}.",
    ],
    "weeding": [
        "Weeding is honestly so therapeutic once you get the hang of it. The key is to go slow, especially on cursive letters. Apply transfer tape, smooth it down, line it up on your bottle, burnish it, and peel back slowly. That's the satisfying part.",
    ],
    "small_biz": [
        "Here's everything I made this week. {count} personalized water bottles going out to their new homes. Still can't believe this is my job.",
        "A week in my small business. {count} orders packed and shipped. Living the dream honestly.",
    ],
    "reply": [
        "Yes I sell these! {price} dollars for a custom bottle. You pick your name, pick your color, and I ship in three to five days. DM me to order.",
    ],
}

FILMING_CHECKLISTS = {
    "tutorial": [
        "Screen record Canva or Cricut Design Space",
        "Type name in chosen font",
        "Show sizing to 2 inches",
        "Cut to Cricut machine cutting",
    ],
    "asmr": [
        "Phone on tripod, close-up angle",
        "Good lighting on Cricut mat",
        "Film entire cutting process",
        "Capture blade movement and sound",
    ],
    "supplies": [
        "Clean white/marble surface",
        "Good overhead lighting",
        "Film hands placing each item",
        "Price tags visible or add in editing",
    ],
    "reveal": [
        "Line up bottles with different names",
        "Cloth or fabric to cover initially",
        "Good lighting",
        "Clean background",
    ],
    "weeding": [
        "Overhead angle of workspace",
        "Good lighting on vinyl",
        "Clean, manicured nails (optional)",
        "Film entire process",
    ],
    "small_biz": [
        "Film snippets throughout the week",
        "Show different stages of production",
        "Include packaging materials",
        "Aesthetic workspace setup",
    ],
    "engagement": [
        "Same name in different vinyl colors",
        "Clean background",
        "Quick cuts between colors",
        "Good lighting to show true colors",
    ],
    "pov": [
        "Multiple finished bottles with different names",
        "Pretty display setup",
        "Cute tote bag or packaging",
        "Aesthetic vibes throughout",
    ],
}

SOUND_SUGGESTIONS = {
    "tutorial": ["trending lo-fi beat", "chill tutorial sound", "original audio"],
    "asmr": ["original Cricut sounds", "lo-fi beat", "no sound - pure ASMR"],
    "meme": ["Don't Blame Me - Taylor Swift", "emotional trending sound", "dramatic audio"],
    "reveal": ["Came Here for Love - Sigala", "dramatic reveal sound", "tropical beat"],
    "engagement": ["Pick a Side sound", "this or that audio", "trending choice sound"],
    "small_biz": ["motivational trending sound", "aesthetic beat", "upbeat audio"],
    "pov": ["Love Story - Taylor Swift", "friendship audio", "relatable sound"],
}

NAMES = [
    "Emma", "Olivia", "Ava", "Sophia", "Isabella", "Mia", "Charlotte", "Amelia",
    "Harper", "Evelyn", "Sarah", "Jessica", "Ashley", "Emily", "Madison",
    "Bella", "Lily", "Chloe", "Grace", "Zoe", "Hannah", "Natalie", "Samantha",
    "Jess", "Liv", "Kate", "Anna", "Leah", "Maya", "Ella", "Aria", "Luna",
]

COLORS = [
    ("Hot Pink", "Bold", "The Bold One"),
    ("Baby Pink", "Soft Girl Era", "The Soft Girl"),
    ("Gold", "Main Character Energy", "The Classy One"),
    ("Rose Gold", "Hopeless Romantic", "The Romantic"),
    ("Teal", "The Creative One", "Unique Vibes"),
    ("Black", "Mysterious Vibes", "The Edgy One"),
]

FONTS = ["Pecita", "Playlist Script", "Adelicia Script", "Hello Honey"]

POST_TIMES = ["6pm", "7pm", "8pm", "12pm", "3pm"]


# ===========================================
# Generator Functions
# ===========================================

def generate_video_script(
    video_number: int,
    format_type: str,
    custom_hook: str = None,
) -> VideoScript:
    """Generate a complete video script."""

    # Select hook
    hook = custom_hook or random.choice(HOOKS.get(format_type, HOOKS["tutorial"]))

    # Get filming checklist
    checklist_key = format_type if format_type in FILMING_CHECKLISTS else "tutorial"
    filming_checklist = FILMING_CHECKLISTS[checklist_key]

    # Generate what to film
    what_to_film = generate_filming_shots(format_type)

    # Generate voiceover
    voiceover = generate_voiceover(format_type)

    # Generate text overlays
    text_overlays = generate_text_overlays(format_type, hook)

    # Select hashtags
    hashtag_key = format_type if format_type in HASHTAG_SETS else "product"
    hashtags = HASHTAG_SETS[hashtag_key]

    # Generate caption
    caption = generate_caption(format_type, hook, hashtags)

    # Get sound suggestions
    sounds = SOUND_SUGGESTIONS.get(format_type, SOUND_SUGGESTIONS["tutorial"])

    # Generate title
    title = generate_title(format_type, hook)

    # Select post time
    post_time = random.choice(POST_TIMES)

    # Generate pro tips
    pro_tips = generate_pro_tips(format_type)

    return VideoScript(
        video_number=video_number,
        title=title,
        format_type=format_type,
        hook=hook,
        filming_checklist=filming_checklist,
        what_to_film=what_to_film,
        voiceover_script=voiceover,
        text_overlays=text_overlays,
        caption=caption,
        hashtags=hashtags,
        sound_suggestions=sounds,
        post_time=post_time,
        pro_tips=pro_tips,
    )


def generate_filming_shots(format_type: str) -> List[dict]:
    """Generate list of shots to film."""
    shots = {
        "tutorial": [
            {"shot": "Screen showing Canva/Design Space", "duration": "3 sec"},
            {"shot": "Typing name in font", "duration": "3 sec"},
            {"shot": "Adjusting size to 2 inches", "duration": "2 sec"},
            {"shot": "Cricut cutting the vinyl", "duration": "5 sec"},
            {"shot": "Final result", "duration": "2 sec"},
        ],
        "asmr": [
            {"shot": "Loading mat into Cricut", "duration": "3 sec"},
            {"shot": "Close-up of blade cutting (speed up)", "duration": "5 sec"},
            {"shot": "Progress circle showing 60%", "duration": "2 sec"},
            {"shot": "Peeling vinyl off mat - THE MONEY SHOT", "duration": "5 sec"},
        ],
        "supplies": [
            {"shot": "Water bottle placed on surface", "duration": "2 sec"},
            {"shot": "Vinyl rolls (show 3 colors)", "duration": "3 sec"},
            {"shot": "Transfer tape roll", "duration": "2 sec"},
            {"shot": "Weeding tools", "duration": "2 sec"},
            {"shot": "Pan to Cricut machine", "duration": "2 sec"},
            {"shot": "Final shot of finished bottle", "duration": "3 sec"},
        ],
        "reveal": [
            {"shot": "Covered bottles (hidden)", "duration": "2 sec"},
            {"shot": "Pull away cloth dramatically", "duration": "2 sec"},
            {"shot": "Pan across all bottles", "duration": "4 sec"},
            {"shot": "Close-up of each name", "duration": "1 sec each"},
            {"shot": "Final wide shot", "duration": "2 sec"},
        ],
        "weeding": [
            {"shot": "Vinyl on mat after cutting", "duration": "2 sec"},
            {"shot": "Weeding letters with hook (speed up)", "duration": "5 sec"},
            {"shot": "Applying transfer tape", "duration": "3 sec"},
            {"shot": "Positioning on bottle", "duration": "3 sec"},
            {"shot": "Burnishing down", "duration": "2 sec"},
            {"shot": "Peeling back transfer tape - REVEAL", "duration": "4 sec"},
            {"shot": "Final spin of completed bottle", "duration": "2 sec"},
        ],
        "small_biz": [
            {"shot": "Screen showing orders/names", "duration": "2 sec"},
            {"shot": "Cricut cutting multiple sheets", "duration": "3 sec"},
            {"shot": "Weeding multiple names", "duration": "3 sec"},
            {"shot": "Stack of bottles getting vinyl", "duration": "3 sec"},
            {"shot": "Wrapping in tissue paper", "duration": "3 sec"},
            {"shot": "Putting in boxes", "duration": "2 sec"},
            {"shot": "Stack of ready-to-ship packages", "duration": "3 sec"},
        ],
        "engagement": [
            {"shot": "Bottle with Hot Pink vinyl", "duration": "2 sec"},
            {"shot": "Bottle with Baby Pink vinyl", "duration": "2 sec"},
            {"shot": "Bottle with Gold vinyl", "duration": "2 sec"},
            {"shot": "Bottle with Rose Gold vinyl", "duration": "2 sec"},
            {"shot": "Bottle with Teal vinyl", "duration": "2 sec"},
            {"shot": "Bottle with Black vinyl", "duration": "2 sec"},
        ],
        "pov": [
            {"shot": "All bottles lined up beautifully", "duration": "3 sec"},
            {"shot": "Close-up pan across names", "duration": "4 sec"},
            {"shot": "Placing bottles into a bag", "duration": "3 sec"},
            {"shot": "Zipping/closing bag", "duration": "2 sec"},
            {"shot": "Final ready-to-go shot", "duration": "2 sec"},
        ],
    }
    return shots.get(format_type, shots["tutorial"])


def generate_voiceover(format_type: str) -> str:
    """Generate voiceover script."""
    templates = VOICEOVER_TEMPLATES.get(format_type, VOICEOVER_TEMPLATES.get("tutorial", [""]))
    template = random.choice(templates) if templates else ""

    # Fill in placeholders
    font = random.choice(FONTS)
    return template.format(
        font=font,
        size="2 inches",
        price1="$12",
        cost="$15",
        sell_price="$35",
        count=random.randint(8, 15),
        price=random.choice([25, 30, 35]),
    )


def generate_text_overlays(format_type: str, hook: str) -> List[dict]:
    """Generate text overlays with timestamps."""
    base_overlays = [
        {"timestamp": "0:00-0:02", "text": hook},
    ]

    if format_type == "tutorial":
        base_overlays.extend([
            {"timestamp": "0:03-0:05", "text": f"Font: {random.choice(FONTS)} (FREE)"},
            {"timestamp": "0:06-0:08", "text": "Size: ~2 inches tall"},
            {"timestamp": "0:09-0:11", "text": "Vinyl: Permanent outdoor"},
            {"timestamp": "0:12-0:15", "text": "Result:"},
        ])
    elif format_type == "asmr":
        names = random.sample(NAMES, 4)
        base_overlays.extend([
            {"timestamp": "0:03-0:06", "text": f"cutting: {names[0]}..."},
            {"timestamp": "0:06-0:09", "text": f"{names[1]}... {names[2]}..."},
            {"timestamp": "0:10-0:12", "text": f"{names[3]}..."},
        ])
    elif format_type == "engagement":
        for i, (color, vibe, _) in enumerate(COLORS):
            start = i * 2
            base_overlays.append({
                "timestamp": f"0:{start:02d}-0:{start+2:02d}",
                "text": f"{color} = {vibe}"
            })

    return base_overlays


def generate_caption(format_type: str, hook: str, hashtags: List[str]) -> str:
    """Generate TikTok caption."""
    ctas = {
        "tutorial": "Tutorial since y'all keep asking! Save this for later",
        "asmr": "I could watch this all day",
        "supplies": "Everything linked in bio!",
        "reveal": "Comment your name and I might make you one",
        "weeding": "Full tutorial for my DIY girlies",
        "small_biz": "DM to order yours!",
        "engagement": "Comment your color!",
        "pov": "Tag your girls who need matching bottles!!",
    }

    cta = ctas.get(format_type, "Link in bio!")
    hashtag_str = " ".join(hashtags[:8])

    return f"{hook}\n\n{cta}\n\n{hashtag_str}"


def generate_title(format_type: str, hook: str) -> str:
    """Generate video title."""
    titles = {
        "tutorial": f"Tutorial - {hook}",
        "asmr": "Satisfying Cricut Cutting ASMR",
        "supplies": "Everything You Need - Supplies Haul",
        "reveal": "New Product Reveal",
        "weeding": "Weeding & Application Tutorial",
        "small_biz": "Small Business Check - Weekly Orders",
        "engagement": "Which Color Are You?",
        "pov": "POV: Friend Group Bottles",
    }
    return titles.get(format_type, f"Video - {hook}")


def generate_pro_tips(format_type: str) -> List[str]:
    """Generate pro tips for the video."""
    tips = {
        "tutorial": [
            "Show the font name clearly on screen",
            "Mention it's free from dafont.com",
            "2 inches is the sweet spot for most bottles",
        ],
        "asmr": [
            "Let the Cricut sounds do the work",
            "The peel reveal is the money shot",
            "Good lighting makes colors pop",
        ],
        "supplies": [
            "Show prices as each item appears",
            "End with profit margin math",
            "Link everything in your bio",
        ],
        "weeding": [
            "Go slow on cursive letter tails (y, g, j)",
            "The peel-back is the satisfying part",
            "Good lighting is essential",
        ],
        "engagement": [
            "Reply to every comment",
            "Ask follow-up questions",
            "This boosts algorithm engagement",
        ],
    }
    return tips.get(format_type, ["Post at peak times", "Engage in first hour"])


def generate_weekly_calendar(start_date: datetime, week_number: int) -> ContentCalendar:
    """Generate a weekly content calendar."""
    formats_rotation = [
        ("asmr", "Tuesday", "7pm"),
        ("tutorial", "Wednesday", "12pm"),
        ("meme", "Thursday", "8pm"),
        ("reveal", "Friday", "6pm"),
        ("engagement", "Saturday", "7pm"),
        ("small_biz", "Sunday", "3pm"),
        ("pov", "Monday", "6pm"),
    ]

    videos = []
    for i, (format_type, day, time) in enumerate(formats_rotation):
        video_date = start_date + timedelta(days=i)
        videos.append({
            "day": day,
            "date": video_date.strftime("%Y-%m-%d"),
            "format": format_type,
            "time": time,
            "video_number": (week_number - 1) * 7 + i + 1,
        })

    return ContentCalendar(
        week_number=week_number,
        start_date=start_date.strftime("%Y-%m-%d"),
        videos=videos,
    )


def generate_batch_scripts(count: int, start_number: int = 11) -> List[VideoScript]:
    """Generate a batch of video scripts."""
    formats = ["tutorial", "asmr", "supplies", "reveal", "weeding",
               "small_biz", "engagement", "pov", "meme"]

    scripts = []
    for i in range(count):
        format_type = formats[i % len(formats)]
        script = generate_video_script(
            video_number=start_number + i,
            format_type=format_type,
        )
        scripts.append(script)

    return scripts


def script_to_markdown(script: VideoScript) -> str:
    """Convert VideoScript to markdown format."""
    filming_list = "\n".join([f"- [ ] {item}" for item in script.filming_checklist])

    shots_list = "\n".join([
        f"{i+1}. **{shot['shot']}** ({shot['duration']})"
        for i, shot in enumerate(script.what_to_film)
    ])

    overlays_table = "| Timestamp | Text |\n|-----------|------|\n"
    overlays_table += "\n".join([
        f"| {o['timestamp']} | {o['text']} |"
        for o in script.text_overlays
    ])

    tips_list = "\n".join([f"- {tip}" for tip in script.pro_tips])
    sounds_list = "\n".join([f"- {sound}" for sound in script.sound_suggestions])

    return f"""# Video {script.video_number}: "{script.title}"

**Format:** {script.format_type.replace("_", " ").title()} (faceless)
**Post Time:** {script.post_time}

---

## Hook (First 1-2 seconds)
**"{script.hook}"**

---

## Filming Checklist

{filming_list}

---

## What to Film (In Order)

{shots_list}

---

## AI Voiceover Script

Copy this into CapCut TTS or ElevenLabs:

```
{script.voiceover_script}
```

---

## Text Overlays (Add in CapCut)

{overlays_table}

---

## Caption

```
{script.caption}
```

---

## Sound Suggestions

{sounds_list}

---

## Pro Tips

{tips_list}
"""


# ===========================================
# CLI Interface
# ===========================================

def main():
    parser = argparse.ArgumentParser(description="Generate TikTok content for water bottle business")
    parser.add_argument("--type", choices=["single", "batch", "weekly", "calendar"],
                       default="batch", help="Type of content to generate")
    parser.add_argument("--format", choices=["tutorial", "asmr", "supplies", "reveal",
                                            "weeding", "small_biz", "engagement", "pov", "meme"],
                       help="Video format (for single)")
    parser.add_argument("--count", type=int, default=10, help="Number of videos to generate")
    parser.add_argument("--start", type=int, default=11, help="Starting video number")
    parser.add_argument("--output", type=str, default=".", help="Output directory")
    parser.add_argument("--json", action="store_true", help="Output as JSON instead of markdown")

    args = parser.parse_args()
    output_dir = Path(args.output)
    output_dir.mkdir(parents=True, exist_ok=True)

    if args.type == "single":
        format_type = args.format or "tutorial"
        script = generate_video_script(args.start, format_type)

        if args.json:
            print(json.dumps(asdict(script), indent=2))
        else:
            print(script_to_markdown(script))

    elif args.type == "batch":
        scripts = generate_batch_scripts(args.count, args.start)

        for script in scripts:
            if args.json:
                filepath = output_dir / f"video-{script.video_number:02d}.json"
                with open(filepath, "w") as f:
                    json.dump(asdict(script), f, indent=2)
            else:
                filepath = output_dir / f"video-{script.video_number:02d}-{script.format_type}.md"
                with open(filepath, "w") as f:
                    f.write(script_to_markdown(script))
            print(f"Generated: {filepath}")

    elif args.type == "weekly" or args.type == "calendar":
        start_date = datetime.now()
        weeks = (args.count + 6) // 7  # Round up to weeks

        for week in range(1, weeks + 1):
            calendar = generate_weekly_calendar(start_date + timedelta(weeks=week-1), week)

            if args.json:
                filepath = output_dir / f"week-{week}-calendar.json"
                with open(filepath, "w") as f:
                    json.dump(asdict(calendar), f, indent=2)
            else:
                filepath = output_dir / f"week-{week}-calendar.md"
                with open(filepath, "w") as f:
                    f.write(f"# Week {week} Content Calendar\n\n")
                    f.write(f"Starting: {calendar.start_date}\n\n")
                    f.write("| Day | Date | Format | Time |\n")
                    f.write("|-----|------|--------|------|\n")
                    for v in calendar.videos:
                        f.write(f"| {v['day']} | {v['date']} | {v['format']} | {v['time']} |\n")
            print(f"Generated: {filepath}")


if __name__ == "__main__":
    main()
