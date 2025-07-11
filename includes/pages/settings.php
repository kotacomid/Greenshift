<?php
/**
 * Settings Page
 * Plugin configuration and AI API settings
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit']) && wp_verify_nonce($_POST['gsba_settings_nonce'], 'gsba_settings')) {
    $settings = array(
        'openai_api_key' => sanitize_text_field($_POST['openai_api_key']),
        'claude_api_key' => sanitize_text_field($_POST['claude_api_key']),
        'gemini_api_key' => sanitize_text_field($_POST['gemini_api_key']),
        'default_ai_model' => sanitize_text_field($_POST['default_ai_model']),
        'max_tokens' => intval($_POST['max_tokens']),
        'temperature' => floatval($_POST['temperature']),
        'default_primary_color' => sanitize_hex_color($_POST['default_primary_color']),
        'default_secondary_color' => sanitize_hex_color($_POST['default_secondary_color']),
        'page_status' => sanitize_text_field($_POST['page_status'])
    );
    
    update_option('gsba_settings', $settings);
    
    echo '<div class="notice notice-success is-dismissible"><p>' . 
         __('Settings saved successfully!', 'greenshift-business-ai') . 
         '</p></div>';
}

// Get current settings
$settings = get_option('gsba_settings', array());
$defaults = array(
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

$settings = wp_parse_args($settings, $defaults);

// Get global stats for admin
$db = new GSBA_Database();
$global_stats = $db->get_global_stats();
?>

<div class="wrap gsba-settings">
    <!-- Header -->
    <div class="gsba-settings-header">
        <h1 class="gsba-main-title">
            <span class="dashicons dashicons-admin-settings"></span>
            <?php _e('AI Generator Settings', 'greenshift-business-ai'); ?>
        </h1>
        <p class="gsba-subtitle"><?php _e('Configure AI models and plugin settings', 'greenshift-business-ai'); ?></p>
    </div>

    <!-- Global Stats (Admin Only) -->
    <div class="gsba-admin-stats">
        <h2><?php _e('Usage Statistics', 'greenshift-business-ai'); ?></h2>
        <div class="gsba-stats-grid">
            <div class="gsba-stat-card">
                <div class="gsba-stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="gsba-stat-content">
                    <div class="gsba-stat-number"><?php echo intval($global_stats['active_users']); ?></div>
                    <div class="gsba-stat-label"><?php _e('Active Users', 'greenshift-business-ai'); ?></div>
                </div>
            </div>
            
            <div class="gsba-stat-card">
                <div class="gsba-stat-icon">
                    <span class="dashicons dashicons-building"></span>
                </div>
                <div class="gsba-stat-content">
                    <div class="gsba-stat-number"><?php echo intval($global_stats['total_profiles']); ?></div>
                    <div class="gsba-stat-label"><?php _e('Total Profiles', 'greenshift-business-ai'); ?></div>
                </div>
            </div>
            
            <div class="gsba-stat-card">
                <div class="gsba-stat-icon">
                    <span class="dashicons dashicons-admin-page"></span>
                </div>
                <div class="gsba-stat-content">
                    <div class="gsba-stat-number"><?php echo intval($global_stats['total_pages']); ?></div>
                    <div class="gsba-stat-label"><?php _e('Generated Pages', 'greenshift-business-ai'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form method="post" action="" class="gsba-settings-form">
        <?php wp_nonce_field('gsba_settings', 'gsba_settings_nonce'); ?>
        
        <!-- AI Configuration -->
        <div class="gsba-settings-section">
            <h2><?php _e('AI Configuration', 'greenshift-business-ai'); ?></h2>
            <p class="description"><?php _e('Configure your AI API keys and settings', 'greenshift-business-ai'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="default_ai_model"><?php _e('Default AI Model', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <select id="default_ai_model" name="default_ai_model" class="regular-text">
                            <option value="openai" <?php selected($settings['default_ai_model'], 'openai'); ?>>
                                OpenAI GPT-3.5 Turbo
                            </option>
                            <option value="claude" <?php selected($settings['default_ai_model'], 'claude'); ?>>
                                Claude 3 Sonnet
                            </option>
                            <option value="gemini" <?php selected($settings['default_ai_model'], 'gemini'); ?>>
                                Google Gemini Pro
                            </option>
                        </select>
                        <p class="description"><?php _e('Select the default AI model for content generation.', 'greenshift-business-ai'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="openai_api_key"><?php _e('OpenAI API Key', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <div class="gsba-api-key-input">
                            <input type="password" id="openai_api_key" name="openai_api_key" 
                                   value="<?php echo esc_attr($settings['openai_api_key']); ?>" 
                                   class="regular-text" placeholder="sk-...">
                            <button type="button" class="gsba-toggle-password" tabindex="-1">
                                <?php _e('Show', 'greenshift-business-ai'); ?>
                            </button>
                            <button type="button" class="gsba-test-api" data-model="openai">
                                <?php _e('Test', 'greenshift-business-ai'); ?>
                            </button>
                        </div>
                        <p class="description">
                            <?php echo sprintf(
                                __('Get your API key from %s', 'greenshift-business-ai'),
                                '<a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_api_key"><?php _e('Claude API Key', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <div class="gsba-api-key-input">
                            <input type="password" id="claude_api_key" name="claude_api_key" 
                                   value="<?php echo esc_attr($settings['claude_api_key']); ?>" 
                                   class="regular-text" placeholder="sk-ant-...">
                            <button type="button" class="gsba-toggle-password" tabindex="-1">
                                <?php _e('Show', 'greenshift-business-ai'); ?>
                            </button>
                            <button type="button" class="gsba-test-api" data-model="claude">
                                <?php _e('Test', 'greenshift-business-ai'); ?>
                            </button>
                        </div>
                        <p class="description">
                            <?php echo sprintf(
                                __('Get your API key from %s', 'greenshift-business-ai'),
                                '<a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="gemini_api_key"><?php _e('Gemini API Key', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <div class="gsba-api-key-input">
                            <input type="password" id="gemini_api_key" name="gemini_api_key" 
                                   value="<?php echo esc_attr($settings['gemini_api_key']); ?>" 
                                   class="regular-text" placeholder="AIza...">
                            <button type="button" class="gsba-toggle-password" tabindex="-1">
                                <?php _e('Show', 'greenshift-business-ai'); ?>
                            </button>
                            <button type="button" class="gsba-test-api" data-model="gemini">
                                <?php _e('Test', 'greenshift-business-ai'); ?>
                            </button>
                        </div>
                        <p class="description">
                            <?php echo sprintf(
                                __('Get your API key from %s', 'greenshift-business-ai'),
                                '<a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Generation Settings -->
        <div class="gsba-settings-section">
            <h2><?php _e('Generation Settings', 'greenshift-business-ai'); ?></h2>
            <p class="description"><?php _e('Fine-tune AI content generation parameters', 'greenshift-business-ai'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="max_tokens"><?php _e('Max Tokens', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="max_tokens" name="max_tokens" 
                               value="<?php echo esc_attr($settings['max_tokens']); ?>" 
                               class="small-text" min="100" max="4000" step="100">
                        <p class="description"><?php _e('Maximum number of tokens per AI request (100-4000).', 'greenshift-business-ai'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="temperature"><?php _e('Temperature', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="temperature" name="temperature" 
                               value="<?php echo esc_attr($settings['temperature']); ?>" 
                               class="small-text" min="0" max="1" step="0.1">
                        <p class="description"><?php _e('Controls randomness in AI responses (0-1). Lower = more focused, Higher = more creative.', 'greenshift-business-ai'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Default Styling -->
        <div class="gsba-settings-section">
            <h2><?php _e('Default Styling', 'greenshift-business-ai'); ?></h2>
            <p class="description"><?php _e('Set default colors for generated pages', 'greenshift-business-ai'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="default_primary_color"><?php _e('Default Primary Color', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="default_primary_color" name="default_primary_color" 
                               value="<?php echo esc_attr($settings['default_primary_color']); ?>">
                        <p class="description"><?php _e('Default primary color for new business pages.', 'greenshift-business-ai'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="default_secondary_color"><?php _e('Default Secondary Color', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="default_secondary_color" name="default_secondary_color" 
                               value="<?php echo esc_attr($settings['default_secondary_color']); ?>">
                        <p class="description"><?php _e('Default secondary color for new business pages.', 'greenshift-business-ai'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Page Settings -->
        <div class="gsba-settings-section">
            <h2><?php _e('Page Settings', 'greenshift-business-ai'); ?></h2>
            <p class="description"><?php _e('Configure how generated pages are created', 'greenshift-business-ai'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="page_status"><?php _e('Default Page Status', 'greenshift-business-ai'); ?></label>
                    </th>
                    <td>
                        <select id="page_status" name="page_status" class="regular-text">
                            <option value="draft" <?php selected($settings['page_status'], 'draft'); ?>>
                                <?php _e('Draft', 'greenshift-business-ai'); ?>
                            </option>
                            <option value="publish" <?php selected($settings['page_status'], 'publish'); ?>>
                                <?php _e('Published', 'greenshift-business-ai'); ?>
                            </option>
                            <option value="private" <?php selected($settings['page_status'], 'private'); ?>>
                                <?php _e('Private', 'greenshift-business-ai'); ?>
                            </option>
                        </select>
                        <p class="description"><?php _e('Status for newly generated pages.', 'greenshift-business-ai'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(__('Save Settings', 'greenshift-business-ai')); ?>
    </form>

    <!-- Usage Guide -->
    <div class="gsba-usage-guide">
        <h2><?php _e('Getting Started', 'greenshift-business-ai'); ?></h2>
        
        <div class="gsba-guide-content">
            <h3><?php _e('Setup Instructions', 'greenshift-business-ai'); ?></h3>
            <ol>
                <li><?php _e('Choose your preferred AI model and obtain an API key from the provider.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Enter your API key in the appropriate field above and test the connection.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Adjust generation settings if needed (default values work well for most cases).', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Set your preferred default colors for generated pages.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Go to the Dashboard to start generating business pages!', 'greenshift-business-ai'); ?></li>
            </ol>
            
            <h3><?php _e('Supported Content Types', 'greenshift-business-ai'); ?></h3>
            <div class="gsba-content-types">
                <div class="gsba-content-type">
                    <h4><?php _e('Landing Page', 'greenshift-business-ai'); ?></h4>
                    <p><?php _e('Complete business landing page with hero section, features, about preview, and contact information.', 'greenshift-business-ai'); ?></p>
                </div>
                
                <div class="gsba-content-type">
                    <h4><?php _e('About Us Page', 'greenshift-business-ai'); ?></h4>
                    <p><?php _e('Comprehensive about page with company story, mission, vision, values, and team information.', 'greenshift-business-ai'); ?></p>
                </div>
                
                <div class="gsba-content-type">
                    <h4><?php _e('Pricing Page', 'greenshift-business-ai'); ?></h4>
                    <p><?php _e('Professional pricing page with service packages, feature comparison, and FAQ section.', 'greenshift-business-ai'); ?></p>
                </div>
            </div>
            
            <h3><?php _e('Tips for Best Results', 'greenshift-business-ai'); ?></h3>
            <ul>
                <li><?php _e('Provide detailed business descriptions for more accurate AI content generation.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Include specific services, unique selling points, and target audience information.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Add contact information and branding to make pages look professional.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Review and edit generated content before publishing.', 'greenshift-business-ai'); ?></li>
                <li><?php _e('Use high-quality logos and appropriate brand colors for better visual appeal.', 'greenshift-business-ai'); ?></li>
            </ul>
        </div>
    </div>
</div>