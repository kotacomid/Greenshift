<?php
/**
 * Admin Settings Page
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
        'temperature' => floatval($_POST['temperature'])
    );
    
    update_option('gsba_settings', $settings);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'greenshift-business-ai') . '</p></div>';
}

$settings = get_option('gsba_settings', array());
?>

<div class="wrap">
    <h1><?php _e('Business AI Generator Settings', 'greenshift-business-ai'); ?></h1>
    
    <div class="gsba-admin-header">
        <h2><?php _e('Configure your AI API keys and settings', 'greenshift-business-ai'); ?></h2>
        <p><?php _e('This plugin works with multiple AI providers. Configure at least one API key to start generating business content.', 'greenshift-business-ai'); ?></p>
    </div>
    
    <form method="post" action="">
        <?php wp_nonce_field('gsba_settings', 'gsba_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="openai_api_key"><?php _e('OpenAI API Key', 'greenshift-business-ai'); ?></label>
                </th>
                <td>
                    <input type="password" id="openai_api_key" name="openai_api_key" 
                           value="<?php echo esc_attr(isset($settings['openai_api_key']) ? $settings['openai_api_key'] : ''); ?>" 
                           class="regular-text" />
                    <p class="description">
                        <?php printf(__('Get your API key from %s', 'greenshift-business-ai'), '<a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="claude_api_key"><?php _e('Claude API Key', 'greenshift-business-ai'); ?></label>
                </th>
                <td>
                    <input type="password" id="claude_api_key" name="claude_api_key" 
                           value="<?php echo esc_attr(isset($settings['claude_api_key']) ? $settings['claude_api_key'] : ''); ?>" 
                           class="regular-text" />
                    <p class="description">
                        <?php printf(__('Get your API key from %s', 'greenshift-business-ai'), '<a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a>'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="gemini_api_key"><?php _e('Gemini API Key', 'greenshift-business-ai'); ?></label>
                </th>
                <td>
                    <input type="password" id="gemini_api_key" name="gemini_api_key" 
                           value="<?php echo esc_attr(isset($settings['gemini_api_key']) ? $settings['gemini_api_key'] : ''); ?>" 
                           class="regular-text" />
                    <p class="description">
                        <?php printf(__('Get your API key from %s', 'greenshift-business-ai'), '<a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a>'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="default_ai_model"><?php _e('Default AI Model', 'greenshift-business-ai'); ?></label>
                </th>
                <td>
                    <select id="default_ai_model" name="default_ai_model">
                        <option value="openai" <?php selected(isset($settings['default_ai_model']) ? $settings['default_ai_model'] : 'openai', 'openai'); ?>>
                            <?php _e('OpenAI GPT-3.5 Turbo', 'greenshift-business-ai'); ?>
                        </option>
                        <option value="claude" <?php selected(isset($settings['default_ai_model']) ? $settings['default_ai_model'] : 'openai', 'claude'); ?>>
                            <?php _e('Claude 3 Sonnet', 'greenshift-business-ai'); ?>
                        </option>
                        <option value="gemini" <?php selected(isset($settings['default_ai_model']) ? $settings['default_ai_model'] : 'openai', 'gemini'); ?>>
                            <?php _e('Google Gemini Pro', 'greenshift-business-ai'); ?>
                        </option>
                    </select>
                    <p class="description"><?php _e('Select the default AI model to use for content generation.', 'greenshift-business-ai'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="max_tokens"><?php _e('Max Tokens', 'greenshift-business-ai'); ?></label>
                </th>
                <td>
                    <input type="number" id="max_tokens" name="max_tokens" 
                           value="<?php echo esc_attr(isset($settings['max_tokens']) ? $settings['max_tokens'] : 1000); ?>" 
                           min="100" max="4000" step="100" />
                    <p class="description"><?php _e('Maximum number of tokens to generate (100-4000). Higher values generate longer content but cost more.', 'greenshift-business-ai'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="temperature"><?php _e('Temperature', 'greenshift-business-ai'); ?></label>
                </th>
                <td>
                    <input type="number" id="temperature" name="temperature" 
                           value="<?php echo esc_attr(isset($settings['temperature']) ? $settings['temperature'] : 0.7); ?>" 
                           min="0" max="1" step="0.1" />
                    <p class="description"><?php _e('Controls creativity (0-1). Lower values = more focused, higher values = more creative.', 'greenshift-business-ai'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Save Settings', 'greenshift-business-ai')); ?>
    </form>
    
    <hr>
    
    <div class="gsba-usage-guide">
        <h3><?php _e('How to Use', 'greenshift-business-ai'); ?></h3>
        <ol>
            <li><?php _e('Configure at least one API key above', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Add the business generator to any page or post using:', 'greenshift-business-ai'); ?>
                <ul>
                    <li><strong><?php _e('Shortcode:', 'greenshift-business-ai'); ?></strong> <code>[gsba_business_generator]</code></li>
                    <li><strong><?php _e('Gutenberg Block:', 'greenshift-business-ai'); ?></strong> <?php _e('Search for "Business AI Generator"', 'greenshift-business-ai'); ?></li>
                    <?php if (function_exists('gspb_get_greenshift_block_styles')): ?>
                    <li><strong><?php _e('GreenShift Block:', 'greenshift-business-ai'); ?></strong> <?php _e('Available in GreenShift blocks panel', 'greenshift-business-ai'); ?></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li><?php _e('Users input their business name, type, and description', 'greenshift-business-ai'); ?></li>
            <li><?php _e('AI generates professional business content instantly', 'greenshift-business-ai'); ?></li>
        </ol>
        
        <h4><?php _e('Available Content Types:', 'greenshift-business-ai'); ?></h4>
        <ul class="gsba-content-types">
            <li><?php _e('About Us Section', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Mission & Vision Statements', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Services Description', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Hero Section Content', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Key Features & Benefits', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Customer Testimonials', 'greenshift-business-ai'); ?></li>
            <li><?php _e('FAQ Section', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Contact Information', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Blog Posts', 'greenshift-business-ai'); ?></li>
            <li><?php _e('Product Descriptions', 'greenshift-business-ai'); ?></li>
        </ul>
    </div>
    
    <div class="gsba-api-info">
        <h3><?php _e('API Information', 'greenshift-business-ai'); ?></h3>
        <div class="gsba-api-cards">
            <div class="gsba-api-card">
                <h4>OpenAI</h4>
                <p><?php _e('Most popular and reliable. Good balance of quality and speed.', 'greenshift-business-ai'); ?></p>
                <p><strong><?php _e('Cost:', 'greenshift-business-ai'); ?></strong> ~$0.002 per 1K tokens</p>
            </div>
            <div class="gsba-api-card">
                <h4>Claude</h4>
                <p><?php _e('Excellent for long-form content and detailed explanations.', 'greenshift-business-ai'); ?></p>
                <p><strong><?php _e('Cost:', 'greenshift-business-ai'); ?></strong> ~$0.003 per 1K tokens</p>
            </div>
            <div class="gsba-api-card">
                <h4>Gemini</h4>
                <p><?php _e('Fast and cost-effective. Good for creative content.', 'greenshift-business-ai'); ?></p>
                <p><strong><?php _e('Cost:', 'greenshift-business-ai'); ?></strong> Free tier available</p>
            </div>
        </div>
    </div>
</div>

<style>
.gsba-admin-header {
    background: #f9f9f9;
    padding: 20px;
    border-left: 4px solid #007cba;
    margin: 20px 0;
}

.gsba-usage-guide {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    margin: 20px 0;
}

.gsba-content-types {
    columns: 2;
    list-style-type: disc;
    margin-left: 20px;
}

.gsba-api-info {
    margin: 20px 0;
}

.gsba-api-cards {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.gsba-api-card {
    flex: 1;
    background: #f0f8ff;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.gsba-api-card h4 {
    margin-top: 0;
    color: #007cba;
}
</style>