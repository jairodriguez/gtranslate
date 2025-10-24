<?php
/**
 * GTranslate Free - Custom
 * Sub-directory URL handler for Google Ads landing pages
 *
 * This file handles requests to /es/page, /fr/page, etc. and redirects to the original
 * page with language information that the JavaScript widget will use for translation.
 *
 * Uses FREE Google Translate Widget - no API costs, no external servers needed.
 */

error_reporting(0);

include 'config.php';

// Check if we have the required parameters
if(!isset($_GET['glang']) || !isset($_GET['gurl'])) {
    exit;
}

$glang = strtolower(trim($_GET['glang']));
$gurl = $_GET['gurl'];

// Build the original page URL
$page_url = '/' . ltrim($gurl, '/');

// Properly encode URL segments
$page_url_segments = explode('/', $page_url);
foreach($page_url_segments as $i => $segment) {
    if($segment !== '') {
        $page_url_segments[$i] = rawurlencode(rawurldecode($segment));
    }
}
$page_url = implode('/', $page_url_segments);

// Preserve GET parameters (except glang and gurl)
$get_params = $_GET;
unset($get_params['glang']);
unset($get_params['gurl']);

if(count($get_params)) {
    $page_url .= '?' . http_build_query($get_params, '', '&', PHP_QUERY_RFC3986);
}

// Validate language code (103 supported languages)
$valid_languages = array(
    'af', 'sq', 'am', 'ar', 'hy', 'az', 'eu', 'be', 'bn', 'bs', 'bg', 'ca', 'ceb', 'ny',
    'zh-cn', 'zh-tw', 'co', 'hr', 'cs', 'da', 'nl', 'en', 'eo', 'et', 'tl', 'fi', 'fr',
    'fy', 'gl', 'ka', 'de', 'el', 'gu', 'ht', 'ha', 'haw', 'iw', 'hi', 'hmn', 'hu', 'is',
    'ig', 'id', 'ga', 'it', 'ja', 'jw', 'kn', 'kk', 'km', 'ko', 'ku', 'ky', 'lo', 'la',
    'lv', 'lt', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mn', 'my', 'ne', 'no',
    'ps', 'fa', 'pl', 'pt', 'pa', 'ro', 'ru', 'sm', 'gd', 'sr', 'st', 'sn', 'sd', 'si',
    'sk', 'sl', 'so', 'es', 'su', 'sw', 'sv', 'tg', 'ta', 'te', 'th', 'tr', 'uk', 'ur',
    'uz', 'vi', 'cy', 'xh', 'yi', 'yo', 'zu'
);

if(!in_array($glang, $valid_languages)) {
    // Invalid language code - redirect to original page
    header('Location: ' . $page_url, true, 302);
    exit;
}

// If requesting the main language, redirect to the original URL without language prefix
if($glang === $main_lang) {
    $page_url = preg_replace('/^[\/]+/', '/', $page_url);
    header('Location: ' . $page_url, true, 301);
    exit;
}

// Load WordPress to access settings
if(file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php')) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
    $gt_settings = get_option('GTranslate');
} else {
    $gt_settings = array();
}

// Check if Translation API is configured
$translation_api_provider = isset($gt_settings['translation_api_provider']) ? $gt_settings['translation_api_provider'] : '';
$translation_api_key = '';

if($translation_api_provider === 'deepl') {
    $translation_api_key = isset($gt_settings['deepl_api_key']) ? $gt_settings['deepl_api_key'] : '';
} elseif($translation_api_provider === 'google') {
    $translation_api_key = isset($gt_settings['google_translate_api_key']) ? $gt_settings['google_translate_api_key'] : '';
}

// SERVER-SIDE TRANSLATION: If API key is configured, translate server-side
if(!empty($translation_api_key)) {
    require_once dirname(__FILE__) . '/translator.php';

    // Build the original page URL
    $protocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
        ? 'https' : 'http';

    $host = $_SERVER['HTTP_HOST'];
    $original_url = $protocol . '://' . $host . $page_url;

    // Fetch original HTML
    $ch = curl_init($original_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local development
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-GT-INTERNAL: 1')); // Marker to prevent infinite loops
    $original_html = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($http_code !== 200 || empty($original_html)) {
        // Failed to fetch original page - fallback to redirect
        if($debug) {
            error_log("Failed to fetch original page: $original_url (HTTP $http_code)");
        }
    } else {
        // Translate HTML
        $translator = new GT_Translator($main_lang, $glang, $translation_api_provider, $translation_api_key);
        $translated_html = $translator->translate_html($original_html, $page_url);

        if($translated_html !== false) {
            // Success! Serve translated HTML
            header('Content-Type: text/html; charset=UTF-8');
            echo $translated_html;
            exit;
        } else {
            // Translation failed - fallback to client-side
            if($debug) {
                error_log("Translation API failed for $page_url");
            }
        }
    }
}

// FALLBACK: CLIENT-SIDE TRANSLATION (when no API key or server-side fails)
// Build the target URL
$protocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
    ? 'https' : 'http';

$host = $_SERVER['HTTP_HOST'];
$target_url = $protocol . '://' . $host . $page_url;

// Add a marker for the JavaScript to detect the target language
if(strpos($page_url, '?') !== false) {
    $target_url .= '&gt_lang=' . $glang;
} else {
    $target_url .= '?gt_lang=' . $glang;
}

// Set a cookie so the language selection persists
setcookie('googtrans', '/' . $main_lang . '/' . $glang, time() + (86400 * 30), '/');

// Debug mode: log the redirect
if($debug && is_writable(dirname(__FILE__))) {
    $debug_info = date('Y-m-d H:i:s') . " - Redirect (client-side): /{$glang}/{$gurl} -> {$target_url}\n";
    file_put_contents(dirname(__FILE__) . '/debug.txt', $debug_info, FILE_APPEND);
}

// Redirect to the original page with language marker
// The JavaScript will automatically translate it using the free Google Translate widget
header('Location: ' . $target_url, true, 302);
exit;
