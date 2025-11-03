# JAI Free Translator

This is a WordPress translation plugin optimized for free client-side translation with clean sub-directory URLs perfect for Google Ads landing pages.

## Current Status: ✅ READY TO USE

### Implementation Complete
✅ Plugin rebranded to "JAI Free Translator"
✅ Removed all paid/pro features
✅ Default `url_structure` set to `'sub_directory'` for Google Ads
✅ Simplified configuration
✅ URL redirect handler (no external servers)
✅ Clean .htaccess rewrite rules
✅ Auto-translation from URL parameters
✅ Free Google Translate Widget - **NO API COSTS**

### How It Works: Hybrid Client-Side Approach

**1. Clean URLs for Google Ads**
- Visitor goes to: `yoursite.com/es/landing-page`
- .htaccess rewrites to: `url_addon/jai-translator.php?glang=es&gurl=landing-page`

**2. Smart Redirect**
- PHP handler redirects to: `yoursite.com/landing-page?gt_lang=es`
- Sets `googtrans` cookie for language persistence
- No external servers, runs entirely on your WordPress

**3. Auto-Translation**
- JavaScript detects `gt_lang=es` parameter
- Loads free Google Translate widget automatically
- Translates page client-side (takes 1-2 seconds)
- Uses Google's free service - **zero API costs**

**Benefits:**
- ✅ Clean URLs perfect for Google Ads campaigns
- ✅ SEO-friendly (can add hreflang tags)
- ✅ No external dependencies or paid servers
- ✅ Free Google Translate - unlimited translations
- ✅ Cookie persistence - language stays selected
- ⚠️ Slight translation delay (client-side)

## Features Included

- ✅ 103+ languages support
- ✅ Sub-directory URL structure (`example.com/es/page`)
- ✅ Multiple widget styles (dropdown, flags, float, globe, popup)
- ✅ WooCommerce email translation (if enabled)
- ✅ Browser language auto-detection
- ✅ Native language names
- ✅ Alternative flags (USA, Brazil, Mexico, Quebec, etc.)

## Features Removed

- ❌ Pro/Enterprise version checks
- ❌ Payment/upgrade prompts
- ❌ GTranslate TDN server pool
- ❌ Sub-domain URL structure (only sub-directory kept)
- ❌ License key validation

## Installation

1. Copy this directory to `wp-content/plugins/jai-free-translator/`
2. Activate the plugin in WordPress admin
3. Go to Settings → JAI Translator to configure
4. Select languages and widget style

## For Google Ads Landing Pages

To use sub-directory URLs for Google Ads:

1. Enable sub-directory URL structure in settings (default)
2. Set your default language (e.g., 'en')
3. Enable desired target languages
4. Create landing pages, use URLs like:
   - `yoursite.com/es/landing-page` (Spanish)
   - `yoursite.com/fr/landing-page` (French)
   - `yoursite.com/de/landing-page` (German)

## Development

See `CLAUDE.md` for full architecture documentation and development guidelines.

## License

GPL v2 or later (inherited from original GTranslate plugin)
