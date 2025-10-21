# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

GTranslate is a WordPress plugin that provides website translation functionality using Google Translate. It supports 103+ languages and offers both free (on-the-fly client-side translation) and paid versions (server-side Translation Delivery Network with SEO features).

**Key Technologies:**
- PHP (WordPress plugin architecture)
- Vanilla JavaScript (ES5/ES6 compatible)
- CSS for styling language selectors
- SVG flags for visual language indicators

## Architecture

### Core Components

1. **Main Plugin File** (`gtranslate.php` - ~2770 lines)
   - WordPress plugin initialization and hooks
   - Widget registration and rendering
   - Settings page management
   - Email translation features (for WooCommerce)
   - Shortcode handlers: `[gtranslate]`, `[gt-link]`

2. **URL Add-on** (`url_addon/`)
   - `config.php`: Configuration for paid versions (main language, server list)
   - `gtranslate.php`: Translation proxy logic for sub-domain/sub-directory URL structures
   - `gtranslate-email.php`: WooCommerce email translation handling

3. **JavaScript Modules** (`js/`)
   - `base.js`: Core JavaScript engine - handles language switching, cookie management, auto-detection
   - Widget-specific scripts: `dropdown.js`, `float.js`, `flags.js`, `dwf.js`, `fd.js`, `fn.js`, `fc.js`, `lc.js`, `ln.js`, `globe.js`, `popup.js`
   - Each widget type has its own JavaScript module loaded on-demand

4. **Assets** (`flags/`)
   - SVG and PNG flags in multiple sizes (16, 24, 32, 48, svg)
   - 103+ language flags with alternative flags (USA, Brazil, Mexico, Quebec, Canada, Argentina, Colombia)

### Translation Modes

**Free Version (url_structure: 'none')**
- Client-side translation using Google Translate Widget
- JavaScript loads `translate.google.com/translate_a/element.js`
- Uses `googtrans` cookie to persist language selection
- Language changes trigger Google's TranslateElement

**Paid Versions (url_structure: 'sub_directory' or 'sub_domain')**
- Server-side Translation Delivery Network (TDN)
- Sub-directory: `example.com/es/page`
- Sub-domain: `es.example.com/page`
- Custom domains: `example.fr`, `example.de`
- Translations cached on proxy servers

### Widget Rendering Flow

1. Settings stored in WordPress options table (`get_option('GTranslate')`)
2. Settings passed to JavaScript via `gtranslateSettings` global variable
3. `base.js` initializes and sets up language links with `data-gt-lang` attributes
4. Widget-specific JS adds UI styling and interactions
5. Language clicks trigger either:
   - `doGTranslate()` function (free version - client-side)
   - Direct navigation to translated URL (paid versions)

## Development Commands

### No Build System
This plugin does not use a build system. All JavaScript and CSS are vanilla/plain files loaded directly.

### Testing Locally
1. Install in WordPress: Copy to `wp-content/plugins/gtranslate/`
2. Activate plugin via WordPress admin
3. Configure at Settings → GTranslate
4. Test on frontend by viewing language selector

### File Modification Workflow
- **PHP changes**: Edit files directly, reload WordPress admin/frontend
- **JS changes**: Edit `js/*.js` files, clear browser cache
- **CSS changes**: Inline styles in `gtranslate.php` or widget-specific CSS

### No Linting/Testing Commands
- No automated tests in repository
- No package.json or composer.json
- Manual testing required in WordPress environment

## Code Organization Patterns

### Settings Structure
Settings are stored as a single array in WordPress options:
- `default_language`: Original site language (e.g., 'en')
- `languages`: Array of enabled language codes
- `widget_look`: Visual style (dropdown, flags, float, globe, popup, etc.)
- `pro_version`, `enterprise_version`: Feature flags
- `flag_size`: 16, 24, 32, or 48 pixels
- `native_language_names`: Show languages in native script vs English
- `detect_browser_language`: Auto-switch based on Accept-Language header
- `url_structure`: 'none', 'sub_directory', 'sub_domain'

