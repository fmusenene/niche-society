# Syncing blog articles from cPanel to localhost

## Why localhost shows 5 articles and cPanel shows 222

- **Same code, different data.** The blog reads from the `blog_posts` table in MySQL.  
- **cPanel** uses one database: it has been filled by the RSS aggregator (and/or cron) over time → 222 articles.  
- **localhost** uses a different database (XAMPP): it only has what you seeded or imported → 5 articles.

So the difference is **which database** each environment uses, not a bug in the code.

---

## How to get the same 222 articles on localhost

### Option 1: Export from cPanel, import on local (recommended)

1. **In cPanel**
   - Open **phpMyAdmin** (or “MySQL® Databases” → phpMyAdmin).
   - Select the database that your site uses (same one in `config/database.php` on the server).
   - Click the **Export** tab.
   - Choose **Custom** export.
   - Under “Tables,” select only **`blog_posts`** (or export the whole DB if you want everything).
   - Format: **SQL**.
   - Click **Go** and save the `.sql` file.

2. **On your PC (XAMPP)**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Select (or create) the database you use locally (e.g. `niche_society` from `config/database.php`).
   - Click **Import**.
   - Choose the `.sql` file you downloaded.
   - If you exported only `blog_posts`:  
     - Either import into the same DB (it will add/update rows), or  
     - If you want a clean copy, **truncate** the `blog_posts` table first (Operations or SQL: `TRUNCATE TABLE blog_posts;`), then import.
   - Click **Go**.

3. Reload `http://localhost/niche-society/blog.php` — you should see the same count as on cPanel (e.g. 222).

### Option 2: Let the RSS aggregator fill localhost over time

- Each time someone opens the blog page, the site may call the RSS aggregator (about once per hour).
- On localhost you can run it manually:  
  `php rss-feed-aggregator.php`  
  (from the project root in a terminal, with PHP in PATH).
- Over time you’ll get more articles, but reaching 222 depends on feeds and how often you run it. For a quick match with cPanel, use Option 1.

---

## Keeping config correct

- **cPanel:** `config/database.php` (or env) must point to the cPanel MySQL host, database name, user, and password.
- **localhost:** `config/database.php` should use `localhost`, your local DB name, `root`, and your local password (often empty).

Do not overwrite the cPanel `config/database.php` with your local one when deploying; use environment-specific config or a separate config for production.
