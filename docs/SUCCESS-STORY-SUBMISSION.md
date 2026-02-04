# How Success Stories Appear on the Website

## How clients submit a success story

1. **Where:** Clients go to the **Success Stories** page and click **"Share your story"** (or open `success-story-submit.php` directly).
2. **Form:** They fill in:
   - Name (or client name)
   - Email
   - Client type (Individual, Corporate, Government, Royal)
   - Service category (e.g. Estate Management, Event Management)
   - Story title
   - Short summary (1–2 sentences, at least 20 characters)
   - Full story (at least 50 characters)
3. **Submit:** They click **Submit story**. The form posts to `success-story-handler.php`.
4. **What happens:** The story is saved in the `success_stories` table with **status = 'active'** and **appears on the website automatically**. The client sees a thank-you message and (optionally) the admin receives an email.

So: **clients write and submit via the form; stories are published automatically and show on the Success Stories page right away.**

---

## How a submitted story appears on the website

- The listing page (`success-stories.php`) shows only rows where **status = 'active'**.
- New submissions are stored with **status = 'active'**, so they appear on the site immediately after submission.

**To hide or unpublish a story later:**

1. Open your `success_stories` table (phpMyAdmin, MySQL client, or cPanel).
2. Find the row (by title/slug or date).
3. Set **status** from `active` to **`inactive`** and save. The story will no longer appear on the Success Stories page.

---

## Summary

| Step | Who | Where |
|------|-----|--------|
| Client fills form | Client | Success Stories page → "Share your story" → `success-story-submit.php` |
| Form is saved | System | `success-story-handler.php` → inserts into `success_stories` with `status = 'active'` |
| Story appears on site | Automatic | Story is live as soon as the form is submitted |

Stories are **published automatically** after submission. To remove one from the site, set its **status** to `inactive` in the database.
