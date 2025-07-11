<?php
/**
 * Plugin Name: GreenShift Business AI Generator
 * Plugin URI: https://greenshiftwp.com/business-ai-generator
 * Description: AI-powered business content generator for GreenShift. Input business name, type, and description to generate professional business content using various AI models.
 * Version: 1.0.0
 * Author: WPSoul
 * Author URI: https://wpsoul.com
 * License: GPL2+
 * Text Domain: greenshift-business-ai
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GSBA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GSBA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GSBA_PLUGIN_FILE', __FILE__);
define('GSBA_VERSION', '1.0.0');

// Main plugin class
class GreenShift_Business_AI_Generator {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_ajax_gsba_generate_business_content', array($this, 'ajax_generate_business_content'));
        add_action('wp_ajax_nopriv_gsba_generate_business_content', array($this, 'ajax_generate_business_content'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Hook into GreenShift if available
        add_action('plugins_loaded', array($this, 'greenshift_integration'));
        
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('greenshift-business-ai', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Register shortcode
        add_shortcode('gsba_business_generator', array($this, 'business_generator_shortcode'));
        
        // Add Gutenberg block if Gutenberg is active
        if (function_exists('register_block_type')) {
            add_action('init', array($this, 'register_gutenberg_block'));
        }
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'gsba-frontend',
            GSBA_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            GSBA_VERSION,
            true
        );
        
        wp_enqueue_style(
            'gsba-frontend',
            GSBA_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            GSBA_VERSION
        );
        
        // Localize script for AJAX
        wp_localize_script('gsba-frontend', 'gsba_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gsba_nonce'),
            'loading_text' => __('Generating content...', 'greenshift-business-ai'),
            'error_text' => __('Error generating content. Please try again.', 'greenshift-business-ai')
        ));
    }
    
    /**
     * Enqueue admin scripts
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'gsba') !== false) {
            wp_enqueue_script(
                'gsba-admin',
                GSBA_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                GSBA_VERSION,
                true
            );
            
            wp_enqueue_style(
                'gsba-admin',
                GSBA_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                GSBA_VERSION
            );
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Business AI Generator', 'greenshift-business-ai'),
            __('Business AI Generator', 'greenshift-business-ai'),
            'manage_options',
            'gsba-settings',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        include GSBA_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
    /**
     * GreenShift integration
     */
    public function greenshift_integration() {
        if (function_exists('gspb_get_greenshift_block_styles') || class_exists('greenshiftPlugin')) {
            // Add custom GreenShift block
            add_action('init', array($this, 'register_greenshift_block'));
        }
    }
    
    /**
     * Register GreenShift block
     */
    public function register_greenshift_block() {
        if (function_exists('gspb_get_greenshift_block_styles')) {
            wp_register_script(
                'gsba-greenshift-block',
                GSBA_PLUGIN_URL . 'assets/js/greenshift-block.js',
                array('wp-blocks', 'wp-element', 'wp-editor'),
                GSBA_VERSION,
                true
            );
            
            register_block_type('greenshift-business-ai/generator', array(
                'editor_script' => 'gsba-greenshift-block',
                'render_callback' => array($this, 'render_greenshift_block')
            ));
        }
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_gutenberg_block() {
        wp_register_script(
            'gsba-gutenberg-block',
            GSBA_PLUGIN_URL . 'assets/js/gutenberg-block.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            GSBA_VERSION,
            true
        );
        
        register_block_type('greenshift-business-ai/generator', array(
            'editor_script' => 'gsba-gutenberg-block',
            'render_callback' => array($this, 'render_gutenberg_block')
        ));
    }
    
    /**
     * Render GreenShift block
     */
    public function render_greenshift_block($attributes) {
        return $this->render_business_generator($attributes);
    }
    
    /**
     * Render Gutenberg block
     */
    public function render_gutenberg_block($attributes) {
        return $this->render_business_generator($attributes);
    }
    
    /**
     * Business generator shortcode
     */
    public function business_generator_shortcode($atts) {
        $attributes = shortcode_atts(array(
            'title' => __('Business AI Generator', 'greenshift-business-ai'),
            'show_examples' => 'true',
            'style' => 'default'
        ), $atts);
        
        return $this->render_business_generator($attributes);
    }
    
    /**
     * Render business generator form
     */
    private function render_business_generator($attributes = array()) {
        $defaults = array(
            'title' => __('Business AI Generator', 'greenshift-business-ai'),
            'show_examples' => true,
            'style' => 'default'
        );
        
        $attrs = wp_parse_args($attributes, $defaults);
        
        ob_start();
        include GSBA_PLUGIN_DIR . 'templates/business-generator-form.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX handler for generating business content
     */
    public function ajax_generate_business_content() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'gsba_nonce')) {
            wp_die(__('Security check failed', 'greenshift-business-ai'));
        }
        
        $business_name = sanitize_text_field($_POST['business_name']);
        $business_type = sanitize_text_field($_POST['business_type']);
        $business_description = sanitize_textarea_field($_POST['business_description']);
        $content_type = sanitize_text_field($_POST['content_type']);
        $ai_model = sanitize_text_field($_POST['ai_model']);
        
        // Validate required fields
        if (empty($business_name) || empty($business_type) || empty($business_description)) {
            wp_send_json_error(__('Please fill in all required fields.', 'greenshift-business-ai'));
        }
        
        // Include AI generator class
        require_once GSBA_PLUGIN_DIR . 'includes/class-ai-generator.php';
        
        $ai_generator = new GSBA_AI_Generator();
        $result = $ai_generator->generate_content($business_name, $business_type, $business_description, $content_type, $ai_model);
        
        if ($result['success']) {
            wp_send_json_success($result['data']);
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables or set default options if needed
        $default_options = array(
            'openai_api_key' => '',
            'claude_api_key' => '',
            'gemini_api_key' => '',
            'default_ai_model' => 'openai',
            'max_tokens' => 1000,
            'temperature' => 0.7
        );
        
        add_option('gsba_settings', $default_options);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }
}

// Initialize the plugin
function gsba_init() {
    return GreenShift_Business_AI_Generator::get_instance();
}

// Start the plugin
gsba_init();