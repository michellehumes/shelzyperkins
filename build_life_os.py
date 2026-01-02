#!/usr/bin/env python3
"""
Life OS - Personal Dashboard & Life Management System
======================================================
A comprehensive personal productivity and life management system.

Features:
- Goal tracking (yearly, quarterly, monthly)
- Habit tracking with streaks
- Task management with priorities
- Daily/weekly planning and reviews
- Life areas balance tracking
- Generates an HTML dashboard
"""

import json
import os
from datetime import datetime, timedelta
from pathlib import Path

# Configuration
LIFE_OS_DIR = Path(__file__).parent / "life-os"
DATA_DIR = LIFE_OS_DIR / "data"
OUTPUT_DIR = LIFE_OS_DIR / "dashboard"

# Life Areas for balance tracking
LIFE_AREAS = [
    "Health & Fitness",
    "Career & Business",
    "Finances",
    "Relationships",
    "Personal Growth",
    "Fun & Recreation",
    "Physical Environment",
    "Contribution"
]


def ensure_directories():
    """Create necessary directories if they don't exist."""
    DATA_DIR.mkdir(parents=True, exist_ok=True)
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)


def load_json(filename, default=None):
    """Load JSON data from file."""
    filepath = DATA_DIR / filename
    if filepath.exists():
        with open(filepath, 'r') as f:
            return json.load(f)
    return default if default is not None else {}


def save_json(filename, data):
    """Save JSON data to file."""
    filepath = DATA_DIR / filename
    with open(filepath, 'w') as f:
        json.dump(data, f, indent=2, default=str)


def initialize_data_files():
    """Initialize data files with default structure if they don't exist."""

    # Goals
    if not (DATA_DIR / "goals.json").exists():
        goals = {
            "yearly": [
                {"id": 1, "title": "Build successful affiliate business", "category": "Career & Business", "progress": 40, "target_date": "2026-12-31"},
                {"id": 2, "title": "Establish healthy daily routine", "category": "Health & Fitness", "progress": 60, "target_date": "2026-12-31"},
                {"id": 3, "title": "Grow email list to 10,000 subscribers", "category": "Career & Business", "progress": 25, "target_date": "2026-12-31"}
            ],
            "quarterly": [
                {"id": 1, "title": "Publish 30 blog posts", "category": "Career & Business", "progress": 33, "target_date": "2026-03-31"},
                {"id": 2, "title": "Set up all automation systems", "category": "Career & Business", "progress": 70, "target_date": "2026-03-31"}
            ],
            "monthly": [
                {"id": 1, "title": "Write 10 product reviews", "category": "Career & Business", "progress": 50, "target_date": "2026-01-31"},
                {"id": 2, "title": "Exercise 20 days this month", "category": "Health & Fitness", "progress": 10, "target_date": "2026-01-31"}
            ]
        }
        save_json("goals.json", goals)

    # Habits
    if not (DATA_DIR / "habits.json").exists():
        habits = {
            "habits": [
                {"id": 1, "name": "Morning routine", "frequency": "daily", "streak": 5, "best_streak": 12, "completed_today": True},
                {"id": 2, "name": "Write 500 words", "frequency": "daily", "streak": 3, "best_streak": 21, "completed_today": False},
                {"id": 3, "name": "Exercise", "frequency": "daily", "streak": 0, "best_streak": 30, "completed_today": False},
                {"id": 4, "name": "Read 30 minutes", "frequency": "daily", "streak": 7, "best_streak": 45, "completed_today": True},
                {"id": 5, "name": "Weekly review", "frequency": "weekly", "streak": 4, "best_streak": 8, "completed_today": False},
                {"id": 6, "name": "Publish blog post", "frequency": "weekly", "streak": 2, "best_streak": 6, "completed_today": False}
            ],
            "log": {}
        }
        save_json("habits.json", habits)

    # Tasks
    if not (DATA_DIR / "tasks.json").exists():
        tasks = {
            "inbox": [
                {"id": 1, "title": "Research new affiliate products", "priority": "high", "due": None},
                {"id": 2, "title": "Update Pinterest automation", "priority": "medium", "due": None}
            ],
            "today": [
                {"id": 3, "title": "Write blog post on kitchen gadgets", "priority": "high", "due": "2026-01-02", "completed": False},
                {"id": 4, "title": "Check affiliate dashboard", "priority": "medium", "due": "2026-01-02", "completed": True},
                {"id": 5, "title": "Respond to reader comments", "priority": "low", "due": "2026-01-02", "completed": False}
            ],
            "upcoming": [
                {"id": 6, "title": "Quarterly content planning", "priority": "high", "due": "2026-01-05"},
                {"id": 7, "title": "Update SEO for top 10 posts", "priority": "medium", "due": "2026-01-10"}
            ],
            "someday": [
                {"id": 8, "title": "Launch YouTube channel", "priority": "low", "due": None},
                {"id": 9, "title": "Create digital product", "priority": "medium", "due": None}
            ]
        }
        save_json("tasks.json", tasks)

    # Life Balance
    if not (DATA_DIR / "life_balance.json").exists():
        balance = {
            "current_scores": {
                "Health & Fitness": 6,
                "Career & Business": 7,
                "Finances": 5,
                "Relationships": 7,
                "Personal Growth": 8,
                "Fun & Recreation": 4,
                "Physical Environment": 6,
                "Contribution": 5
            },
            "target_scores": {
                "Health & Fitness": 8,
                "Career & Business": 9,
                "Finances": 8,
                "Relationships": 8,
                "Personal Growth": 9,
                "Fun & Recreation": 7,
                "Physical Environment": 7,
                "Contribution": 7
            },
            "last_updated": str(datetime.now().date())
        }
        save_json("life_balance.json", balance)

    # Weekly Reviews
    if not (DATA_DIR / "weekly_reviews.json").exists():
        reviews = {
            "reviews": []
        }
        save_json("weekly_reviews.json", reviews)

    # Daily Journal
    if not (DATA_DIR / "journal.json").exists():
        journal = {
            "entries": []
        }
        save_json("journal.json", journal)


