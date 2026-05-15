<?php
/**
 * JAI Free Translator - Server-Side Translation Engine
 *
 * Handles HTML translation via DeepL API or Google Cloud Translation API
 * with intelligent caching and link rewriting for SEO-friendly multilingual sites.
 *
 * @package JAI_Free_Translator
 * @version 1.0.0
 */

class GT_Translator {

    private $source_lang;
    private $target_lang;
    private $api_provider; // 'deepl' or 'google'
    private $api_key;
    private $cache_dir;
    private $cache_ttl = 2592000; // 30 days in seconds

    /**
     * Constructor
     *
     * @param string $source_lang Source language code (e.g., 'en')
     * @param string $target_lang Target language code (e.g., 'es')
     * @param string $api_provider API provider ('deepl' or 'google')
     * @param string $api_key API key for the selected provider
     */
    public function __construct($source_lang, $target_lang, $api_provider, $api_key) {
        $this->source_lang = $source_lang;
        $this->target_lang = $target_lang;
        $this->api_provider = $api_provider;
        $this->api_key = $api_key;

        // Set up cache directory
        $wp_content_dir = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR : dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-content';
        $this->cache_dir = $wp_content_dir . '/cache/gtranslate';

        // Create cache directories if they don't exist
        $this->ensure_cache_directories();
    }

    /**
     * Translate HTML content
     *
     * @param string $html HTML content to translate
     * @param string $page_id Unique identifier for caching (e.g., URL path)
     * @return string Translated HTML with rewritten links
     */
    public function translate_html($html, $page_id) {
        // Check cache first
        $cached = $this->get_cached_translation($page_id);
        if ($cached !== false) {
            return $cached;
        }

        // Parse HTML and extract translatable text
        $dom = $this->parse_html($html);
        $texts = $this->extract_text_nodes($dom);

        error_log('Translation debug - Extracted ' . count($texts) . ' text nodes');
        if (!empty($texts)) {
            error_log('First 3 texts: ' . implode(' | ', array_slice($texts, 0, 3)));
        }

        if (empty($texts)) {
            error_log('Translation debug - No texts found, HTML length: ' . strlen($html));
            return $html; // No text to translate
        }

        // Translate all text nodes
        $translations = $this->translate_texts($texts);

        if ($translations === false) {
            return false; // Translation API error
        }

        // Replace text in DOM
        $this->replace_text_nodes($dom, $translations);

        // Rewrite internal links to maintain language
        $this->rewrite_links($dom);

        // Get translated HTML
        $translated_html = $this->serialize_html($dom);

        // Cache the result
        $this->cache_translation($page_id, $translated_html);

        return $translated_html;
    }

    /**
     * Parse HTML into DOMDocument
     */
    private function parse_html($html) {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);

        // Add meta charset to ensure proper UTF-8 handling
        $html_with_charset = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';

        // Load HTML with UTF-8 encoding
        $dom->loadHTML(mb_convert_encoding($html_with_charset, 'HTML-ENTITIES', 'UTF-8'));

        libxml_clear_errors();

