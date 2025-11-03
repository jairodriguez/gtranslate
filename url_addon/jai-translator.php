<?php
/**
 * JAI Free Translator
 * Server-Side Translation Handler
 * Serves translated content at /es/page URLs (no redirect)
 */

error_reporting(0);

// Load configuration
require_once 'config.php';
require_once 'translator.php';

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

// Load WordPress
$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
if(!file_exists($wp_load_path)) {
    // Try to find wp-load.php
    $search_path = dirname(__FILE__);
    for($i = 0; $i < 10; $i++) {
        $wp_load_path = $search_path . '/wp-load.php';
        if(file_exists($wp_load_path)) break;
        $search_path = dirname($search_path);
    }
}

if(!file_exists($wp_load_path)) {
    die('WordPress not found');
}

require_once $wp_load_path;

// Get WordPress settings for translation
$settings = get_option('JAI_Translator', array());
$api_provider = isset($settings['translation_api_provider']) ? $settings['translation_api_provider'] : 'deepl';
$api_key = '';

if($api_provider === 'deepl' && isset($settings['deepl_api_key'])) {
    $api_key = $settings['deepl_api_key'];
} elseif($api_provider === 'google' && isset($settings['google_translate_api_key'])) {
    $api_key = $settings['google_translate_api_key'];
}

// Get the original page content
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$original_url = $protocol . '://' . $host . $page_url;

// Fetch original content
$context = stream_context_create(array('http' => array('ignore_errors' => true)));
$original_html = file_get_contents($original_url, false, $context);

if($original_html === false) {
    // Failed to fetch original page - show error
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>Page Not Found</title></head><body><h1>404 - Page Not Found</h1></body></html>';
    exit;
}

// If we have an API key, use server-side translation
if(!empty($api_key)) {
    try {
        $translator = new GT_Translator($main_lang, $glang, $api_provider, $api_key);
        $translated_html = $translator->translate_html($original_html, $gurl);

        // Debug logging
        if($debug && is_writable(dirname(__FILE__))) {
            $debug_info = date('Y-m-d H:i:s') . " - Server-side translation: /{$glang}/{$gurl} (API: {$api_provider})\n";
            file_put_contents(dirname(__FILE__) . '/debug.txt', $debug_info, FILE_APPEND);
        }

        // Output translated HTML
        echo $translated_html;

    } catch(Exception $e) {
        // Translation failed - fall back to original with client-side translation
        if($debug && is_writable(dirname(__FILE__))) {
            $debug_info = date('Y-m-d H:i:s') . " - Translation error: " . $e->getMessage() . "\n";
            file_put_contents(dirname(__FILE__) . '/debug.txt', $debug_info, FILE_APPEND);
        }

        // Inject client-side translation
        $translated_html = str_replace('</head>', '<script>var gt_lang = "' . $glang . '";</script></head>', $original_html);
        echo $translated_html;
    }
} else {
    // No API key - use client-side translation
    if($debug && is_writable(dirname(__FILE__))) {
        $debug_info = date('Y-m-d H:i:s') . " - Client-side fallback: /{$glang}/{$gurl} (no API key)\n";
        file_put_contents(dirname(__FILE__) . '/debug.txt', $debug_info, FILE_APPEND);
    }

    // Inject client-side translation marker
    $translated_html = str_replace('</head>', '<script>var gt_lang = "' . $glang . '";</script></head>', $original_html);
    echo $translated_html;
}

exit;
