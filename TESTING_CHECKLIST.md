# JAI Free Translator - Testing Checklist

## Automated Tests Completed ✅

### PHP Syntax Validation
- ✅ `jai-translator.php` - No syntax errors
- ✅ `url_addon/jai-translator.php` - No syntax errors
- ✅ `url_addon/jai-translator-email.php` - No syntax errors
- ✅ `url_addon/translator.php` - No syntax errors
- ✅ `url_addon/config.php` - No syntax errors

### JavaScript Validation
- ✅ `js/base.js` - Valid syntax
- ✅ Global variable: `window.jaitranslatorSettings` - Correct
- ✅ Function name: `doJAITranslate()` - Correct

### File References
- ✅ Plugin header correct: "JAI Free Translator"
- ✅ Plugin paths: `/jai-free-translator/`
- ✅ .htaccess paths: `/jai-free-translator/url_addon/jai-translator.php`
- ✅ CSS file: `jai-translator-notices.css`

## WordPress Installation Tests

### Step 1: Upload Plugin
```bash
# Copy to WordPress plugins directory
cp -r /path/to/jai-free-translator /path/to/wordpress/wp-content/plugins/
```

**Expected Result:**
- Plugin appears in WordPress Admin → Plugins
- Listed as "JAI Free Translator v1.1.0"
- Author: JAI

### Step 2: Activate Plugin
**Actions:**
1. Go to WordPress Admin → Plugins
2. Find "JAI Free Translator"
3. Click "Activate"

**Expected Result:**
- ✅ Activates without errors
- ✅ Settings link appears
- ✅ No PHP warnings or errors

### Step 3: Check Settings Page
**Actions:**
1. Go to Settings → JAI Translator

**Expected Result:**
- ✅ Settings page loads
- ✅ All options display correctly
- ✅ Language list appears
- ✅ Widget style options available

### Step 4: Configure Basic Settings
**Actions:**
1. Select default language: English
2. Select target languages: Spanish, French, German
3. Choose widget look: Dropdown with flags
4. Click "Save Changes"

**Expected Result:**
- ✅ Settings save successfully
- ✅ Success message displays
- ✅ Options persist after page reload

### Step 5: Test Shortcode
**Actions:**
1. Create new page/post
2. Add shortcode: `[jaitranslator]`
3. Publish and view page

**Expected Result:**
- ✅ Language selector appears
- ✅ Selected languages display
- ✅ Flags show correctly
- ✅ No JavaScript errors in console

### Step 6: Test Legacy Shortcode
**Actions:**
1. Add old shortcode: `[gtranslate]`
2. View page

**Expected Result:**
- ✅ Still works (backward compatibility)
- ✅ Language selector appears

### Step 7: Test Widget
**Actions:**
1. Go to Appearance → Widgets
2. Add "JAI Translator" widget to sidebar
3. View frontend

**Expected Result:**
- ✅ Widget appears in sidebar
- ✅ Language selector functional

### Step 8: Test .htaccess Rules
**Actions:**
1. Copy rules from `.htaccess-ready`
2. Paste BEFORE `# BEGIN WordPress` in site's `.htaccess`
3. Visit URL: `yoursite.com/es/`

**Expected Result:**
- ✅ Redirects to homepage with language parameter
- ✅ Page translates to Spanish
- ✅ No 404 errors

### Step 9: Test URL Translation
**Actions:**
1. Create a page at: `yoursite.com/about/`
2. Visit: `yoursite.com/es/about/`

**Expected Result:**
- ✅ Page loads correctly
- ✅ Content translates to Spanish
- ✅ URL stays clean (no query parameters visible)

### Step 10: Test Language Switching
**Actions:**
1. Click on language selector
2. Select different language

**Expected Result:**
- ✅ Page translates
- ✅ Google Translate widget appears briefly
- ✅ Content displays in selected language
- ✅ Cookie persists language choice

## JavaScript Console Tests

### Check for Errors
**Actions:**
1. Open browser DevTools (F12)
2. Go to Console tab
3. Load page with translator

**Expected Result:**
- ✅ No JavaScript errors
- ✅ No warnings about missing variables
- ✅ `jaitranslatorSettings` object exists

### Check Network Requests
**Actions:**
1. Open DevTools → Network tab
2. Trigger translation

**Expected Result:**
- ✅ Google Translate scripts load
- ✅ No 404 errors for CSS/JS files
- ✅ Flag images load correctly

## CSS/Styling Tests

### Visual Inspection
**Actions:**
1. Test each widget style:
   - Dropdown
   - Flags
   - Float
   - Globe
   - Popup

**Expected Result:**
- ✅ All styles render correctly
- ✅ No broken layouts
- ✅ Flags display properly
- ✅ Responsive on mobile

## Browser Compatibility

Test in:
- ✅ Chrome/Edge
- ✅ Firefox
- ✅ Safari
- ✅ Mobile browsers

## Performance Tests

**Actions:**
1. Check page load time with translator
2. Test with 10+ languages enabled

**Expected Result:**
- ✅ Minimal impact on page load
- ✅ Lazy loading works for flags
- ✅ No memory leaks

## Known Working Features

Based on successful rebrand:
1. ✅ All PHP files have valid syntax
2. ✅ JavaScript files are syntactically correct
3. ✅ File paths updated correctly
4. ✅ Plugin headers correct
5. ✅ Shortcodes registered (both new and legacy)
6. ✅ CSS classes updated
7. ✅ .htaccess rules updated

## Issues to Watch For

### Potential Issues:
1. **Cache plugins** - May cache old gtranslate references
   - Solution: Clear all caches after activation

2. **Old shortcodes in content** - Sites using `[gtranslate]`
   - Solution: Both work, but recommend updating to `[jaitranslator]`

3. **Custom theme code** - If theme hardcoded old class names
   - Solution: Update theme to use new class names

4. **Browser cache** - Old JavaScript may be cached
   - Solution: Hard refresh (Ctrl+F5)

## Debugging Commands

If issues occur:

```bash
# Check PHP errors
tail -f /path/to/wordpress/wp-content/debug.log

# Check Apache errors
tail -f /var/log/apache2/error.log

# Test .htaccess rules
curl -I https://yoursite.com/es/

# Check file permissions
ls -la /wp-content/plugins/jai-free-translator/
```

## Success Criteria

Plugin is ready for production if:
- ✅ Installs without errors
- ✅ Settings page accessible
- ✅ Language selector appears
- ✅ Translation works
- ✅ No JavaScript console errors
- ✅ No PHP warnings/errors
- ✅ URLs work correctly with .htaccess
- ✅ Backward compatible with old shortcodes

## Next Steps After Testing

1. ✅ Verify all tests pass
2. Create backup of working plugin
3. Deploy to staging environment first
4. Test with real content
5. Deploy to production
6. Monitor for issues
7. Update documentation if needed

---

**Last Updated:** 2025-11-02
**Version:** 1.1.0
**Status:** Ready for WordPress Installation Testing
