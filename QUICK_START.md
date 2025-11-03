# JAI Free Translator - Quick Start Guide

## Installation (2 minutes)

### Step 1: Upload to WordPress
```bash
# Copy entire directory to:
/wp-content/plugins/jai-free-translator/
```

### Step 2: Activate
1. WordPress Admin → Plugins
2. Find "JAI Free Translator"
3. Click **Activate**

### Step 3: Configure .htaccess
Copy from `.htaccess-ready` and paste **BEFORE** `# BEGIN WordPress`:

```apache
### BEGIN JAI Translator config ###
RewriteRule ^(lang-codes)/(.*)$ /wp-content/plugins/jai-free-translator/url_addon/jai-translator.php?glang=$1&gurl=$2 [L,QSA]
### END JAI Translator config ###

# BEGIN WordPress
...your existing WordPress rules...
```

### Step 4: Basic Settings
1. Go to: **Settings → JAI Translator**
2. Default Language: `English`
3. Target Languages: Select languages you want
4. Widget Look: Choose style (e.g., "Dropdown with flags")
5. Click **Save Changes**

## Usage

### Display Language Selector

**In Posts/Pages:**
```
[jaitranslator]
```

**In PHP Templates:**
```php
<?php echo do_shortcode('[jaitranslator]'); ?>
```

**As Widget:**
Appearance → Widgets → Add "JAI Translator"

### URL Structure

Your URLs will work like:
- Original: `yoursite.com/about/`
- Spanish: `yoursite.com/es/about/`
- French: `yoursite.com/fr/about/`
- German: `yoursite.com/de/about/`

## Testing

1. Add `[jaitranslator]` to any page
2. Publish and view
3. Click a language flag
4. Page should translate automatically

## Troubleshooting

**Translation not working?**
- Check .htaccess is configured
- Clear browser cache (Ctrl+F5)
- Check JavaScript console for errors

**404 errors on /es/ URLs?**
- Verify .htaccess rules are active
- Check file path is correct
- Ensure mod_rewrite is enabled

**Language selector not appearing?**
- Check shortcode is correct: `[jaitranslator]`
- Verify plugin is activated
- Clear any caching plugins

## Support Files

- `TESTING_CHECKLIST.md` - Complete testing guide
- `TEST_RESULTS.md` - Validation results
- `INSTALLATION.md` - Detailed installation
- `README_CUSTOM.md` - Feature documentation

## Quick Facts

- ✅ 100% Free - Zero API costs
- ✅ 103+ Languages supported
- ✅ Google Ads optimized URLs
- ✅ Client-side translation
- ✅ Cookie-based persistence
- ✅ Multiple widget styles

**Version:** 1.1.0  
**Status:** Production Ready  
**Last Updated:** 2025-11-02
