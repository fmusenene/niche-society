# RSS Aggregator on Live Server – Step-by-Step Guide

This guide shows you how to:
1. **Run the RSS aggregator manually** on your live server (so blog news updates right away).
2. **Set it to run automatically every 1 hour** so news keeps updating without you doing anything.

---

## Before You Start

- Your site must be **deployed** on the live server (e.g. cPanel) with at least:
  - `rss-feed-aggregator.php` in the site root (or your site folder).
  - `config/config.php` and `config/database.php` using the **live** database.
  - A writable `logs/` folder (the script can create it; folder must be writable).
- You need **cPanel login** (or SSH/Terminal access) to set up the automatic hourly run.

---

# Part 1: Run the RSS Aggregator Manually on the Live Server

You can run it in two ways: **in the browser** (easiest) or **via Terminal/SSH** (if you have access).

---

## Method A: Run Manually via Browser (easiest)

**Step 1.** Open your browser and go to your **live** site URL for the script:

- If your site is at the **main domain** (e.g. `https://niche-society.com`):
  ```
  https://niche-society.com/rss-feed-aggregator.php
  ```
- If your site is in a **subfolder** (e.g. `https://niche-society.com/niche-society/`):
  ```
  https://niche-society.com/niche-society/rss-feed-aggregator.php
  ```

**Step 2.** Press Enter. The page may show log lines (e.g. “RSS Feed Aggregator Started”, “Fetching RSS feed…”, “Saved article…”) or look mostly blank. Both are normal.

**Step 3.** Wait 1–2 minutes (the script fetches several feeds). Then open your **blog page**:
- `https://yourdomain.com/blog.php` (or `.../niche-society/blog.php`)
- Refresh the page. You should see new or updated articles (up to 9 per page).

**Step 4.** *(Optional)* Check the log file to confirm it ran:
- In cPanel **File Manager**, go to your site folder → open the `logs` folder.
- Open `rss-aggregator.log`. You should see lines like “Successfully fetched RSS feed”, “Saved article”, “RSS Feed Aggregator Completed”.

**Note:** Running manually in the browser is fine for testing. For ongoing updates, set up the **automatic run every 1 hour** in Part 2.

---

## Method B: Run Manually via Terminal / SSH (if you have access)

**Step 1.** Log in to your server via **SSH** (or use cPanel **Terminal**).

**Step 2.** Go to the folder where your site (and `rss-feed-aggregator.php`) lives. For example:
```bash
cd ~/public_html
# or, if the site is in a subfolder:
cd ~/public_html/niche-society
```

**Step 3.** Run the script with PHP (use the path your host gives; common examples):
```bash
php rss-feed-aggregator.php
```
or:
```bash
/usr/local/bin/php rss-feed-aggregator.php
```

**Step 4.** You’ll see log output in the terminal. When it finishes, open your blog in the browser and refresh; new articles should appear.

---

# Part 2: Set the RSS Aggregator to Run Automatically Every 1 Hour

On a live server with **cPanel**, use **Cron Jobs** to run the script every hour.

---

## Step 1: Find the Full Path to Your Script

You need the **full server path** to `rss-feed-aggregator.php`.

1. Log in to **cPanel**.
2. Open **File Manager**.
3. Go to the folder that contains `rss-feed-aggregator.php` (e.g. `public_html` or `public_html/niche-society`).
4. Note the path. The full path is usually:
   - **Main domain:** `/home/YOUR_CPANEL_USERNAME/public_html/rss-feed-aggregator.php`
   - **Subfolder:** `/home/YOUR_CPANEL_USERNAME/public_html/niche-society/rss-feed-aggregator.php`

Replace `YOUR_CPANEL_USERNAME` with your actual cPanel username (often in the top-right of cPanel).

**Tip:** In cPanel **Terminal**, you can run `pwd` when you’re in the site folder to see the full path; then add `/rss-feed-aggregator.php` to it.

---

## Step 2: Find the PHP Path on the Server

The cron job must call PHP. Common paths:

- `/usr/local/bin/php`
- `/usr/bin/php`
- or just `php`

In cPanel **Cron Jobs**, there is often a dropdown or an example job that shows the correct PHP path. Use that. If not, try `/usr/local/bin/php` first.

---

## Step 3: Create the Cron Job (Every 1 Hour)

