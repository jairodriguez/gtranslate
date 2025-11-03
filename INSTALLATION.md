# Installation Guide - JAI Free Translator

## Quick Start (3 Steps)

### Step 1: Upload Plugin to WordPress

**Option A: Via WordPress Admin**
1. Zip this entire directory: `zip -r jai-free-translator.zip .`
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Upload the zip file and click "Install Now"
4. Click "Activate Plugin"

**Option B: Via FTP/SSH**
1. Upload this entire directory to: `/wp-content/plugins/jai-free-translator/`
2. Go to WordPress Admin → Plugins
3. Find "JAI Free Translator" and click "Activate"

### Step 2: Configure .htaccess Rules

**Location:** Your WordPress `.htaccess` file in the site root (where `wp-config.php` is)

**How to Edit:**
1. Connect via FTP/SSH or use your hosting control panel's File Manager
2. Open `.htaccess` (usually at `/public_html/.htaccess`)
3. Copy ALL rules from `.htaccess-ready` file
4. Paste them **BEFORE** the `# BEGIN WordPress` line
5. Save the file

**Your .htaccess should look like this:**

```apache
# JAI Free Translator rules
<IfModule mod_rewrite.c>
RewriteEngine On
### BEGIN JAI Translator config ###
[... JAI Translator rules ...]
### END JAI Translator config ###
</IfModule>

# BEGIN WordPress
<IfModule mod_rewrite.c>
[... existing WordPress rules ...]
</IfModule>
# END WordPress
```

**Important:** If you installed the plugin to a different directory, edit line 16 of the pasted rules:
```apache
# Change this line if needed:
RewriteRule ^(...) /wp-content/plugins/jai-free-translator/url_addon/jai-translator.php?glang=$1&gurl=$2 [L,QSA]
                    ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                    Adjust this path if different
```

### Step 3: Configure Plugin Settings

1. Go to WordPress Admin → Settings → JAI Translator
2. Select your **default language** (e.g., English)
3. Select **target languages** you want to support
4. Choose a **widget look** (dropdown, flags, float, etc.)
5. Click **Save Changes**

## Testing Your Installation

### Test URLs

Try visiting these URLs (replace with your domain):
- `yoursite.com/es/` - Should load homepage in Spanish
- `yoursite.com/fr/about/` - Should load about page in French
- `yoursite.com/de/contact/` - Should load contact page in German

### What Should Happen

1. You visit `yoursite.com/es/landing-page`
2. Page redirects to `yoursite.com/landing-page?gt_lang=es`
3. Original page loads
4. After 1-2 seconds, Google Translate widget auto-translates to Spanish
5. Language persists via cookie (browsing other pages stays in Spanish)

### Troubleshooting

**"404 Not Found" on /es/ URLs**
- Check that .htaccess rules are properly added
- Verify Apache `mod_rewrite` is enabled
- Check file permissions on `.htaccess` (should be 644)
- Clear browser cache and try again

**Page doesn't translate**
- Open browser console (F12) and check for JavaScript errors
- Verify plugin is activated in WordPress Admin → Plugins
- Check that language is enabled in Settings → JAI Translator
- Try a different browser to rule out cache issues

**Wrong path errors in .htaccess**
- Edit `.htaccess` line 16 with your actual plugin path
- Path should be relative to web root: `/wp-content/plugins/jai-free-translator/`
- If WordPress is in a subdirectory, include it: `/blog/wp-content/plugins/jai-free-translator/`

## Google Ads Integration

### Setting Up Multilingual Campaigns

1. **Create Landing Pages**
   - Design your landing page at: `yoursite.com/special-offer`

2. **Use Language URLs in Ads**
   - Spanish Ad → `yoursite.com/es/special-offer`
   - French Ad → `yoursite.com/fr/special-offer`
   - German Ad → `yoursite.com/de/special-offer`

3. **Benefits**
   - Clean, professional URLs improve CTR
   - Pages auto-translate to visitor's language
   - Cookie maintains language during browsing
   - Zero API costs with unlimited translations

### Supported Languages (103 total)

All Google Translate languages supported. Use these language codes in URLs:
- `es` (Spanish), `fr` (French), `de` (German), `it` (Italian), `pt` (Portuguese)
- `ru` (Russian), `ja` (Japanese), `zh-CN` (Chinese Simplified), `ko` (Korean)
- `ar` (Arabic), `hi` (Hindi), `nl` (Dutch), `sv` (Swedish), `pl` (Polish)
- And 88 more! See `.htaccess-ready` for complete list

## Advanced Configuration

### Customizing Widget Appearance

Go to Settings → JAI Translator:
- **Widget Look**: Dropdown, Flags, Float, Globe, Popup, etc.
- **Flag Size**: 16px, 24px, 32px, or 48px
- **Native Language Names**: Show "Español" instead of "Spanish"
- **Flag Style**: Choose alternative flags (USA, Brazil, Mexico, Quebec, etc.)

### Adding Language Selector to Menus

**Option 1: Widget Area**
- Go to Appearance → Widgets
- Drag "JAI Translator" widget to your sidebar

**Option 2: Shortcode**
- Add `[jaitranslator]` to any page/post
- Or in PHP templates: `<?php echo do_shortcode('[jaitranslator]'); ?>`

**Option 3: Menu Integration**
- Settings → JAI Translator
- "Show in menu" dropdown → Select your menu
- Language selector appears in that menu

### Browser Language Auto-Detection

Enable in Settings → JAI Translator:
- Check "Auto switch to browser language"
- Visitors with Spanish browser will see Spanish automatically
- Only triggers once per visitor (uses localStorage)

## File Structure

```
jai-free-translator/
├── jai-translator.php          # Main plugin file
├── js/
│   ├── base.js             # Core JavaScript (auto-translation logic)
│   ├── dropdown.js         # Dropdown widget
│   ├── flags.js            # Flag widget
│   └── ...                 # Other widget types
├── url_addon/
│   ├── config.php          # Language configuration
│   ├── jai-translator.php      # URL redirect handler
│   └── translator.php      # Server-side translation engine
├── flags/                   # SVG/PNG flags (103 languages)
├── .htaccess-ready         # Ready-to-use rewrite rules
├── INSTALLATION.md         # This file
├── README_CUSTOM.md        # Usage guide
└── CLAUDE.md               # Developer documentation
```

## Support

This is JAI Free Translator - a custom WordPress translation plugin. For:
- **Plugin questions:** Check README_CUSTOM.md and CLAUDE.md
- **Google Translate issues:** https://support.google.com/translate

## License

GPL v2 or later
