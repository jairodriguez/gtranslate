# .htaccess Setup for ai.withjai.com

## Quick Fix for 404 Error on /es/ URLs

Your URL `https://ai.withjai.com/es/gmb-ranking-ranking/` is giving a 404 because Apache doesn't know how to handle the `/es/` language prefix yet.

## Solution: Add Rewrite Rules to .htaccess

### Step 1: Access Your .htaccess File

Location: `/public_html/.htaccess` (or your WordPress root directory)

Access via:
- FTP/SFTP
- cPanel File Manager
- SSH/Terminal

### Step 2: Add JAI Translator Rules

**IMPORTANT:** Add these rules **BEFORE** the `# BEGIN WordPress` line.

Copy and paste this entire block:

```apache
### BEGIN JAI Translator config ###
<IfModule mod_rewrite.c>
RewriteEngine On

# Prevent double language codes in URL (e.g., /es/fr/page -> /es/page)
RewriteRule ^(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)/(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)/(.*)$ /$1/$3 [R=301,L]

# Main rule: Rewrite /es/page to jai-translator.php handler
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)/(.*)$ /wp-content/plugins/jai-free-translator/url_addon/jai-translator.php?glang=$1&gurl=$2 [L,QSA]

# Add trailing slash to language-only URLs (e.g., /es -> /es/)
RewriteRule ^(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)$ /$1/ [R=301,L]

</IfModule>
### END JAI Translator config ###
```

### Step 3: Your .htaccess Should Look Like This

```apache
# JAI Translator rules (ADD THESE FIRST)
### BEGIN JAI Translator config ###
<IfModule mod_rewrite.c>
RewriteEngine On
[... JAI Translator rules from above ...]
</IfModule>
### END JAI Translator config ###

# BEGIN WordPress
# The WordPress rules start here
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
```

### Step 4: Save and Test

1. Save the `.htaccess` file
2. Visit: `https://ai.withjai.com/es/gmb-ranking-ranking/`
3. Should now redirect and translate!

## How It Works

1. **URL Request:** `https://ai.withjai.com/es/gmb-ranking-ranking/`
2. **Apache Rewrite:** `/es/gmb-ranking-ranking/` → `/wp-content/plugins/jai-free-translator/url_addon/jai-translator.php?glang=es&gurl=gmb-ranking-ranking/`
3. **PHP Redirect:** Script redirects to `https://ai.withjai.com/gmb-ranking-ranking/?gt_lang=es`
4. **JavaScript:** Detects `gt_lang=es` parameter and translates page to Spanish

## Troubleshooting

### Still Getting 404?

**Check 1: Verify mod_rewrite is enabled**
```bash
# SSH into server and run:
apache2ctl -M | grep rewrite
# Should show: rewrite_module (shared)
```

**Check 2: Verify plugin path is correct**
The rewrite rule assumes plugin is at:
```
/wp-content/plugins/jai-free-translator/
```

If your plugin is in a different location, update line with the path:
```apache
RewriteRule ^(lang-codes)/(.*)$ /YOUR/CUSTOM/PATH/jai-translator.php?glang=$1&gurl=$2 [L,QSA]
```

**Check 3: Verify .htaccess is being read**
```bash
# Add this at top of .htaccess to test:
# Deny from all
# If you get 403 Forbidden, .htaccess is working
# Remove the line after testing
```

**Check 4: Check file permissions**
```bash
# .htaccess should be readable:
chmod 644 .htaccess
```

### Test Checklist

After adding the rules, test these URLs:

1. ✅ English (original): `https://ai.withjai.com/gmb-ranking-ranking/`
2. ✅ Spanish: `https://ai.withjai.com/es/gmb-ranking-ranking/`
3. ✅ French: `https://ai.withjai.com/fr/gmb-ranking-ranking/`
4. ✅ German: `https://ai.withjai.com/de/gmb-ranking-ranking/`

All should load and show the translated version!

## Quick Copy-Paste Version

If you just want the essential rule:

```apache
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(es|fr|de|pt|it|ru|ja|zh-CN|ar|nl)/(.*)$ /wp-content/plugins/jai-free-translator/url_addon/jai-translator.php?glang=$1&gurl=$2 [L,QSA]
</IfModule>
```

This shorter version only includes the languages you're likely using. Add it before `# BEGIN WordPress`.

## Need Help?

Check these files in the plugin directory:
- `.htaccess-ready` - Full rules with all 103 languages
- `INSTALLATION.md` - Complete installation guide
- `QUICK_START.md` - Quick setup guide

---
**Next:** After adding rules, test `https://ai.withjai.com/es/gmb-ranking-ranking/` again!
