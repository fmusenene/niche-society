# Step-by-step: Contact form email & auto-reply on cPanel (SMTP)

This guide gets the contact form working **cleanly** on cPanel so that:

1. **You** receive every message at `info@niche-society.com`.
2. **The sender** sees a clear on-page message (success or problem).
3. **The sender** receives an automatic “Thank you” email (and is told to check spam if needed).

The site uses **SMTP** when you configure it. SMTP is more reliable than PHP `mail()` on cPanel.

---

## What the sender sees

- **Success (emails sent):**  
  *“Thank you! We have received your message and sent a confirmation to your email. Check your spam folder if you don’t see it. We will get back to you as soon as possible.”*

- **Success (message saved, confirmation email failed):**  
  *“Thank you! We have received your message. We couldn’t send the confirmation email to your inbox; we will still get back to you as soon as possible.”*

- **Error (e.g. validation):**  
  A clear message explaining what to fix (e.g. invalid email, missing privacy agreement).

So the sender is **always notified** on the page whether the message was received and whether a confirmation email was sent.

---

## Step 1: Create the email account on cPanel

1. Log in to **cPanel**.
2. Go to **Email** → **Email Accounts**.
3. Click **Create**.
4. Create the address your site will use to send (and receive) contact messages:
   - **Username:** `info` (so the address is `info@yourdomain.com`).
   - **Domain:** Select your domain (e.g. `niche-society.com`).
   - **Password:** Set a strong password. Click **Generate**, copy it, and save it somewhere safe (you will use it in Step 3).
5. Click **Create**.

You will use this inbox to read the messages that the form sends to you. The same account is used to **send** the notification to you and the auto-reply to the sender.

---

## Step 2: Get SMTP details from cPanel

1. In cPanel, stay in **Email** → **Email Accounts**.
2. Find the account you created (e.g. `info@niche-society.com`) and click **Connect Devices** (or **More** → **Configure Email Client** / **Manual Settings**).
3. Note the **Manual Settings** (or “SMTP” section). You need:
   - **Incoming / Outgoing server:**  
     Often `mail.niche-society.com` or `mail.yourdomain.com`. Some hosts use the domain only, e.g. `niche-society.com`. Write this down as **SMTP host**.
   - **Ports:**
     - **SSL:** 465 (recommended)
     - **TLS:** 587  
     Use **one** of these. 465 is common on cPanel.
   - **Username:** `info@niche-society.com` (full email).
   - **Password:** The same password you set in Step 1.

Typical values:

| Setting    | Example value              |
|-----------|----------------------------|
| SMTP host | `mail.niche-society.com`   |
| Port      | `465` (SSL) or `587` (TLS) |
| Username  | `info@niche-society.com`   |
| Password  | (the one you set)          |

---

## Step 3: Create `config/email.php` on the server

1. In cPanel **File Manager**, go to your site folder (e.g. `public_html`) and then into the **config** folder.
2. You should see **email.php.example**.  
   - If you don’t, upload it from your project: it’s in `config/email.php.example`.
3. **Copy** `email.php.example` to a new file named **email.php** (same folder: `config/`).
4. **Edit** `config/email.php` and set the values you noted in Step 2:

```php
define('SMTP_ENABLED', true);

define('SMTP_HOST', 'mail.niche-society.com');   // Your host’s SMTP server
define('SMTP_PORT', 465);                         // 465 for SSL, 587 for TLS
define('SMTP_SECURE', 'ssl');                     // 'ssl' for 465, 'tls' for 587
define('SMTP_USERNAME', 'info@niche-society.com');
define('SMTP_PASSWORD', 'your-email-password');   // The password from Step 1
define('SMTP_FROM_EMAIL', 'info@niche-society.com');
define('SMTP_FROM_NAME', 'Niche Society');
```

5. **Save** the file.

Important:

- Use the **exact** SMTP host and port your host shows (e.g. `mail.niche-society.com`, port `465` or `587`).
- Do **not** commit `config/email.php` to Git (it contains your password). It’s in `.gitignore`.

---

## Step 4: Confirm main config

1. In **File Manager**, open **config/config.php** (same site).
2. Ensure the contact address matches the email account you use for SMTP:

