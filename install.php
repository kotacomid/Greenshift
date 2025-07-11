<?php
/**
 * GreenShift Business AI Generator - Installation Script
 * 
 * This script helps set up the plugin with sample data and default configuration.
 * Can be run via WP-CLI or by accessing it directly in the browser.
 * 
 * Usage:
 * - WP-CLI: wp eval-file install.php
 * - Browser: /wp-content/plugins/greenshift-business-ai-generator/install.php
 */

// Prevent direct access without WordPress
if (!defined('ABSPATH') && !defined('WP_CLI')) {
    // If accessed directly, try to load WordPress
    $wp_load_paths = array(
        __DIR__ . '/../../../../wp-load.php',
        __DIR__ . '/../../../wp-load.php',
        __DIR__ . '/../../wp-load.php',
        __DIR__ . '/../wp-load.php'
    );
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('WordPress not found. Please run this script from WordPress root or via WP-CLI.');
    }
}

class GSBA_Installer {
    
    private $messages = array();
    
    public function __construct() {
        $this->messages = array();
    }
    
    public function run() {
        $this->log('🚀 Starting GreenShift Business AI Generator Installation...');
        
        // Check prerequisites
        if (!$this->check_prerequisites()) {
            return false;
        }
        
        // Activate plugin if not active
        $this->activate_plugin();
        
        // Install default templates
        $this->install_templates();
        
        // Install sample data
        $this->install_sample_data();
        
        // Set default configuration
        $this->set_default_config();
        
        // Create initial pages
        $this->create_demo_pages();
        
        $this->log('✅ Installation completed successfully!');
        $this->display_next_steps();
        
        return true;
    }
    
