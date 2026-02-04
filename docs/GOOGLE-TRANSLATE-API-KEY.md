# How to Get and Set a Google Translate API Key

## Where to get the API key

1. **Open Google Cloud Console**  
   Go to: **https://console.cloud.google.com/**

2. **Create or select a project**
   - Click the project dropdown at the top.
   - Click **New Project**, give it a name (e.g. "Niche Society"), then **Create**.
   - Or select an existing project.

3. **Enable the Translation API**
   - In the left menu go to **APIs & Services** → **Library**.
   - Search for **Cloud Translation API**.
   - Open it and click **Enable**.

4. **Create an API key**
   - Go to **APIs & Services** → **Credentials**.
   - Click **+ Create Credentials** → **API key**.
   - Your key will appear (e.g. `AIzaSyB...`). Copy it.
   - (Recommended) Click **Restrict key**: set “API restrictions” to **Cloud Translation API** only, then save.

5. **Billing (required for Translation API)**
   - Go to **Billing** in the left menu and link a billing account.
   - New accounts get free trial credit; Translation has a free tier (e.g. 500,000 characters/month for Basic).  
   - See: https://cloud.google.com/translate/pricing

---

## Where to set it in your project

1. Open **`config/config.php`** in your project (the real one, not the `.example` file).

2. Add this line with your key (for example after the “Contact emails” section):

```php
// Google Translate API – for blog Arabic translations (get key from Google Cloud Console)
define('TRANSLATE_API_KEY', 'AIzaSyB...your-actual-key-here...');
```

3. Save the file. Do **not** commit this file to Git if it contains the real key (it should stay in `.gitignore`).

4. Run the backfill once so existing posts get Arabic:
   - In the browser: `https://yourdomain.com/backfill-blog-arabic.php?run=1`
   - Or in terminal: `php backfill-blog-arabic.php`

After that, when you switch the site to Arabic, the blog can use the translated titles and content.
