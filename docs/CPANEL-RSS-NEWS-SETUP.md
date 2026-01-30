# Step-by-step: Set up automatic news updates on cPanel

This guide is for the **Niche Society** site. It tells you exactly how to get the blog news (RSS aggregator) running automatically on your cPanel host.

---

## What you’re setting up

- **Script:** `rss-feed-aggregator.php` (in your site root)
- **What it does:** Fetches articles from RSS feeds, saves them to the `blog_posts` table, and removes items older than 7 days.
- **On cPanel:** You will run this script on a **schedule** (cron) so news updates even when nobody visits the site.

---

## Step 1: Upload your site to cPanel (if not already)

1. Log in to **cPanel** (your host’s control panel).
2. Open **File Manager**.
3. Go to the folder where the site lives:
   - **Main domain:** usually `public_html`
   - **Subdomain or addon domain:** might be `public_html/niche-society` or another folder.
4. Upload your project so that these exist in that folder:
   - `rss-feed-aggregator.php`
   - `config/` (with `config.php` and `database.php`)
   - `functions/`
   - All other site files (index.php, blog.php, assets/, etc.)

**Note:** Your **exact path** will look like one of these (replace `USERNAME` with your cPanel username):

- `/home/USERNAME/public_html/`
- `/home/USERNAME/public_html/niche-society/`

You’ll need this path in Step 4.

---

## Step 2: Make sure the database is set up on the server

1. In cPanel, open **MySQL® Databases** (or **MySQL Database Wizard**).
2. Create a database (e.g. `USERNAME_niche` or `niche_society`).
3. Create a database user and give it **All privileges** on that database.
4. Edit your site’s **`config/database.php`** on the server so it uses the **cPanel** database details, for example:

   - **DB_HOST:** often `localhost` (or the hostname cPanel shows).
   - **DB_NAME:** the database you created.
   - **DB_USER:** the user you created.
   - **DB_PASS:** the user’s password.

5. If you have a **database setup script** (e.g. `run-schema.php`), run it once in the browser (e.g. `https://yourdomain.com/run-schema.php`) so the `blog_posts` table and schema exist.

---

## Step 3: Create and protect the `logs` folder

The aggregator writes a “last run” file and a log file. The script can create `logs/` itself, but it must be writable.

1. In **File Manager**, go to the **same folder** where `rss-feed-aggregator.php` is (your site root).
2. If there is no `logs` folder:
   - Click **+ Folder**.
   - Name it `logs`.
   - Create it.
3. Right‑click the `logs` folder → **Change Permissions**.
4. Set permissions to **755** (or **775** if your host requires it). Ensure the owner (your cPanel user) can read and write.

This folder will hold:

- `rss-last-run.txt` (used by the blog page to decide when to run the aggregator).
- `rss-aggregator.log` (optional; if the script logs to a file).

---

## Step 4: Find the full path to the aggregator script

You need the **full server path** to `rss-feed-aggregator.php` for the cron job.

1. In **File Manager**, go to the folder that contains `rss-feed-aggregator.php`.
2. At the top of File Manager you often see the path, e.g.:
   - `public_html`
   - or `public_html/niche-society`
3. The **full path** is usually:
   - `/home/USERNAME/public_html/`
   - or `/home/USERNAME/public_html/niche-society/`

So the full path to the script is one of:

- `/home/USERNAME/public_html/rss-feed-aggregator.php`
- `/home/USERNAME/public_html/niche-society/rss-feed-aggregator.php`

Replace `USERNAME` with your cPanel username. Write this down; you’ll use it in the next step.

**Tip:** In cPanel, **Cron Jobs** sometimes show “Current path” or you can run `pwd` in **Terminal** from your site folder to see the path.

---

## Step 5: Add a cron job in cPanel

1. In cPanel, open **Advanced** → **Cron Jobs**.
2. Under **Add New Cron Job**:
   - **Common settings:** choose how often you want news to update:
     - **Every hour:** “Once Per Hour” (or `0 * * * *`).
     - **Every 6 hours:** “Every Six Hours” or `0 */6 * * *`.
     - **Once per day:** “Once Per Day” and pick a time (e.g. 6:00 AM), or `0 6 * * *`.
   - **Command:** use one of the forms below, with **your** path from Step 4.