    private function check_prerequisites() {
        $this->log('🔍 Checking prerequisites...');
        
        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, '6.0', '<')) {
            $this->log('❌ WordPress 6.0+ required. Current version: ' . $wp_version, 'error');
            return false;
        }
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->log('❌ PHP 7.4+ required. Current version: ' . PHP_VERSION, 'error');
            return false;
        }
        
        // Check if plugin files exist
        if (!file_exists(__DIR__ . '/greenshift-business-ai-generator.php')) {
            $this->log('❌ Plugin files not found.', 'error');
            return false;
        }
        
        $this->log('✅ Prerequisites check passed');
        return true;
    }
    
    private function activate_plugin() {
        $plugin_file = 'greenshift-business-ai-generator/greenshift-business-ai-generator.php';
        
        if (!is_plugin_active($plugin_file)) {
            $this->log('📦 Activating plugin...');
            
            $result = activate_plugin($plugin_file);
            if (is_wp_error($result)) {
                $this->log('❌ Failed to activate plugin: ' . $result->get_error_message(), 'error');
                return false;
            }
            
            $this->log('✅ Plugin activated successfully');
        } else {
            $this->log('✅ Plugin is already active');
        }
        
        return true;
    }
    
    private function install_templates() {
        $this->log('🎨 Installing default templates...');
        
        // Initialize template manager
        if (!class_exists('GSBA_Template_Manager')) {
            require_once __DIR__ . '/includes/class-database.php';
            require_once __DIR__ . '/includes/class-template-manager.php';
        }
        
        $template_manager = new GSBA_Template_Manager();
        $template_manager->install_default_templates();
        
        $this->log('✅ Default templates installed (Landing, About, Pricing)');
    }
    
    private function install_sample_data() {
        $this->log('📊 Installing sample business data...');
        
        global $wpdb;
        
        // Sample business profiles
        $sample_businesses = array(
            array(
                'user_id' => 1,
                'business_name' => 'Tech Solutions Pro',
                'business_type' => 'technology',
                'description' => 'We provide cutting-edge technology solutions for businesses. Our team specializes in custom software development, cloud migration, and digital transformation consulting.',
                'logo_url' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200&h=200&fit=crop&crop=center',
                'primary_color' => '#667eea',
                'secondary_color' => '#764ba2',
                'website_url' => 'https://techsolutionspro.com',
                'phone' => '+1 (555) 123-4567',
                'email' => 'contact@techsolutionspro.com',
                'address' => '123 Tech Street, San Francisco, CA 94105',
                'tagline' => 'Transforming businesses through technology',
                'social_media' => json_encode(array(
                    'facebook' => 'https://facebook.com/techsolutionspro',
                    'linkedin' => 'https://linkedin.com/company/techsolutionspro',
                    'whatsapp' => '+15551234567'
                ))
            ),
            array(
                'user_id' => 1,
                'business_name' => 'Green Garden Landscaping',
                'business_type' => 'landscaping',
                'description' => 'Professional landscaping services for residential and commercial properties. We create beautiful, sustainable outdoor spaces.',
                'logo_url' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=200&h=200&fit=crop&crop=center',
                'primary_color' => '#22c55e',
                'secondary_color' => '#16a34a',
                'website_url' => 'https://greengardenpro.com',
                'phone' => '+1 (555) 987-6543',
                'email' => 'info@greengardenpro.com',
                'address' => '456 Garden Avenue, Austin, TX 78701',
                'tagline' => 'Creating beautiful outdoor spaces',
                'social_media' => json_encode(array(
                    'facebook' => 'https://facebook.com/greengardenpro',
                    'instagram' => 'https://instagram.com/greengardenpro'
                ))
            )
        );
        
        $table_name = $wpdb->prefix . 'gsba_business_profiles';
        
        foreach ($sample_businesses as $business) {
            // Check if business already exists
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE business_name = %s",
                $business['business_name']
            ));
            
            if (!$existing) {
                $wpdb->insert($table_name, $business);
                $this->log('✅ Added sample business: ' . $business['business_name']);
            }
        }
        
        $this->log('✅ Sample business data installed');
    }
    
    private function set_default_config() {
        $this->log('⚙️ Setting default configuration...');
        
        $default_settings = array(
            'openai_api_key' => '',
            'claude_api_key' => '',
            'gemini_api_key' => '',
            'default_ai_model' => 'openai',
            'max_tokens' => 2000,
            'temperature' => 0.7,
            'default_primary_color' => '#667eea',
            'default_secondary_color' => '#764ba2',
            'page_status' => 'draft',
            'auto_seo' => true,
            'install_date' => current_time('mysql'),
            'version' => '1.0.0'
        );
        
        // Only update if settings don't exist
        if (!get_option('gsba_settings')) {
            update_option('gsba_settings', $default_settings);
            $this->log('✅ Default settings configured');
        } else {
            $this->log('✅ Settings already exist, skipping');
        }
    }
    
    private function create_demo_pages() {
        $this->log('📄 Creating demo pages...');
        
        // Create a sample landing page
        $demo_page = array(
            'post_title' => 'AI Generated Demo - Tech Solutions Pro',
            'post_content' => '<!-- This is a demo page generated by GreenShift Business AI Generator -->
            
<!-- wp:greenshift-blocks/container {"id":"hero-section","background":"linear-gradient(135deg, #667eea, #764ba2)","padding":"100px 20px","minHeight":"60vh","display":"flex","alignItems":"center"} -->
<div class="wp-block-greenshift-blocks-container" id="hero-section" style="background:linear-gradient(135deg, #667eea, #764ba2);padding:100px 20px;min-height:60vh;display:flex;align-items:center">

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"3rem","fontWeight":"700"},"color":{"text":"#ffffff"},"spacing":{"margin":{"bottom":"20px"}}},"className":"gsba-fade-in-up"} -->
<h1 class="wp-block-heading gsba-fade-in-up" style="color:#ffffff;margin-bottom:20px;font-size:3rem;font-weight:700">Transform Your Business with Technology</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.25rem"},"color":{"text":"rgba(255,255,255,0.9)"},"spacing":{"margin":{"bottom":"30px"}}},"className":"gsba-fade-in-up"} -->
<p class="gsba-fade-in-up" style="color:rgba(255,255,255,0.9);margin-bottom:30px;font-size:1.25rem">We help businesses leverage cutting-edge technology to increase efficiency, reduce costs, and drive growth through custom solutions.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:greenshift-blocks/container -->',
            'post_status' => 'draft',
            'post_type' => 'page',
            'meta_input' => array(
                '_gsba_generated' => true,
                '_gsba_demo' => true
            )
        );
        
        $page_id = wp_insert_post($demo_page);
        if ($page_id) {
            $this->log('✅ Demo page created (ID: ' . $page_id . ')');
            
            // Set as front page option (for demo)
            update_option('gsba_demo_page_id', $page_id);
        }
    }
    
    private function display_next_steps() {
        $this->log('');
        $this->log('🎉 INSTALLATION COMPLETE!');
        $this->log('');
        $this->log('📋 NEXT STEPS:');
        $this->log('1. Go to WordPress Admin → AI Business Gen → Settings');
        $this->log('2. Add your AI API key (OpenAI, Claude, or Gemini)');
        $this->log('3. Go to WordPress Admin → AI Business Gen');
        $this->log('4. Click "Generate Page" to create your first business page');
        $this->log('');
        $this->log('🎨 SAMPLE DATA INCLUDED:');
        $this->log('- Tech Solutions Pro (Technology)');
        $this->log('- Green Garden Landscaping (Landscaping)');
        $this->log('');
        $this->log('📝 TEMPLATES AVAILABLE:');
        $this->log('- Modern Business Landing');
        $this->log('- Professional About Us');
        $this->log('- Clear Pricing Page');
        $this->log('');
        $this->log('🔗 Quick Links:');
        $admin_url = admin_url('admin.php?page=gsba-dashboard');
        $this->log('Dashboard: ' . $admin_url);
        $this->log('Settings: ' . admin_url('admin.php?page=gsba-settings'));
        $this->log('');
    }
    
    private function log($message, $level = 'info') {
        $timestamp = date('Y-m-d H:i:s');
        $formatted_message = "[{$timestamp}] {$message}";
        
        $this->messages[] = array(
            'message' => $message,
            'level' => $level,
            'timestamp' => $timestamp
        );
        
        // Output based on context
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::log($message);
        } else {
            echo $formatted_message . PHP_EOL;
        }
        
        // Also log to WordPress debug.log if enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('GSBA Installer: ' . $message);
        }
    }
    
    public function get_messages() {
        return $this->messages;
    }
}