```php
define('CONTACT_EMAIL', 'info@niche-society.com');
define('MAIL_FROM', 'info@niche-society.com');
```

3. Save if you changed anything.

---

## Step 5: Ensure these files exist on the server

In the **same folder** as `contact.php` and `contact-handler.php` (usually `public_html` or your site root), you should have:

- `contact.php` – contact page with the form.
- `contact-handler.php` – processes the form and sends both emails.
- `config/email.php` – the file you created in Step 3.
- `functions/mail-smtp.php` – SMTP helper (part of the project).

The form already posts to `contact-handler.php`; no change needed there.

---

## Step 6: Test the contact form

1. Open **https://niche-society.com/contact.php** (or your contact URL).
2. Fill the form with **your own** email as “sender”:
   - Full Name  
   - Email (your real address)  
   - Phone  
   - Service (any)  
   - Message (at least 10 characters)  
   - Check “I agree to the privacy policy”.
3. Click **Send** (or “إرسال الرسالة”).

**Expected:**

- You see the **success message** on the page (e.g. “Thank you! We have received your message and sent a confirmation to your email…”).
- **Your inbox** (info@…) receives the contact message.
- **The sender’s inbox** (the email you entered) receives the automatic “Thank you for contacting us” reply.  
  If you don’t see it, check **spam/junk** and the success message (it tells the user to check spam).

If something fails, go to Step 7.

---

## Step 7: If emails still don’t arrive

**A. Check the on-page message**

- The contact page now shows different messages depending on what worked (message saved, confirmation sent or not). Use that to see if the script thinks it sent or not.

**B. Check the log file**

- On the server, open **logs/contact-form-emails.log** (in the same folder as `contact-handler.php`).
- After a test submit it will say whether the “Admin notification” and “Auto-reply” were **SENT** or **FAILED**.  
- If both are FAILED, SMTP is likely wrong or blocked.

**C. Check SMTP settings**

- **Port:** 465 → `SMTP_SECURE` must be `'ssl'`. 587 → `SMTP_SECURE` must be `'tls'`.
- **Host:** Some hosts use `mail.yourdomain.com`, others `yourdomain.com` or a different name. Use exactly what cPanel shows under “Manual Settings” / “Connect Devices”.
- **Password:** No spaces; use the password you set for that email account. If you changed it in cPanel, update `config/email.php`.

**D. Email deliverability (cPanel)**

1. In cPanel go to **Email** → **Email Deliverability** (or **Track Delivery**).
2. Select your domain and run the check.
3. Fix any issues it reports (e.g. SPF, DKIM, “Relay Access Denied”) so that outgoing mail is not blocked.

**E. Firewall / host**

- Some hosts block outbound SMTP from shared hosting. If the log always says FAILED and the settings are correct, contact support and ask if they allow SMTP (port 465 or 587) for your account.

---

## Quick reference

| What | Where |
|------|--------|
| Form page | https://niche-society.com/contact.php |
| Form action | `contact-handler.php` |
| SMTP config | `config/email.php` (copy from `config/email.php.example`) |
| Email to you | `CONTACT_EMAIL` in `config/config.php`; same as SMTP account |
| Auto-reply to sender | Sent by `contact-handler.php` via SMTP to the email they entered |
| On-page feedback | Success/error message set by `contact-handler.php` (redirect back to contact.php) |
| Log file | `logs/contact-form-emails.log` |

---

## Summary

1. Create **info@niche-society.com** (or your address) in cPanel **Email Accounts** and set a password.
2. Get **SMTP host, port (465 or 587), username, password** from **Email Accounts** → **Connect Devices** (or Manual Settings).
3. Copy **config/email.php.example** to **config/email.php** and fill in SMTP settings; set **SMTP_ENABLED** to **true**.
4. Confirm **CONTACT_EMAIL** / **MAIL_FROM** in **config/config.php** match that address.
5. Test the form: you should get the message, the sender should get the auto-reply, and **the sender is notified on the page** (success message or error). Check **logs/contact-form-emails.log** if something fails.

After this, email is delivered via SMTP, and the sender is clearly notified on the page and by the automatic reply.