        return $dom;
    }

    /**
     * Extract text nodes from DOM
     *
     * @return array Array of text strings to translate
     */
    private function extract_text_nodes($dom) {
        $texts = array();
        $xpath = new DOMXPath($dom);

        // Skip these tags (scripts, styles, etc.)
        $skip_tags = array('script', 'style', 'code', 'pre', 'noscript');

        // Get all text nodes
        $text_nodes = $xpath->query('//text()[normalize-space()]');

        foreach ($text_nodes as $node) {
            // Skip if parent is in skip list
            $parent_name = strtolower($node->parentNode->nodeName);
            if (in_array($parent_name, $skip_tags)) {
                continue;
            }

            $text = trim($node->nodeValue);
            if (!empty($text)) {
                $texts[] = $text;
            }
        }

        return $texts;
    }

    /**
     * Translate array of texts using selected API
     *
     * @param array $texts Array of text strings to translate
     * @return array|false Array of translated texts or false on error
     */
    private function translate_texts($texts) {
        if ($this->api_provider === 'deepl') {
            return $this->translate_with_deepl($texts);
        } elseif ($this->api_provider === 'google') {
            return $this->translate_with_google($texts);
        }

        return false;
    }

    /**
     * Translate using DeepL API
     */
    private function translate_with_deepl($texts) {
        $url = 'https://api-free.deepl.com/v2/translate';

        $target_lang = $this->map_deepl_language($this->target_lang);

        $data = array(
            'text' => $texts,
            'source_lang' => strtoupper($this->source_lang),
            'target_lang' => strtoupper($target_lang),
            'tag_handling' => 'html'
        );

        $headers = array(
            'Authorization: DeepL-Auth-Key ' . $this->api_key,
            'Content-Type: application/json'
        );

        $response = $this->make_api_request_json($url, $data, $headers);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (!isset($result['translations'])) {
            error_log('DeepL API error: ' . print_r($result, true));
            return false;
        }

        $translations = array();
        foreach ($result['translations'] as $translation) {
            $translations[] = $translation['text'];
        }

        return $translations;
    }

    /**
     * Translate using Google Cloud Translation API
     */
    private function translate_with_google($texts) {
        $url = 'https://translation.googleapis.com/language/translate/v2?key=' . urlencode($this->api_key);

        $data = array(
            'q' => $texts,
            'source' => $this->source_lang,
            'target' => $this->target_lang,
            'format' => 'html'
        );

        $response = $this->make_api_request($url, $data, true);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (!isset($result['data']['translations'])) {
            error_log('Google Translate API error: ' . print_r($result, true));
            return false;
        }

        $translations = array();
        foreach ($result['data']['translations'] as $translation) {
            $translations[] = $translation['translatedText'];
        }

        return $translations;
    }

    /**
     * Map language codes to DeepL format
     */
    private function map_deepl_language($lang) {
        $mapping = array(
            'zh-cn' => 'ZH',
            'zh-tw' => 'ZH',
            'en' => 'EN-US',
            'pt' => 'PT-PT'
        );

        $lang_lower = strtolower($lang);
        return isset($mapping[$lang_lower]) ? $mapping[$lang_lower] : $lang;
    }

    /**
     * Make API request
     */
    private function make_api_request($url, $data, $use_post_json = false) {
        $ch = curl_init();

        if ($use_post_json) {
            // Google API uses JSON
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
        } else {
            // DeepL API uses form data
            // Special handling for arrays: DeepL expects text=A&text=B not text[0]=A&text[1]=B
            $post_fields = '';
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $post_fields .= urlencode($key) . '=' . urlencode($item) . '&';
                    }
                } else {
                    $post_fields .= urlencode($key) . '=' . urlencode($value) . '&';
                }
            }
            $post_fields = rtrim($post_fields, '&');

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log('Translation API cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if ($http_code !== 200) {
            error_log('Translation API HTTP error: ' . $http_code . ' - ' . $response);
            return false;
        }

        return $response;
    }

    private function make_api_request_json($url, $data, $headers = array()) {
        $ch = curl_init();

        $post_fields = json_encode($data);
        if ($post_fields === false) {
            error_log('DeepL API JSON encode error');
            return false;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log('DeepL API cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if ($http_code !== 200) {
            error_log('DeepL API HTTP error: ' . $http_code . ' - ' . $response);
            return false;
        }

        return $response;
    }

    /**
     * Replace text nodes in DOM with translations
     */
    private function replace_text_nodes($dom, $translations) {
        $xpath = new DOMXPath($dom);
        $skip_tags = array('script', 'style', 'code', 'pre', 'noscript');
        $text_nodes = $xpath->query('//text()[normalize-space()]');

        $index = 0;
        foreach ($text_nodes as $node) {
            $parent_name = strtolower($node->parentNode->nodeName);
            if (in_array($parent_name, $skip_tags)) {
                continue;
            }

            $text = trim($node->nodeValue);
            if (!empty($text) && isset($translations[$index])) {
                $node->nodeValue = $translations[$index];
                $index++;
            }
        }
    }

    /**
     * Rewrite internal links to maintain language
     */
    private function rewrite_links($dom) {
        $xpath = new DOMXPath($dom);

        // Rewrite <a href> links
        $links = $xpath->query('//a[@href]');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $new_href = $this->rewrite_url($href);
            if ($new_href !== $href) {
                $link->setAttribute('href', $new_href);
            }
        }

        // Rewrite form actions
        $forms = $xpath->query('//form[@action]');
        foreach ($forms as $form) {
            $action = $form->getAttribute('action');
            $new_action = $this->rewrite_url($action);
            if ($new_action !== $action) {
                $form->setAttribute('action', $new_action);
            }
        }

        // Rewrite canonical links
        $canonicals = $xpath->query('//link[@rel="canonical"][@href]');
        foreach ($canonicals as $canonical) {
            $href = $canonical->getAttribute('href');
            $new_href = $this->rewrite_url($href);
            if ($new_href !== $href) {
                $canonical->setAttribute('href', $new_href);
            }
        }
    }

    /**
     * Rewrite a single URL to include language prefix
     */
    private function rewrite_url($url) {
        // Skip external links, anchors, and special URLs
        if (empty($url) ||
            $url[0] === '#' ||
            strpos($url, '://') !== false ||
            strpos($url, 'mailto:') === 0 ||
            strpos($url, 'tel:') === 0 ||
            strpos($url, 'javascript:') === 0) {
            return $url;
        }

        // Parse URL
        $parsed = parse_url($url);
        $path = isset($parsed['path']) ? $parsed['path'] : '/';

        // Check if already has language prefix
        if (preg_match('#^/[a-z]{2}(-[A-Z]{2})?/#', $path)) {
            return $url; // Already has language prefix
        }

        // Add language prefix
        $new_path = '/' . $this->target_lang . $path;

        // Rebuild URL
        $new_url = $new_path;
        if (isset($parsed['query'])) {
            $new_url .= '?' . $parsed['query'];
        }
        if (isset($parsed['fragment'])) {
            $new_url .= '#' . $parsed['fragment'];
        }

        return $new_url;
    }

    /**
     * Serialize DOM back to HTML
     */
    private function serialize_html($dom) {
        // Since we wrapped content in html/body tags during parsing,
        // we need to extract just the body innerHTML
        $body = $dom->getElementsByTagName('body')->item(0);

        if (!$body) {
            return $dom->saveHTML();
        }

        $html = '';
        foreach ($body->childNodes as $node) {
            $html .= $dom->saveHTML($node);
        }

        return $html;
    }

    /**
     * Get cached translation
     */
    private function get_cached_translation($page_id) {
        $cache_file = $this->get_cache_file_path($page_id);

        if (!file_exists($cache_file)) {
            return false;
        }

        // Check if cache is expired
        $file_time = filemtime($cache_file);
        if (time() - $file_time > $this->cache_ttl) {
            unlink($cache_file); // Delete expired cache
            return false;
        }

        return file_get_contents($cache_file);
    }

    /**
     * Cache translation
     */
    private function cache_translation($page_id, $html) {
        $cache_file = $this->get_cache_file_path($page_id);
        $cache_dir = dirname($cache_file);

        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }

        file_put_contents($cache_file, $html);
    }

    /**
     * Get cache file path
     */
    private function get_cache_file_path($page_id) {
        $safe_id = md5($page_id);
        return $this->cache_dir . '/translations/' . $safe_id . '_' . $this->target_lang . '.html';
    }

    /**
     * Ensure cache directories exist
     */
    private function ensure_cache_directories() {
        $dirs = array(
            $this->cache_dir,
            $this->cache_dir . '/translations',
            $this->cache_dir . '/queue',
            $this->cache_dir . '/stats'
        );

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }

        // Create .htaccess to protect cache directory
        $htaccess_file = $this->cache_dir . '/.htaccess';
        if (!file_exists($htaccess_file)) {
            file_put_contents($htaccess_file, "Deny from all\n");
        }
    }

    /**
     * Clear cache for a specific page
     */
    public function clear_cache($page_id) {
        $cache_file = $this->get_cache_file_path($page_id);
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }

    /**
     * Clear all cache
     */
    public static function clear_all_cache() {
        $wp_content_dir = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR : dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-content';
        $cache_dir = $wp_content_dir . '/cache/gtranslate/translations';

        if (is_dir($cache_dir)) {
            $files = glob($cache_dir . '/*.html');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