// Run the installer
if (!defined('WP_CLI')) {
    // If running in browser, output HTML
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>GreenShift Business AI Generator - Installation</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; margin: 40px; }
            .container { max-width: 800px; margin: 0 auto; }
            .log { background: #f1f1f1; padding: 20px; border-radius: 8px; font-family: monospace; white-space: pre-line; }
            .success { color: #22c55e; }
            .error { color: #ef4444; }
            .header { text-align: center; margin-bottom: 30px; }
            .next-steps { background: #e0f2fe; padding: 20px; border-radius: 8px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 GreenShift Business AI Generator</h1>
                <h2>Installation Script</h2>
            </div>
            
            <div class="log">
    <?php
}

// Run the installation
$installer = new GSBA_Installer();
$success = $installer->run();

if (!defined('WP_CLI')) {
    ?>
            </div>
            
            <?php if ($success): ?>
            <div class="next-steps">
                <h3>🎉 Installation Successful!</h3>
                <p><strong>Next Steps:</strong></p>
                <ol>
                    <li>Go to <a href="<?php echo admin_url('admin.php?page=gsba-settings'); ?>">AI Business Gen → Settings</a></li>
                    <li>Add your AI API key</li>
                    <li>Go to <a href="<?php echo admin_url('admin.php?page=gsba-dashboard'); ?>">AI Business Gen Dashboard</a></li>
                    <li>Click "Generate Page" to start creating</li>
                </ol>
            </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
}
?>