<?php
/**
 * Plugin Name: GreenShift Business AI Generator
 * Plugin URI: https://greenshiftwp.com/business-ai-generator
 * Description: Complete AI-powered business website generator with GreenShift integration. Generate professional business pages from simple input data.
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
define('GSBA_DB_VERSION', '1.0');

// Main plugin class
class GreenShift_Business_AI_Generator {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_gsba_generate_business_page', array($this, 'ajax_generate_business_page'));
        add_action('wp_ajax_gsba_save_business_profile', array($this, 'ajax_save_business_profile'));
        add_action('wp_ajax_gsba_get_templates', array($this, 'ajax_get_templates'));
        add_action('wp_ajax_gsba_delete_generated_page', array($this, 'ajax_delete_generated_page'));
        
        // Admin only AJAX
        add_action('wp_ajax_gsba_save_template', array($this, 'ajax_save_template'));
        add_action('wp_ajax_gsba_delete_template', array($this, 'ajax_delete_template'));
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Include required files
        $this->include_files();
    }
    
    public function include_files() {
        require_once GSBA_PLUGIN_DIR . 'includes/class-database.php';
        require_once GSBA_PLUGIN_DIR . 'includes/class-ai-generator.php';
        require_once GSBA_PLUGIN_DIR . 'includes/class-template-manager.php';
        require_once GSBA_PLUGIN_DIR . 'includes/class-page-generator.php';
        require_once GSBA_PLUGIN_DIR . 'includes/class-seo-optimizer.php';
    }
    
    public function init() {
        load_plugin_textdomain('greenshift-business-ai', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize database
        $db = new GSBA_Database();
        $db->init();
        
        // Add dashboard to admin bar for all users
        add_action('admin_bar_menu', array($this, 'add_admin_bar_menu'), 100);
    }
    
    public function add_admin_menu() {
        // Main dashboard (accessible to all users)
        add_menu_page(
            __('Business AI Generator', 'greenshift-business-ai'),
            __('AI Business Gen', 'greenshift-business-ai'),
            'read',
            'gsba-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-admin-site-alt3',
            30
        );
        
        // Template management (admin only)
        add_submenu_page(
            'gsba-dashboard',
            __('Template Management', 'greenshift-business-ai'),
            __('Templates', 'greenshift-business-ai'),
            'manage_options',
            'gsba-templates',
            array($this, 'templates_page')
        );
        
        // Settings (admin only)
        add_submenu_page(
            'gsba-dashboard',
            __('AI Settings', 'greenshift-business-ai'),
            __('Settings', 'greenshift-business-ai'),
            'manage_options',
            'gsba-settings',
            array($this, 'settings_page')
        );
    }
    
    public function add_admin_bar_menu($wp_admin_bar) {
        if (current_user_can('read')) {
            $wp_admin_bar->add_menu(array(
                'id' => 'gsba-dashboard',
                'title' => __('🤖 AI Generator', 'greenshift-business-ai'),
                'href' => admin_url('admin.php?page=gsba-dashboard'),
                'meta' => array(
                    'title' => __('Business AI Generator Dashboard', 'greenshift-business-ai')
                )
            ));
        }
    }
    
    public function dashboard_page() {
        include GSBA_PLUGIN_DIR . 'includes/pages/dashboard.php';
    }
    
    public function templates_page() {
        include GSBA_PLUGIN_DIR . 'includes/pages/templates.php';
    }
    
    public function settings_page() {
        include GSBA_PLUGIN_DIR . 'includes/pages/settings.php';
    }
    
    public function enqueue_frontend_scripts() {
        wp_enqueue_style('gsba-frontend', GSBA_PLUGIN_URL . 'assets/css/frontend.css', array(), GSBA_VERSION);
        wp_enqueue_script('gsba-frontend', GSBA_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), GSBA_VERSION, true);
        
        wp_localize_script('gsba-frontend', 'gsba_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gsba_nonce'),
            'loading_text' => __('Generating your business page...', 'greenshift-business-ai'),
            'success_text' => __('Page generated successfully!', 'greenshift-business-ai'),
            'error_text' => __('Error generating page. Please try again.', 'greenshift-business-ai')
        ));
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'gsba') !== false) {
            wp_enqueue_style('gsba-admin', GSBA_PLUGIN_URL . 'assets/css/admin.css', array(), GSBA_VERSION);
            wp_enqueue_script('gsba-admin', GSBA_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), GSBA_VERSION, true);
            
            // Enqueue media uploader
            wp_enqueue_media();
            
            wp_localize_script('gsba-admin', 'gsba_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gsba_admin_nonce'),
                'messages' => array(
                    'generating' => __('Generating page...', 'greenshift-business-ai'),
                    'success' => __('Page generated successfully!', 'greenshift-business-ai'),
                    'error' => __('Error occurred. Please try again.', 'greenshift-business-ai'),
                    'confirm_delete' => __('Are you sure you want to delete this?', 'greenshift-business-ai')
                )
            ));
        }
    }
    
    // AJAX Handlers
    public function ajax_generate_business_page() {
        check_ajax_referer('gsba_admin_nonce', 'nonce');
        
        if (!current_user_can('read')) {
            wp_send_json_error(__('Permission denied.', 'greenshift-business-ai'));
        }
        
        $business_data = array(
            'business_name' => sanitize_text_field($_POST['business_name']),
            'business_type' => sanitize_text_field($_POST['business_type']),
            'description' => sanitize_textarea_field($_POST['description']),
            'logo_url' => esc_url_raw($_POST['logo_url']),
            'phone' => sanitize_text_field($_POST['phone']),
            'email' => sanitize_email($_POST['email']),
            'address' => sanitize_textarea_field($_POST['address']),
            'website_url' => esc_url_raw($_POST['website_url']),
            'primary_color' => sanitize_hex_color($_POST['primary_color']),
            'secondary_color' => sanitize_hex_color($_POST['secondary_color']),
            'tagline' => sanitize_text_field($_POST['tagline']),
            'social_media' => array(
                'facebook' => esc_url_raw($_POST['social_facebook']),
                'instagram' => esc_url_raw($_POST['social_instagram']),
                'whatsapp' => sanitize_text_field($_POST['social_whatsapp']),
                'linkedin' => esc_url_raw($_POST['social_linkedin'])
            )
        );
        
        $template_id = intval($_POST['template_id']);
        
        try {
            // Save business profile
            $db = new GSBA_Database();
            $profile_id = $db->save_business_profile(get_current_user_id(), $business_data);
            
            // Generate AI content
            $ai_generator = new GSBA_AI_Generator();
            $ai_content = $ai_generator->generate_business_content($business_data, $template_id);
            
            if (!$ai_content['success']) {
                wp_send_json_error($ai_content['message']);
            }
            
            // Generate page with GreenShift blocks
            $page_generator = new GSBA_Page_Generator();
            $page_result = $page_generator->create_business_page($business_data, $ai_content['data'], $template_id);
            
            if (!$page_result['success']) {
                wp_send_json_error($page_result['message']);
            }
            
            // Apply SEO optimization
            $seo_optimizer = new GSBA_SEO_Optimizer();
            $seo_optimizer->optimize_page($page_result['page_id'], $business_data, $ai_content['data']);
            
            // Save generation record
            $db->save_generated_page(get_current_user_id(), $profile_id, $template_id, $page_result['page_id'], $ai_content['data']);
            
            wp_send_json_success(array(
                'page_id' => $page_result['page_id'],
                'page_url' => get_permalink($page_result['page_id']),
                'edit_url' => get_edit_post_link($page_result['page_id'], ''),
                'message' => __('Business page generated successfully!', 'greenshift-business-ai')
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(__('Error: ', 'greenshift-business-ai') . $e->getMessage());
        }
    }
    
    public function ajax_save_business_profile() {
        check_ajax_referer('gsba_admin_nonce', 'nonce');
        
        if (!current_user_can('read')) {
            wp_send_json_error(__('Permission denied.', 'greenshift-business-ai'));
        }
        
        // Implementation for saving business profile
        wp_send_json_success(__('Business profile saved!', 'greenshift-business-ai'));
    }
    
    public function ajax_get_templates() {
        check_ajax_referer('gsba_admin_nonce', 'nonce');
        
        $template_manager = new GSBA_Template_Manager();
        $templates = $template_manager->get_all_templates();
        
        wp_send_json_success($templates);
    }
    
    public function ajax_delete_generated_page() {
        check_ajax_referer('gsba_admin_nonce', 'nonce');
        
        if (!current_user_can('delete_posts')) {
            wp_send_json_error(__('Permission denied.', 'greenshift-business-ai'));
        }
        
        $page_id = intval($_POST['page_id']);
        
        if (wp_delete_post($page_id, true)) {
            wp_send_json_success(__('Page deleted successfully.', 'greenshift-business-ai'));
        } else {
            wp_send_json_error(__('Failed to delete page.', 'greenshift-business-ai'));
        }
    }
    
    public function ajax_save_template() {
        check_ajax_referer('gsba_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'greenshift-business-ai'));
        }
        
        $template_manager = new GSBA_Template_Manager();
        $result = $template_manager->save_template($_POST);
        
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    public function ajax_delete_template() {
        check_ajax_referer('gsba_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'greenshift-business-ai'));
        }
        
        $template_id = intval($_POST['template_id']);
        $template_manager = new GSBA_Template_Manager();
        
        if ($template_manager->delete_template($template_id)) {
            wp_send_json_success(__('Template deleted successfully.', 'greenshift-business-ai'));
        } else {
            wp_send_json_error(__('Failed to delete template.', 'greenshift-business-ai'));
        }
    }
    
    public function activate() {
        // Create database tables
        $db = new GSBA_Database();
        $db->create_tables();
        
        // Install default templates
        $template_manager = new GSBA_Template_Manager();
        $template_manager->install_default_templates();
        
        // Set default options
        $default_options = array(
            'openai_api_key' => '',
            'claude_api_key' => '',
            'gemini_api_key' => '',
            'default_ai_model' => 'openai',
            'max_tokens' => 2000,
            'temperature' => 0.7,
            'default_primary_color' => '#667eea',
            'default_secondary_color' => '#764ba2',
            'page_status' => 'draft'
        );
        
        add_option('gsba_settings', $default_options);
        add_option('gsba_db_version', GSBA_DB_VERSION);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Clean up if needed
        flush_rewrite_rules();
    }
}

// Initialize the plugin
function gsba_init() {
    return GreenShift_Business_AI_Generator::get_instance();
}

// Start the plugin
gsba_init();