1. In cPanel, go to **Advanced** → **Cron Jobs** (or search “Cron” in the cPanel search box).
2. Under **Add New Cron Job**:
   - **Set the schedule to “every hour”:**
     - Either choose **“Once Per Hour”** from the **Common settings** dropdown,  
     - Or set **Minute** to `0` and **Hour** to `*` (so the cron runs at minute 0 of every hour: `0 * * * *`).
   - **Command:** enter one of the following (replace paths with yours from Step 1 and Step 2).

**If your site is in the main domain (e.g. `public_html`):**
```bash
/usr/local/bin/php /home/YOUR_CPANEL_USERNAME/public_html/rss-feed-aggregator.php
```

**If your site is in a subfolder (e.g. `public_html/niche-society`):**
```bash
/usr/local/bin/php /home/YOUR_CPANEL_USERNAME/public_html/niche-society/rss-feed-aggregator.php
```

- Replace `YOUR_CPANEL_USERNAME` with your cPanel username.
- If your host uses a different PHP path, replace `/usr/local/bin/php` with that path (e.g. `/usr/bin/php`).

3. Click **Add New Cron Job** (or **Create**).

You should see the new job in the list (e.g. “0 * * * *” or “Once Per Hour”).

---

## Step 4: (Optional) Save Cron Output to a Log

To capture any errors, you can append output to a log file. In the **Command** field, use:

```bash
/usr/local/bin/php /home/YOUR_CPANEL_USERNAME/public_html/niche-society/rss-feed-aggregator.php >> /home/YOUR_CPANEL_USERNAME/public_html/niche-society/logs/rss-cron.log 2>&1
```

(Adjust paths to match your setup.) Then check `logs/rss-cron.log` if something goes wrong.

---

## Step 5: Test the Automatic Run

1. In **Cron Jobs**, find your new job. If there is a **Run** or **Run Now** button, click it.
2. Wait 1–2 minutes, then open your **blog** in the browser and refresh. New articles should appear (or the list should update).
3. After an hour, check the blog again; the cron will have run again and news should keep updating every hour.

---

## Step 6: Optional – Restrict Browser Access to the Script

If you don’t want anyone to run the aggregator by opening it in the browser:

1. In **File Manager**, go to the folder that contains `rss-feed-aggregator.php`.
2. Edit (or create) **`.htaccess`** in that folder.
3. Add:

```apache
# Only allow cron / server; block browser access to the aggregator
<Files "rss-feed-aggregator.php">
    Require all denied
</Files>
```

4. Save. The **cron job** will still work (it runs PHP on the server, not via the web). Only browser access to that URL will be blocked.

---

# Quick Reference

| Task | What to do |
|------|------------|
| **Run once manually (browser)** | Open `https://yourdomain.com/rss-feed-aggregator.php` (or `.../niche-society/rss-feed-aggregator.php`). |
| **Run once manually (Terminal)** | `cd` to site folder, then run `php rss-feed-aggregator.php` (or with full PHP path). |
| **Run every 1 hour automatically** | cPanel → Cron Jobs → Add job: schedule `0 * * * *`, command: `php /full/path/to/rss-feed-aggregator.php`. |
| **Check if it ran** | Open `logs/rss-aggregator.log` in File Manager; or check the blog for new articles. |
| **Cron path examples** | Main domain: `/home/USERNAME/public_html/rss-feed-aggregator.php`. Subfolder: `/home/USERNAME/public_html/niche-society/rss-feed-aggregator.php`. |

---

# Troubleshooting

| Problem | What to do |
|--------|------------|
| Manual run in browser shows error or blank | Check that `config/database.php` on the server has the correct DB name, user, and password. Ensure `logs/` exists and is writable (755 or 775). |
| Cron runs but no new articles | Same as above: verify database config. Check `logs/rss-aggregator.log` for “Saved article” or errors. |
| Cron job “No output” or fails | In the cron command, try `php` instead of `/usr/local/bin/php`, or use the exact PHP path shown in cPanel. |
| “Permission denied” or no log file | Set `logs/` permissions to 755 (or 775). Ensure it’s in the same directory as `rss-feed-aggregator.php`. |
| Blog still shows old count | Clear server/cache if you use caching. Ensure the **live** database is the one used by the site and by the aggregator. |

---

After you complete Part 1 (manual run) and Part 2 (cron every 1 hour), the blog news will update when you run it manually and will keep updating automatically every hour on the live server.