def calculate_stats():
    """Calculate dashboard statistics."""
    goals = load_json("goals.json", {})
    habits = load_json("habits.json", {"habits": []})
    tasks = load_json("tasks.json", {"today": []})
    balance = load_json("life_balance.json", {"current_scores": {}})

    # Goal stats
    all_goals = goals.get("yearly", []) + goals.get("quarterly", []) + goals.get("monthly", [])
    avg_goal_progress = sum(g.get("progress", 0) for g in all_goals) / len(all_goals) if all_goals else 0

    # Habit stats
    habits_list = habits.get("habits", [])
    daily_habits = [h for h in habits_list if h.get("frequency") == "daily"]
    completed_today = sum(1 for h in daily_habits if h.get("completed_today", False))
    total_streak = sum(h.get("streak", 0) for h in habits_list)

    # Task stats
    today_tasks = tasks.get("today", [])
    completed_tasks = sum(1 for t in today_tasks if t.get("completed", False))
    inbox_count = len(tasks.get("inbox", []))

    # Life balance average
    scores = balance.get("current_scores", {})
    avg_balance = sum(scores.values()) / len(scores) if scores else 0

    return {
        "goal_progress": round(avg_goal_progress, 1),
        "habits_completed": completed_today,
        "habits_total": len(daily_habits),
        "habit_streak_total": total_streak,
        "tasks_completed": completed_tasks,
        "tasks_total": len(today_tasks),
        "inbox_count": inbox_count,
        "life_balance_avg": round(avg_balance, 1)
    }


