<?php
/**
 * Business Generator Form Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('gsba_settings', array());
$default_model = isset($settings['default_ai_model']) ? $settings['default_ai_model'] : 'openai';
?>

<div class="gsba-business-generator" id="gsba-generator-<?php echo uniqid(); ?>">
    <div class="gsba-header">
        <h3 class="gsba-title"><?php echo esc_html($attrs['title']); ?></h3>
        <p class="gsba-description">
            <?php _e('Generate professional business content using AI. Simply fill in your business details below and let AI create compelling content for your website.', 'greenshift-business-ai'); ?>
        </p>
    </div>
    
    <form class="gsba-form" id="gsba-business-form">
        <div class="gsba-form-row">
            <div class="gsba-form-group gsba-col-6">
                <label for="gsba-business-name" class="gsba-label">
                    <?php _e('Business Name', 'greenshift-business-ai'); ?> <span class="gsba-required">*</span>
                </label>
                <input type="text" id="gsba-business-name" name="business_name" class="gsba-input" 
                       placeholder="<?php _e('Enter your business name', 'greenshift-business-ai'); ?>" required>
            </div>
            
            <div class="gsba-form-group gsba-col-6">
                <label for="gsba-business-type" class="gsba-label">
                    <?php _e('Business Type', 'greenshift-business-ai'); ?> <span class="gsba-required">*</span>
                </label>
                <select id="gsba-business-type" name="business_type" class="gsba-select" required>
                    <option value=""><?php _e('Select business type', 'greenshift-business-ai'); ?></option>
                    <option value="restaurant"><?php _e('Restaurant', 'greenshift-business-ai'); ?></option>
                    <option value="retail_store"><?php _e('Retail Store', 'greenshift-business-ai'); ?></option>
                    <option value="consulting"><?php _e('Consulting', 'greenshift-business-ai'); ?></option>
                    <option value="technology"><?php _e('Technology', 'greenshift-business-ai'); ?></option>
                    <option value="healthcare"><?php _e('Healthcare', 'greenshift-business-ai'); ?></option>
                    <option value="real_estate"><?php _e('Real Estate', 'greenshift-business-ai'); ?></option>
                    <option value="law_firm"><?php _e('Law Firm', 'greenshift-business-ai'); ?></option>
                    <option value="fitness"><?php _e('Fitness & Wellness', 'greenshift-business-ai'); ?></option>
                    <option value="education"><?php _e('Education', 'greenshift-business-ai'); ?></option>
                    <option value="automotive"><?php _e('Automotive', 'greenshift-business-ai'); ?></option>
                    <option value="construction"><?php _e('Construction', 'greenshift-business-ai'); ?></option>
                    <option value="beauty"><?php _e('Beauty & Salon', 'greenshift-business-ai'); ?></option>
                    <option value="travel"><?php _e('Travel & Tourism', 'greenshift-business-ai'); ?></option>
                    <option value="financial"><?php _e('Financial Services', 'greenshift-business-ai'); ?></option>
                    <option value="marketing"><?php _e('Marketing Agency', 'greenshift-business-ai'); ?></option>
                    <option value="other"><?php _e('Other', 'greenshift-business-ai'); ?></option>
                </select>
            </div>
        </div>
        
        <div class="gsba-form-group">
            <label for="gsba-business-description" class="gsba-label">
                <?php _e('Business Description', 'greenshift-business-ai'); ?> <span class="gsba-required">*</span>
            </label>
            <textarea id="gsba-business-description" name="business_description" class="gsba-textarea" 
                      rows="4" placeholder="<?php _e('Describe your business, what you do, your target customers, and what makes you unique...', 'greenshift-business-ai'); ?>" required></textarea>
        </div>
        
        <div class="gsba-form-row">
            <div class="gsba-form-group gsba-col-6">
                <label for="gsba-content-type" class="gsba-label">
                    <?php _e('Content Type', 'greenshift-business-ai'); ?>
                </label>
                <select id="gsba-content-type" name="content_type" class="gsba-select">
                    <option value="about_us"><?php _e('About Us Section', 'greenshift-business-ai'); ?></option>
                    <option value="mission_vision"><?php _e('Mission & Vision', 'greenshift-business-ai'); ?></option>
                    <option value="services"><?php _e('Services Description', 'greenshift-business-ai'); ?></option>
                    <option value="hero_section"><?php _e('Hero Section', 'greenshift-business-ai'); ?></option>
                    <option value="features"><?php _e('Features & Benefits', 'greenshift-business-ai'); ?></option>
                    <option value="testimonials"><?php _e('Customer Testimonials', 'greenshift-business-ai'); ?></option>
                    <option value="faq"><?php _e('FAQ Section', 'greenshift-business-ai'); ?></option>
                    <option value="contact_info"><?php _e('Contact Information', 'greenshift-business-ai'); ?></option>
                    <option value="blog_post"><?php _e('Blog Post', 'greenshift-business-ai'); ?></option>
                    <option value="product_description"><?php _e('Product Description', 'greenshift-business-ai'); ?></option>
                </select>
            </div>
            
            <div class="gsba-form-group gsba-col-6">
                <label for="gsba-ai-model" class="gsba-label">
                    <?php _e('AI Model', 'greenshift-business-ai'); ?>
                </label>
                <select id="gsba-ai-model" name="ai_model" class="gsba-select">
                    <option value="openai" <?php selected($default_model, 'openai'); ?>><?php _e('OpenAI GPT-3.5', 'greenshift-business-ai'); ?></option>
                    <option value="claude" <?php selected($default_model, 'claude'); ?>><?php _e('Claude 3 Sonnet', 'greenshift-business-ai'); ?></option>
                    <option value="gemini" <?php selected($default_model, 'gemini'); ?>><?php _e('Google Gemini', 'greenshift-business-ai'); ?></option>
                </select>
            </div>
        </div>
        
        <?php if ($attrs['show_examples']): ?>
        <div class="gsba-examples">
            <h4><?php _e('Examples:', 'greenshift-business-ai'); ?></h4>
            <div class="gsba-example-cards">
                <div class="gsba-example-card">
                    <strong><?php _e('Restaurant:', 'greenshift-business-ai'); ?></strong>
                    <p><?php _e('"Bella Vista Italian Restaurant specializing in authentic Italian cuisine with fresh ingredients, homemade pasta, and a cozy family atmosphere in downtown."', 'greenshift-business-ai'); ?></p>
                </div>
                <div class="gsba-example-card">
                    <strong><?php _e('Tech Startup:', 'greenshift-business-ai'); ?></strong>
                    <p><?php _e('"InnovateApp develops mobile productivity apps for small businesses, helping them streamline operations and increase efficiency through user-friendly software solutions."', 'greenshift-business-ai'); ?></p>
                </div>
                <div class="gsba-example-card">
                    <strong><?php _e('Consulting:', 'greenshift-business-ai'); ?></strong>
                    <p><?php _e('"Strategic Business Consulting provides management consulting services to mid-size companies, specializing in operational efficiency and digital transformation."', 'greenshift-business-ai'); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="gsba-form-actions">
            <button type="submit" class="gsba-btn gsba-btn-primary" id="gsba-generate-btn">
                <span class="gsba-btn-text"><?php _e('Generate Content', 'greenshift-business-ai'); ?></span>
                <span class="gsba-loading-spinner" style="display: none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2V6M12 18V22M4.93 4.93L7.76 7.76M16.24 16.24L19.07 19.07M2 12H6M18 12H22M4.93 19.07L7.76 16.24M16.24 7.76L19.07 4.93" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
            </button>
        </div>
    </form>
    
    <div class="gsba-result" id="gsba-result" style="display: none;">
        <div class="gsba-result-header">
            <h4><?php _e('Generated Content:', 'greenshift-business-ai'); ?></h4>
            <div class="gsba-result-actions">
                <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-copy-btn">
                    <?php _e('Copy to Clipboard', 'greenshift-business-ai'); ?>
                </button>
                <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-regenerate-btn">
                    <?php _e('Regenerate', 'greenshift-business-ai'); ?>
                </button>
            </div>
        </div>
        <div class="gsba-result-content" id="gsba-result-content"></div>
        <div class="gsba-result-meta">
            <small class="gsba-tokens-info"></small>
        </div>
    </div>
    
    <div class="gsba-error" id="gsba-error" style="display: none;">
        <div class="gsba-error-content"></div>
    </div>
</div>