**Command (replace the path with yours):**

**Option A – Site in `public_html` (main domain):**

```bash
/usr/local/bin/php /home/USERNAME/public_html/rss-feed-aggregator.php
```

**Option B – Site in a subfolder (e.g. `public_html/niche-society`):**

```bash
/usr/local/bin/php /home/USERNAME/public_html/niche-society/rss-feed-aggregator.php
```

- Replace `USERNAME` with your cPanel username.
- If `php` is not at `/usr/local/bin/php`, cPanel’s Cron Jobs page often has a dropdown or example that shows the correct path (e.g. `php` or `/usr/bin/php`). Use that instead of `/usr/local/bin/php`.

3. Click **Add New Cron Job**.

You should see the new job in the list (e.g. “Run every hour” or “At 6:00 AM”).

---

## Step 6: Test that it works

**Option 1 – Run the cron job once by hand**

1. In **Cron Jobs**, find your new job.
2. If there is a **Run** or “Run now” link, click it.
3. Wait a minute, then open your blog page and check if new articles appear (or if the count changed).

**Option 2 – Run the script via browser (one-time test)**

1. Open in your browser:
   - `https://yourdomain.com/rss-feed-aggregator.php`
   - or `https://yourdomain.com/niche-society/rss-feed-aggregator.php`
2. You may see log output or a blank page. Check the blog; new items may take a short time to show.
3. **Security:** After testing, you can block public access to `rss-feed-aggregator.php` with a rule in `.htaccess` (e.g. allow only localhost or your IP) so only the cron (or your blog’s “on visit” trigger) runs it.

**Option 3 – Check the log (if the script writes one)**

- In File Manager, open `logs/rss-aggregator.log` (if it exists). You should see lines about feeds and articles.

---

## Step 7: Optional – Protect the aggregator URL

To avoid strangers hitting the script in the browser:

1. In your site root (same folder as `rss-feed-aggregator.php`), edit or create **`.htaccess`**.
2. You can restrict access to the script, for example (only run from server / cron):

```apache
# Optional: allow only cron/localhost to run the aggregator
<Files "rss-feed-aggregator.php">
    Require all denied
</Files>
```

If you do this, the **cron job** (Step 5) will still work, because cron runs PHP via the command line, not via the web. The “on visit” trigger from `blog.php` (when someone opens the blog) might no longer be able to call the script via URL; in that case, rely on the cron for updates.

---

## Quick reference – your project layout

```
your-site-root/
├── rss-feed-aggregator.php   ← script run by cron
├── blog.php                  ← page that can trigger aggregator on visit (if allowed)
├── config/
│   ├── config.php
│   └── database.php          ← must use cPanel DB details on server
├── logs/                     ← must exist and be writable (755/775)
│   ├── rss-last-run.txt
│   └── rss-aggregator.log
├── functions/
├── assets/
└── ...
```

---

## Troubleshooting

| Problem | What to do |
|--------|------------|
| Cron runs but no new articles | Check `config/database.php` on the server (correct DB name, user, password). Run the schema if needed. Check `logs/rss-aggregator.log` for errors. |
| “Permission denied” or no log file | Set `logs/` permissions to 755 or 775. Ensure the folder is in the same directory as `rss-feed-aggregator.php`. |
| Cron job “No output” or fails | In Cron Jobs, try command: `php /home/USERNAME/public_html/rss-feed-aggregator.php` (without `/usr/local/bin/`). Or use the PHP path shown in cPanel. |
| Blog doesn’t update when I visit | That’s normal if you blocked the script in `.htaccess`. Rely on the cron; it will update news on the schedule you chose. |

---

## Summary

1. **Upload** the site so `rss-feed-aggregator.php` and `config/` are in place.  
2. **Configure** `config/database.php` for the cPanel database.  
3. **Create** a writable `logs/` folder.  
4. **Get** the full path to `rss-feed-aggregator.php`.  
5. **Add** a cPanel cron job (e.g. every hour or once daily) with that path.  
6. **Test** by running the cron once or opening the script in the browser, then check the blog.  
7. **Optionally** lock down `rss-feed-aggregator.php` in `.htaccess` and rely on cron for automatic news updates.

After this, news will update automatically on the schedule you set (e.g. daily or hourly) on cPanel.