def generate_dashboard_html():
    """Generate the HTML dashboard."""
    stats = calculate_stats()
    goals = load_json("goals.json", {})
    habits = load_json("habits.json", {"habits": []})
    tasks = load_json("tasks.json", {})
    balance = load_json("life_balance.json", {"current_scores": {}})

    today = datetime.now()
    week_number = today.isocalendar()[1]

    html = f'''<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life OS Dashboard</title>
    <style>
        :root {{
            --primary: #FF6B6B;
            --secondary: #FFE66D;
            --accent: #4ECDC4;
            --dark: #2D3436;
            --light: #F8F9FA;
            --success: #00B894;
            --warning: #FDCB6E;
            --danger: #E17055;
        }}

        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}

        body {{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            color: var(--dark);
        }}

        .dashboard {{
            max-width: 1400px;
            margin: 0 auto;
        }}

        header {{
            text-align: center;
            color: white;
            margin-bottom: 2rem;
        }}

        header h1 {{
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }}

        header p {{
            opacity: 0.9;
            font-size: 1.1rem;
        }}

        .stats-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }}

        .stat-card {{
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }}

        .stat-card:hover {{
            transform: translateY(-5px);
        }}

        .stat-value {{
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }}

        .stat-label {{
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }}

        .main-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }}

        .card {{
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }}

        .card h2 {{
            font-size: 1.3rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }}

        .goal-item, .task-item, .habit-item {{
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: var(--light);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }}

        .progress-bar {{
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }}

        .progress-fill {{
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 4px;
            transition: width 0.5s ease;
        }}

        .streak {{
            background: var(--warning);
            color: var(--dark);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }}

        .priority-high {{
            border-left: 4px solid var(--danger);
        }}

        .priority-medium {{
            border-left: 4px solid var(--warning);
        }}

        .priority-low {{
            border-left: 4px solid var(--success);
        }}

        .completed {{
            text-decoration: line-through;
            opacity: 0.6;
        }}

        .balance-grid {{
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }}

        .balance-item {{
            padding: 0.5rem;
            background: var(--light);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }}

        .balance-score {{
            font-weight: 700;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }}

        .score-high {{ background: var(--success); color: white; }}
        .score-medium {{ background: var(--warning); color: var(--dark); }}
        .score-low {{ background: var(--danger); color: white; }}

        .quick-actions {{
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }}

        .action-btn {{
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }}

        .action-btn.primary {{
            background: var(--primary);
            color: white;
        }}

        .action-btn.secondary {{
            background: var(--light);
            color: var(--dark);
        }}

        footer {{
            text-align: center;
            color: white;
            margin-top: 2rem;
            opacity: 0.8;
        }}

        .emoji {{
            font-size: 1.2rem;
        }}
    </style>
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Life OS Dashboard</h1>
            <p>{today.strftime('%A, %B %d, %Y')} | Week {week_number}</p>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{stats['goal_progress']}%</div>
                <div class="stat-label">Goal Progress</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{stats['habits_completed']}/{stats['habits_total']}</div>
                <div class="stat-label">Habits Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{stats['tasks_completed']}/{stats['tasks_total']}</div>
                <div class="stat-label">Tasks Done</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{stats['life_balance_avg']}/10</div>
                <div class="stat-label">Life Balance</div>
            </div>
        </div>

        <div class="main-grid">
            <!-- Goals -->
            <div class="card">
                <h2><span class="emoji">🎯</span> Goals</h2>
                <h3 style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Yearly Goals</h3>
                {''.join(f"""
                <div class="goal-item">
                    <div style="flex: 1;">
                        <div style="font-weight: 500;">{g['title']}</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {g['progress']}%;"></div>
                        </div>
                    </div>
                    <div style="margin-left: 1rem; font-weight: 700; color: var(--primary);">{g['progress']}%</div>
                </div>
                """ for g in goals.get('yearly', [])[:3])}

                <h3 style="font-size: 0.9rem; color: #666; margin: 1rem 0 0.5rem;">This Quarter</h3>
                {''.join(f"""
                <div class="goal-item">
                    <div style="flex: 1;">
                        <div style="font-weight: 500;">{g['title']}</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {g['progress']}%;"></div>
                        </div>
                    </div>
                    <div style="margin-left: 1rem; font-weight: 700; color: var(--primary);">{g['progress']}%</div>
                </div>
                """ for g in goals.get('quarterly', [])[:2])}
            </div>

            <!-- Habits -->
            <div class="card">
                <h2><span class="emoji">🔥</span> Habits</h2>
                {''.join(f"""
                <div class="habit-item {'completed' if h.get('completed_today') else ''}">
                    <div>
                        <span>{'✅' if h.get('completed_today') else '⬜'}</span>
                        <span style="margin-left: 0.5rem;">{h['name']}</span>
                    </div>
                    <span class="streak">🔥 {h['streak']} days</span>
                </div>
                """ for h in habits.get('habits', []) if h.get('frequency') == 'daily')}
            </div>

            <!-- Today's Tasks -->
            <div class="card">
                <h2><span class="emoji">✅</span> Today's Tasks</h2>
                {''.join(f"""
                <div class="task-item priority-{t.get('priority', 'medium')} {'completed' if t.get('completed') else ''}">
                    <div>
                        <span>{'✅' if t.get('completed') else '⬜'}</span>
                        <span style="margin-left: 0.5rem;">{t['title']}</span>
                    </div>
                </div>
                """ for t in tasks.get('today', []))}

                <h3 style="font-size: 0.9rem; color: #666; margin: 1rem 0 0.5rem;">Inbox ({len(tasks.get('inbox', []))} items)</h3>
                {''.join(f"""
                <div class="task-item priority-{t.get('priority', 'medium')}">
                    <span>{t['title']}</span>
                </div>
                """ for t in tasks.get('inbox', [])[:3])}
            </div>

            <!-- Life Balance -->
            <div class="card">
                <h2><span class="emoji">⚖️</span> Life Balance</h2>
                <div class="balance-grid">
                    {''.join(f"""
                    <div class="balance-item">
                        <span>{area}</span>
                        <span class="balance-score {'score-high' if score >= 7 else 'score-medium' if score >= 5 else 'score-low'}">{score}/10</span>
                    </div>
                    """ for area, score in balance.get('current_scores', {}).items())}
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <h2><span class="emoji">⚡</span> Quick Actions</h2>
                <div class="quick-actions">
                    <button class="action-btn primary">+ Add Task</button>
                    <button class="action-btn secondary">📝 Journal Entry</button>
                    <button class="action-btn secondary">📊 Weekly Review</button>
                    <button class="action-btn secondary">🎯 Update Goals</button>
                </div>
                <div style="margin-top: 1rem; padding: 1rem; background: var(--light); border-radius: 8px;">
                    <strong>💡 Daily Focus:</strong>
                    <p style="margin-top: 0.5rem; color: #666;">What's the ONE thing you can do today that will make everything else easier?</p>
                </div>
            </div>

            <!-- Upcoming -->
            <div class="card">
                <h2><span class="emoji">📅</span> Upcoming</h2>
                {''.join(f"""
                <div class="task-item priority-{t.get('priority', 'medium')}">
                    <div>
                        <span>{t['title']}</span>
                        <div style="font-size: 0.8rem; color: #666; margin-top: 0.25rem;">{t.get('due', 'No date')}</div>
                    </div>
                </div>
                """ for t in tasks.get('upcoming', [])[:4])}
            </div>
        </div>

        <footer>
            <p>Life OS v1.0 | Built with purpose | Last updated: {today.strftime('%Y-%m-%d %H:%M')}</p>
        </footer>
    </div>
</body>
</html>'''

    output_file = OUTPUT_DIR / "index.html"
    with open(output_file, 'w') as f:
        f.write(html)

    return output_file


