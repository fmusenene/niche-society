# cPanel deployment

## White screen (blank page)?

The site needs **database credentials** on the server. GitHub does not include them.

**Fix in File Manager (no SSH):**

1. Go to **`config`** folder
2. **Copy** `database.local.php.example` → paste → rename to **`database.local.php`**
3. **Edit** `database.local.php` — set your cPanel MySQL name, user, and password  
   (cPanel → **MySQL® Databases**)
4. Pull latest from GitHub so **`config/config.php`** and **`config/database.php`** exist
5. Open **`https://niche-society.com/health.php`** — shows exactly what is wrong
6. When the site works, **delete `health.php`**

---

## One-time fix (run once in cPanel Terminal)

If you see **“local changes would be overwritten by merge”**:

```bash
cd ~/niche-society
# ^ use your actual repo path (see Git Version Control in cPanel)

bash scripts/cpanel-sync.sh
```

That resets tracked files to GitHub and **keeps** your `config/*.php` secrets.

---

## Every update after that

### Option A — cPanel UI (recommended)

1. **Git Version Control** → your repo  
2. **Pull or Deploy** → **Update from Remote**  
3. If pull fails, run `bash scripts/cpanel-sync.sh` in Terminal again  

Or use **Deploy HEAD commit** — runs `.cpanel.yml` automatically.

### Option B — Terminal

```bash
cd ~/niche-society
bash scripts/cpanel-sync.sh
```

---

## Files never overwritten (stay on server)

These are **not in Git** or are stashed during sync:

| File | Purpose |
|------|---------|
| `config/admin-settings.php` | Admin password |
| `config/secrets.local.php` | reCAPTCHA, encryption key |
| `config/database.local.php` | DB credentials |
| `config/config.php` | Site config (if you use a local copy) |
| `config/email.php` | SMTP settings |
| `logs/` | Error logs |

First deploy: copy examples if missing:

```bash
cp config/admin-settings.php.example config/admin-settings.php
cp config/secrets.local.php.example config/secrets.local.php
cp config/database.local.php.example config/database.local.php
```

Then edit with real values.

---

## Do not edit on the server

Avoid editing these in File Manager (edit locally, push to GitHub, then sync):

- `.htaccess`
- `maintenance.php`
- Any `.php` page in the site root

That prevents merge conflicts on the next pull.