### JavaScript Initialization Pattern
```javascript
(function(){
    var gt = window.gtranslateSettings || {};
    gt = gt[document.currentScript.getAttribute('data-gt-widget-id')] || gt;
    // ... initialization code
})();
```
Each widget JS is an IIFE that reads settings from `gtranslateSettings` global.

### Language Code Handling
- Standard codes: ISO 639-1 (e.g., 'en', 'es', 'fr')
- Special cases:
  - Chinese: 'zh-CN' (Simplified), 'zh-TW' (Traditional)
  - Hebrew: 'iw' (Google Translate code) vs 'he' (standard)
  - Browser language detection maps 'he' → 'iw'

### URL Structure Implementation
**Sub-directory mode:**
- Original: `example.com/about/`
- Translated: `example.com/es/about/`
- .htaccess rewrite rules redirect to `url_addon/gtranslate.php`

**Sub-domain mode:**
- Original: `example.com/about/`
- Translated: `es.example.com/about/`
- DNS CNAME records point to Translation Delivery Network

## Important Configuration

### Main Language Configuration
The `$main_lang` variable in `url_addon/config.php` MUST match the `default_language` in WordPress settings. The plugin auto-updates this on settings save.

### Server List
Paid version uses a pool of translation proxy servers defined in `url_addon/config.php`:
```php
$servers = array('van', 'kars', 'sis', 'dvin', 'ani', 'evn', 'vagh', 'step', 'tigr');
```

### Debug Mode
Setting `$debug = true` in `url_addon/config.php` writes sensitive info to `url_addon/debug.txt` - ensure this is disabled in production.

## WordPress Integration Points

### Hooks Used
- `widgets_init`: Register GTranslate widget
- `admin_menu`: Add settings page
- `init`: Enqueue scripts
- `send_headers`: Add DNS prefetch headers
- `script_loader_tag`: Add async/data attributes to script tags
- `walker_nav_menu_start_el`: Inject language selector in menus
- `wp_mail`: Translate WooCommerce emails (paid)

### Shortcodes
- `[gtranslate]`: Renders language selector
- `[gt-link lang="es" label="Español" widget_look="flags"]`: Single language link

### Widget
Class: `GTranslate extends WP_Widget`
Renders language selector in sidebars/widget areas.

## Key Language Array
Two parallel arrays used throughout codebase:
- `lang_array_english`: Language names in English (for display)
- `lang_array_native`: Language names in native script (e.g., "日本語" for Japanese)

Selection controlled by `native_language_names` setting.

## Performance Optimizations

### Lazy Loading
- Widget-specific JavaScript only loads for active widget type
- Flag images use lazy loading attributes
- Google Translate library loads on first language selector interaction (pointerenter event)

### CDN Option
Flags and scripts can be served from `cdn.gtranslate.net` when `enable_cdn` setting is true.

### Caching Compatibility
Plugin tested with: Autoptimize, LiteSpeed Cache, W3 Total Cache, WP Fastest Cache, WP Rocket, WP Super Cache, WP Optimize, SG Optimizer.
Special excludes added for LiteSpeed Cache to prevent JavaScript minification issues.

## Common Modification Patterns

### Adding a New Widget Look
1. Create `js/{widget-name}.js` with widget-specific rendering
2. Add option to settings page in `gtranslate.php` admin interface
3. Add conditional script enqueuing in `enqueue_scripts()` method
4. Define widget HTML structure and CSS

### Changing Language List
Edit the `$lang_array` in `gtranslate.php` (around line 50) - this is the master list used for both PHP and JavaScript.

### Modifying Translation Proxy Behavior
Edit `url_addon/gtranslate.php` - this file handles the server-side translation proxying for paid versions.

## Security Considerations

- XSS vulnerabilities fixed in versions 2.8.52 and 2.8.65
- Settings page uses `esc_attr()`, `esc_html()`, `esc_url()` for output escaping
- Debug mode can expose sensitive information - must be disabled in production
- Email translation feature encodes data to avoid firewall blocking
