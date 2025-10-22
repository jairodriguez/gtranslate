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

// For translated pages: serve the original page and let JavaScript handle translation
// The JavaScript (base.js) will:
// 1. Detect the language from the URL path
// 2. Load the Google Translate widget
// 3. Automatically translate the page using the free Google Translate service

// Build the target URL
$protocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
    ? 'https' : 'http';

$host = $_SERVER['HTTP_HOST'];
$target_url = $protocol . '://' . $host . $page_url;

// Add a marker for the JavaScript to detect the target language
// This will be read by base.js to automatically trigger translation
if(strpos($page_url, '?') !== false) {
    $target_url .= '&gt_lang=' . $glang;
} else {
    $target_url .= '?gt_lang=' . $glang;
}

// Set a cookie so the language selection persists
// Format: /auto/language_code (same as Google Translate widget expects)
setcookie('googtrans', '/' . $main_lang . '/' . $glang, time() + (86400 * 30), '/');

// Debug mode: log the redirect
if($debug && is_writable(dirname(__FILE__))) {
    $debug_info = date('Y-m-d H:i:s') . " - Redirect: /{$glang}/{$gurl} -> {$target_url}\n";
    file_put_contents(dirname(__FILE__) . '/debug.txt', $debug_info, FILE_APPEND);
}

// Redirect to the original page with language marker
// The JavaScript will automatically translate it using the free Google Translate widget
header('Location: ' . $target_url, true, 302);
exit;