def print_summary():
    """Print a summary to the console."""
    stats = calculate_stats()

    print("\n" + "="*60)
    print("                    🚀 LIFE OS v1.0 🚀")
    print("="*60)
    print(f"\n📅 {datetime.now().strftime('%A, %B %d, %Y')}")
    print("-"*60)

    print("\n📊 TODAY'S SNAPSHOT:")
    print(f"   🎯 Goal Progress:    {stats['goal_progress']}%")
    print(f"   🔥 Habits Today:     {stats['habits_completed']}/{stats['habits_total']} completed")
    print(f"   ✅ Tasks Today:      {stats['tasks_completed']}/{stats['tasks_total']} completed")
    print(f"   ⚖️  Life Balance:     {stats['life_balance_avg']}/10 average")
    print(f"   📥 Inbox Items:      {stats['inbox_count']}")

    # Load and show today's tasks
    tasks = load_json("tasks.json", {})
    today_tasks = tasks.get("today", [])

    if today_tasks:
        print("\n📋 TODAY'S TASKS:")
        for t in today_tasks:
            status = "✅" if t.get("completed") else "⬜"
            priority_icon = {"high": "🔴", "medium": "🟡", "low": "🟢"}.get(t.get("priority", "medium"), "⚪")
            print(f"   {status} {priority_icon} {t['title']}")

    # Load and show habits
    habits = load_json("habits.json", {"habits": []})
    daily_habits = [h for h in habits.get("habits", []) if h.get("frequency") == "daily"]

    if daily_habits:
        print("\n🔥 DAILY HABITS:")
        for h in daily_habits:
            status = "✅" if h.get("completed_today") else "⬜"
            print(f"   {status} {h['name']} (streak: {h.get('streak', 0)} days)")

    print("\n" + "-"*60)
    print("📁 Data files: life-os/data/")
    print("🌐 Dashboard:  life-os/dashboard/index.html")
    print("="*60 + "\n")


def main():
    """Main entry point."""
    print("\n🔧 Initializing Life OS...")

    # Set up directories
    ensure_directories()

    # Initialize data files if needed
    initialize_data_files()

    # Generate dashboard
    dashboard_path = generate_dashboard_html()

    # Print summary
    print_summary()

    print(f"✨ Dashboard generated: {dashboard_path}")
    print("💡 Tip: Open the dashboard HTML file in your browser!\n")


if __name__ == "__main__":
    main()
