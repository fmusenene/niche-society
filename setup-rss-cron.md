# RSS Feed Aggregator Setup Guide

This guide explains how to set up automatic RSS feed aggregation for the Niche Society blog.

## Overview

The RSS Feed Aggregator (`rss-feed-aggregator.php`) automatically fetches articles from various news sources and publishes them to your blog. It:

- Fetches articles from Google News, BBC, Al Jazeera, Reuters, and other sources
- Filters content by category (Estate Management, News, etc.)
- Avoids duplicates by checking article slugs
- Saves articles with proper formatting

## Manual Execution

Run the script manually to test:

```bash
cd C:\xampp\htdocs\niche-society-main
php rss-feed-aggregator.php
```

Or via browser:
```
http://localhost/niche-society-main/rss-feed-aggregator.php
```

## Automated Execution (Windows Task Scheduler)

### Step 1: Create a Batch File

Create a file `run-rss-aggregator.bat` in your project root:

```batch
@echo off
cd C:\xampp\htdocs\niche-society-main
C:\xampp\php\php.exe rss-feed-aggregator.php
exit
```

### Step 2: Schedule the Task

1. Open **Task Scheduler** (search "Task Scheduler" in Windows)
2. Click **Create Basic Task** on the right
3. Name: "Niche Society RSS Aggregator"
4. Description: "Automatically fetch and publish blog articles from RSS feeds"
5. Trigger: **Daily** 
6. Time: Choose a time (e.g., every hour, or 6:00 AM and 6:00 PM for twice daily)
   - For hourly: Create multiple triggers or use Task Scheduler's "Repeat task" option
7. Action: **Start a program**
8. Program/script: `C:\xampp\htdocs\niche-society-main\run-rss-aggregator.bat`
9. Click **Finish**

### Step 3: Verify

- Right-click the task → **Run** to test
- Check the log file: `logs/rss-aggregator.log`

## Automated Execution (Linux/Mac Cron)

Edit your crontab:

```bash
crontab -e
```

Add one of these lines:

```bash
# Run every hour
0 * * * * cd /path/to/niche-society-main && php rss-feed-aggregator.php >> logs/rss-cron.log 2>&1

# Run every 6 hours (alternative)
0 */6 * * * cd /path/to/niche-society-main && php rss-feed-aggregator.php >> logs/rss-cron.log 2>&1

# Run twice daily (6 AM and 6 PM)
0 6,18 * * * cd /path/to/niche-society-main && php rss-feed-aggregator.php >> logs/rss-cron.log 2>&1

# Run daily at 6 AM
0 6 * * * cd /path/to/niche-society-main && php rss-feed-aggregator.php >> logs/rss-cron.log 2>&1
```

## RSS Feed Sources

Currently configured feeds:

1. **Google News - Estate Management**: Luxury estate and property management news
2. **Google News - Luxury Services**: VIP concierge and high-end services
3. **BBC Business**: General business news
4. **Al Jazeera**: International news (Arabic/English)
5. **Reuters Business**: Business news

### Adding Custom Feeds

Edit `rss-feed-aggregator.php` and add to the `$feeds` array:

```php
$feeds = [
    // ... existing feeds ...
    [
        'url' => 'https://example.com/feed/rss',
        'source' => 'Example Blog',
        'category' => 'News' // or 'Estate Management', 'Protocol & Etiquette', etc.
    ],
];
```

## Logging

All operations are logged to:
```
logs/rss-aggregator.log
```

Log entries include:
- Feed fetching status
- Articles saved/skipped
- Errors and warnings
- Summary statistics

## Troubleshooting

### Script doesn't run
- Check PHP path in batch file or cron command
- Verify `logs/` directory exists and is writable
- Check PHP error logs

### No articles saved
- Check RSS feed URLs are accessible
- Verify database connection
- Check log file for errors

### Duplicate articles
- The script checks for existing slugs
- If duplicates appear, RSS feeds may have changed titles

### Content in wrong language
- Articles are saved with English content by default
- Arabic content is the same as English (can be translated later)
- To auto-translate, integrate Google Translate API

## Content Quality

**Note**: RSS feeds may include general news not directly related to your niche. Consider:

1. **Filtering keywords**: Add keyword filters in `processFeed()` function
2. **Manual review**: Review articles before auto-publishing (change status to 'draft')
3. **Translation**: Integrate translation API for Arabic content
4. **Image handling**: External images may need to be downloaded/cached

## Security

- The script should NOT be publicly accessible
- Protect it with `.htaccess` or move outside web root
- Add IP whitelist for remote access (if needed)

## Next Steps

1. Test the script manually
2. Review saved articles in your blog
3. Adjust feed sources and categories as needed
4. Set up automated execution (cron/Task Scheduler)
5. Monitor log file regularly
6. Consider adding content moderation/approval workflow
