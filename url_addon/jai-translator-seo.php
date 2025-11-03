<?php
/**
 * JAI Free Translator - SEO-Friendly Version
 * Serves content at /es/page URL (no redirect)
 * Keeps clean URLs for search engines
 */

error_reporting(0);

include 'config.php';

// Check if we have the required parameters from .htaccess rewrite
if(!isset($_GET['glang']) || !isset($_GET['gurl'])) {
    exit;
}

$glang = strtolower(trim($_GET['glang']));
$gurl = $_GET['gurl'];

// Build the original page URL
$page_url = '/' . ltrim($gurl, '/');

// Validate language code
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

// If requesting the main language, redirect to original URL without language prefix
if($glang === $main_lang) {
    $page_url = preg_replace('/^[\/]+/', '/', $page_url);
    header('Location: ' . $page_url, true, 301);
    exit;
}

// Set cookie for language persistence (Google Translate uses this)
setcookie('googtrans', '/' . $main_lang . '/' . $glang, time() + (86400 * 30), '/');

// Set environment variable so WordPress knows about the language
$_SERVER['HTTP_X_GT_LANG'] = $glang;
$_GET['gt_lang'] = $glang;

// Debug logging
if($debug && is_writable(dirname(__FILE__))) {
    $debug_info = date('Y-m-d H:i:s') . " - SEO Mode: Serving /{$glang}/{$gurl} directly\n";
    file_put_contents(dirname(__FILE__) . '/debug.txt', $debug_info, FILE_APPEND);
}

// Include WordPress to serve the actual page
// This keeps the URL as /es/page/ while serving content
define('WP_USE_THEMES', true);

// Get WordPress path
$wp_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

if(file_exists($wp_path)) {
    // Load WordPress
    require_once($wp_path);
} else {
    // Fallback: Try to find WordPress
    $levels = 0;
    $search_path = dirname(__FILE__);

    while($levels < 10) {
        $wp_load = $search_path . '/wp-load.php';
        if(file_exists($wp_load)) {
            require_once($wp_load);
            break;
        }
        $search_path = dirname($search_path);
        $levels++;
    }
}
