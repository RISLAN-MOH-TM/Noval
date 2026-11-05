<?php
/**
 * URL Helper Functions
 * Provides clean URL generation for the application
 */

class UrlHelper {
    
    /**
     * Get base path (handles both local and live environments)
     * @return string
     */
    private static function getBasePath() {
        // Detect if running in a subfolder (local environment)
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));
        
        // If in root directory, return empty string
        if ($basePath === '/' || $basePath === '.') {
            return '';
        }
        
        return $basePath;
    }
    
    /**
     * Generate clean product URL
     * @param int $productId
     * @return string
     */
    public static function product($productId) {
        return self::getBasePath() . "/product/" . intval($productId);
    }
    
    /**
     * Generate clean category URL
     * @param int $categoryId
     * @return string
     */
    public static function category($categoryId) {
        return self::getBasePath() . "/products/category/" . intval($categoryId);
    }
    
    /**
     * Generate clean search URL
     * @param string $searchTerm
     * @return string
     */
    public static function search($searchTerm) {
        return self::getBasePath() . "/products/search/" . urlencode($searchTerm);
    }
    
    /**
     * Generate clean page URL (removes .php extension)
     * @param string $page
     * @return string
     */
    public static function page($page) {
        return self::getBasePath() . "/" . str_replace('.php', '', $page);
    }
    
    /**
     * Get base URL
     * @return string
     */
    public static function base() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'];
    }
    
    /**
     * Get current URL
     * @return string
     */
    public static function current() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
