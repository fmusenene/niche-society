# Step-by-step: Contact form & auto-reply on cPanel

Your site already has:

1. **Contact form** at [https://niche-society.com/contact.php](https://niche-society.com/contact.php) that posts to `contact-handler.php`.
2. **Two emails** when someone submits:
   - **You** receive the message at `info@niche-society.com`.
   - **The sender** receives an automatic “Thank you” reply (in Arabic or English).

For this to work on cPanel, the server must be able to send email. Follow the steps below.

---

## What happens when someone submits the form

1. Form is sent to `contact-handler.php`.
2. Handler validates input, saves to the `contact_forms` table, then:
   - Sends **one email** to `info@niche-society.com` (the message details).
   - Sends **one email** to the sender’s address (auto-reply: “Thank you for contacting us…”).
3. User is redirected back to the contact page with a success message.

No code change is needed for this flow. You only need to configure email on cPanel.

---

## Step 1: Create the email account on cPanel (if it doesn’t exist)

1. Log in to **cPanel**.
2. Open **Email** → **Email Accounts**.
3. Click **Create**.
4. Create an account for the address your site uses:
   - **Username:** `info` (or the part before @).
   - **Domain:** `niche-society.com` (select your domain).
   - **Password:** Set a strong password (you can use “Generate” and save it).
5. Click **Create**.

You don’t need to check this inbox for the script to *send*; having the account helps the server accept sending from `info@niche-society.com`. You will use this inbox to read the messages that the form sends to you.

---

## Step 2: Confirm config on the server

On the server, open **File Manager** → your site folder (e.g. `public_html`) → `config/config.php`.

Check that these match your domain and the email you created:

```php
define('CONTACT_EMAIL', 'info@niche-society.com');
define('MAIL_FROM', 'info@niche-society.com');
```

If your contact address is different, change both to that address (and use the same address in Step 1). Save the file.

---

## Step 3: Make sure the form and handler are on the server

In File Manager, in the same folder as `index.php` and `contact.php`, confirm you have:

- `contact.php` – contact page with the form.
- `contact-handler.php` – script that sends the two emails.

The form already posts to `contact-handler.php`; no change needed there.

---

## Step 4: Test the contact form

1. Open **https://niche-society.com/contact.php** in a browser.
2. Fill in:
   - Full Name  
   - Email (use your own or a test address)  
   - Phone  
   - Service (any)  
   - Message (at least 10 characters)  
   - Check “I agree to the privacy policy”.
3. Click **Send** (or “إرسال الرسالة” in Arabic).

**Expected:**

- You see a success message on the page.
- You receive the message at **info@niche-society.com** (or whatever you set in `CONTACT_EMAIL`).
- The **sender’s email** receives the automatic “Thank you for contacting us” reply.

If one or both emails don’t arrive, go to Step 5.

---

## Step 5: If emails don’t arrive (troubleshooting)

**A. Check spam/junk**

- Check the Junk/Spam folder for both:
  - `info@niche-society.com`
  - The test sender address.

**B. Check PHP mail on cPanel**

1. In cPanel, go to **Email** → **Email Deliverability** (or **Track Delivery**).
2. Enter your domain (`niche-society.com`) and run the check.
3. Fix any issues it reports (e.g. SPF/DKIM or “Relay Access Denied”).

**C. Check that PHP is allowed to send mail**

- Some hosts restrict `mail()` or require sending from a domain email.
- Ensure the **From** address in your script is `info@niche-society.com` (already set via `CONTACT_EMAIL` / `MAIL_FROM` in config).

**D. Check logs on the server**

After a test submit, check:

- `logs/contact-form-emails.log` (in your site root, next to `contact-handler.php`)  
  - It will say whether the “admin” email and “auto-reply” were SENT or FAILED.
- `logs/email-errors.log` (if it exists) for PHP mail errors.

**E. Use SMTP instead of PHP mail() (if your host requires it)**

If the host says PHP `mail()` is disabled or unreliable, you need to send via SMTP:

1. In cPanel, note **Email** → **Email Accounts** → **Connect Devices** (or **SMTP**) for `info@niche-society.com`:
   - Server: often `mail.niche-society.com` or your host’s SMTP.
   - Port: 465 (SSL) or 587 (TLS).
   - Username: `info@niche-society.com`, Password: the one you set for that account.
2. The code would need to be changed to use an SMTP library (e.g. PHPMailer) and these settings. If you want, we can add a small “SMTP mode” using PHPMailer and a config like `config/email.php` so you only fill in server, port, user, and password.

For most cPanel accounts, Step 1 + correct `CONTACT_EMAIL`/`MAIL_FROM` is enough and both “message to you” and “auto-reply to sender” work without code changes.

---

## Quick reference

| What | Where |
|------|--------|
| Form page | https://niche-society.com/contact.php |
| Form action | `contact-handler.php` (same folder as contact.php) |
| Email to you | `CONTACT_EMAIL` in `config/config.php` (e.g. info@niche-society.com) |
| Auto-reply to sender | Sent by `contact-handler.php` to the email they entered |
| Log file | `logs/contact-form-emails.log` (after submit) |

---

## Summary

1. Create **info@niche-society.com** (or your chosen address) in cPanel **Email Accounts**.
2. Set **CONTACT_EMAIL** and **MAIL_FROM** in `config/config.php` on the server to that address.
3. Submit a test from https://niche-society.com/contact.php and check:
   - Your inbox for the message.
   - Sender inbox for the automatic “Thank you” reply.
4. If nothing arrives, use **Email Deliverability** and `logs/contact-form-emails.log`; if the host blocks `mail()`, switch to SMTP (we can add that next).

After this, the contact form will both send you the message and send the sender an automatic response on your live site.
