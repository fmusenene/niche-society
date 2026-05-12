# Mailchimp Setup for Niche Society Newsletter

## Does Mailchimp require payment?

**No.** Mailchimp has a **free plan** ($0/month):

| Free plan includes        | Limit              |
|---------------------------|--------------------|
| Contacts                  | Up to **250**      |
| Emails you can send       | **500/day** (about 1,000/month) |
| Signup forms & landing pages | Yes             |
| Basic reports (opens, clicks) | Yes            |

You only need to pay if you want more contacts (e.g. 500+) or advanced automation. For a new or small newsletter, the free plan is enough.

---

## Step 1: Create a Mailchimp account

1. Go to **https://mailchimp.com** and click **Sign Up Free**.
2. Enter email, username, and password. No credit card required for the free plan.
3. Complete signup and confirm your email if asked.

---

## Step 2: Create an audience (list)

1. Log in to Mailchimp.
2. Go to **Audience** → **All contacts** (or **Audiences** in the menu).
3. Click **Create audience** (or use the default one).
4. Set **Audience name** (e.g. “Niche Society Newsletter”) and your **From name** and **From email**.
5. Save. You now have an **Audience** (this is your “list”).

---

## Step 3: Get your API key and List ID

### API key

1. Click your **profile/account** (top right) → **Account & Billing**.
2. Go to **Extras** → **API keys** (or **Integrations** → **API keys**).
3. Click **Create A Key**. Copy the key (it looks like `a1b2c3d4e5f6g7h8i9j0-us20`).
4. **Keep it secret** (like a password). Do not put it in public code or GitHub.

### List ID (Audience ID)

1. Go to **Audience** → **All contacts**.
2. Click **Settings** (or the **Manage audience** dropdown).
3. Under **Audience name and defaults** you’ll see **Audience ID** (e.g. `a1b2c3d4e5`). Copy it.

### Data center

Your API key has a suffix like `-us20`, `-us21`, etc. That is your **data center** (e.g. `us20`). The site uses this when calling Mailchimp’s API.

---

## Step 4: Add credentials to your site

1. In your project, copy the example config:
   - Copy `config/mailchimp.php.example` to `config/mailchimp.php`.
2. Edit `config/mailchimp.php` and set:
   - **MAILCHIMP_API_KEY** = your full API key (e.g. `a1b2c3d4e5f6g7h8i9j0-us20`).
   - **MAILCHIMP_LIST_ID** = your Audience ID (e.g. `a1b2c3d4e5`).
3. Do **not** commit `config/mailchimp.php` to Git (add it to `.gitignore`). Keep it only on the server.

Once this file exists and is correct, the newsletter signup form will add new subscribers to both your database and your Mailchimp audience.

---

## Step 5: Optional – Send campaigns from Mailchimp

To send “latest news and articles” emails:

1. In Mailchimp go to **Create** → **Email** (or **Campaigns** → **Create campaign**).
2. Choose **Regular campaign**, select your audience, and design the email (or use a template).
3. Schedule or send. Mailchimp will use your 500/day (1,000/month) limit on the free plan.

You can send a campaign whenever you have new blog content; no extra code is required on the site for that.

---

## Summary

| Question              | Answer                                      |
|-----------------------|---------------------------------------------|
| Does it require payment? | No for the free plan (up to 250 contacts). |
| How to set it up?     | Create account → Create audience → Get API key + List ID → Add to `config/mailchimp.php`. |
| What does the site do?| When someone subscribes on your site, they are saved in your database and, if Mailchimp is configured, added to your Mailchimp audience. |
| Who sends the emails? | You send campaigns from the Mailchimp dashboard (or use their automation on paid plans). |

For more: [Mailchimp Help – Get started](https://mailchimp.com/help/getting-started-with-mailchimp/).
