=== JAI Free Translator ===
Contributors: JAI
Author: JAI
Tags: translate, translator, multilingual, translation, language
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Author URI: https://withjai.com

Free multilingual translation for your website with sub-directory URL support for Google Ads landing pages.

== Description ==

JAI Free Translator uses Google Translate's free translation service to make your WordPress site **multilingual**. With 103+ available languages, your site will be accessible to more than 99% of internet users.

This plugin is optimized for **Google Ads landing pages** with SEO-friendly sub-directory URLs (e.g., example.com/es/landing-page).

**Key Features:**
* 100% free - no API costs or hidden fees
* Clean sub-directory URLs perfect for Google Ads
* Client-side translation using Google's free widget
* Easy to implement and use

**Features**

* Free Google automatic machine translation
* Hides Google top frame after translation
* Translate website on the fly
* Translate posts and pages
* Translate categories and tags
* Menus and widgets translation
* Themes and plugins translation
* Right to left language support
* Google language translator widget
* Auto-switch language based on browser defined language
* Available styles: Float, Dropdown, Flags, Flags with dropdown, Nice dropdown with flags, Flags with language names, Flags with language codes, Language names, Language codes, Globe, Popup
* Floating language selector
* WooCommerce shop translation
* Multilingual language names in native alphabet
* Alternative flags for Quebec, Canada, USA, Brazil, Mexico, Argentina, Colombia
* Lazy loading for language flags and js libraries to boost performance
* Lightweight vanilla javascript without dependencies

**How It Works**

1. **Clean URLs** - Visitor goes to: yoursite.com/es/landing-page
2. **Smart Redirect** - Plugin redirects to: yoursite.com/landing-page?gt_lang=es
3. **Auto-Translation** - JavaScript loads Google Translate widget and translates the page

This hybrid approach gives you clean, SEO-friendly URLs while using Google's free translation service.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/jai-free-translator` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Settings → JAI Translator to configure the plugin
4. Select your default language and target languages
5. Choose your preferred widget style

**Ways to Display the Language Selector:**

**1. Floating Language Selector**
Enable from Settings → JAI Translator and choose position (Top Right, Bottom Right, Top Left or Bottom Left).

**2. In Navigation Menu**
Select which menu should display the language selector from the settings page.

**3. Shortcode**
Use `[jaitranslator]` shortcode anywhere on your site (posts, pages, text widgets).
Or use in template files: `<?php echo do_shortcode('[jaitranslator]'); ?>`

**4. Widget**
Use Appearance → Widgets screen to add JAI Translator widget to your sidebar.

**5. Single Language Link**
Create custom language links using: `[gt-link lang="es" label="Español" widget_look="flags_name"]`

**For Google Ads Landing Pages:**
Use clean URLs like: yoursite.com/es/landing-page, yoursite.com/fr/landing-page, etc.

== Frequently Asked Questions ==

= It doesn't translate, what to do? =
Make sure you've enabled sub-directory URL structure in the plugin settings and that your .htaccess file has been properly configured. The translation happens client-side so it may take 1-2 seconds to load.

= What is JAI Free Translator? =
JAI Free Translator is a WordPress plugin that makes your website multilingual using Google's free translation service. It's specifically optimized for Google Ads landing pages with clean sub-directory URLs.

* Multilingual solution makes your website available to the world
* One-click translation helps visitors read your site in their native language
* Free automatic translation with zero API costs
* Customizable language switcher with multiple styles
* Clean URLs perfect for Google Ads campaigns

= Is it FREE? =
Yes! JAI Free Translator is 100% free with no hidden costs. Unlike other translation plugins that charge for API usage, this plugin uses Google's free translation widget - completely free with unlimited translations.

= What is the quality of translation? =
JAI Free Translator uses Google Translate's automatic machine translation. The quality is generally good for most language pairs, though it may not be perfect for all contexts. For best results, keep your content clear and simple.

= Are the translations provided free of charge? =
Yes! All translations are completely free. The plugin uses Google Translate's free widget which provides unlimited automatic translations at no cost.

= Can I modify the translations? =
The translations are generated automatically by Google Translate. If you need custom or professional translations, you may want to consider a different solution designed for manual translation editing.

= Which languages are supported? =
Here is the list: Afrikaans, Albanian, Amharic, Arabic, Armenian, Azerbaijani, Basque, Belarusian, Bengali, Bosnian, Bulgarian, Catalan, Cebuano, Chichewa, Chinese (Simplified), Chinese (Traditional), Corsican, Croatian, Czech, Danish, Dutch, English, Esperanto, Estonian, Filipino, Finnish, French, Frisian, Galician, Georgian, German, Greek, Gujarati, Haitian Creole, Hausa, Hawaiian, Hebrew, Hindi, Hmong, Hungarian, Icelandic, Igbo, Indonesian, Irish, Italian, Japanese, Javanese, Kannada, Kazakh, Khmer, Korean, Kurdish (Kurmanji), Kyrgyz, Lao, Latin, Latvian, Lithuanian, Luxembourgish, Macedonian, Malagasy, Malay, Malayalam, Maltese, Maori, Marathi, Mongolian, Myanmar (Burmese), Nepali, Norwegian, Pashto, Persian, Polish, Portuguese, Punjabi, Romanian, Russian, Samoan, Scottish Gaelic, Serbian, Sesotho, Shona, Sindhi, Sinhala, Slovak, Slovenian, Somali, Spanish, Sundanese, Swahili, Swedish, Tajik, Tamil, Telugu, Thai, Turkish, Ukrainian, Urdu, Uzbek, Vietnamese, Welsh, Xhosa, Yiddish, Yoruba, Zulu

= Is it SEO compatible? =
JAI Free Translator uses sub-directory URLs (example.com/es/page) which are SEO-friendly and perfect for Google Ads landing pages. However, since the actual translation happens client-side with JavaScript, search engines will see the original untranslated content.

For Google Ads campaigns, the clean URL structure is beneficial as it allows you to create language-specific campaign URLs.

= How does it work for Google Ads? =
The plugin provides clean sub-directory URLs perfect for Google Ads campaigns:
* Create landing pages with URLs like: yoursite.com/es/landing-page
* Use these URLs in your Google Ads campaigns
* Visitors see translated content automatically
* Language selection persists via cookies

= Can I exclude some parts from being translated? =
Yes, wrap any text you don't want translated with &lt;span class=&quot;notranslate&quot;&gt;&lt;/span&gt;. Google Translate will skip content inside elements with the "notranslate" class.

= Which plugins are supported? =
The plugin translates all visible content on the page, so it works with most WordPress plugins including WooCommerce, Yoast SEO, contact forms, and more. Since translation happens client-side using Google Translate's widget, any HTML content can be translated.

= What are the server requirements? =
Standard WordPress hosting requirements. The plugin works with any WordPress installation (version 5.0 or higher recommended). No special server configuration needed.

= Do I need to pay for Google Translate API? =
No! The plugin uses Google's free translation widget. There are zero API costs and no usage limits. Unlimited translations completely free.

== Screenshots ==
1. Float widget style
2. Nice dropdown with flags (closed)
3. Nice dropdown with flags (open)
4. Globe with language flags
5. Flags with language names
6. Flags with language codes
7. Settings page

== Changelog ==
= 1.1.0 =
* Custom JAI Free Translator build
* Removed all paid/pro features
* Optimized for Google Ads landing pages with sub-directory URLs
* Free client-side translation with zero API costs
* Simplified configuration and setup
* Based on GTranslate 3.0.8
