<?php
/**
 * Dashboard Page
 * Main interface for business AI generator
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get user data
$current_user = wp_get_current_user();
$db = new GSBA_Database();
$user_stats = $db->get_user_stats($current_user->ID);
$recent_pages = $db->get_user_generated_pages($current_user->ID, 5);
$template_manager = new GSBA_Template_Manager();
$templates = $template_manager->get_all_templates();
?>

<div class="wrap gsba-dashboard">
    <!-- Header -->
    <div class="gsba-dashboard-header">
        <div class="gsba-header-content">
            <div class="gsba-header-left">
                <h1 class="gsba-main-title">
                    <span class="dashicons dashicons-admin-site-alt3"></span>
                    <?php _e('Business AI Generator', 'greenshift-business-ai'); ?>
                </h1>
                <p class="gsba-subtitle"><?php _e('Generate professional business pages with AI in minutes', 'greenshift-business-ai'); ?></p>
            </div>
            <div class="gsba-header-right">
                <div class="gsba-user-info">
                    <span class="gsba-welcome"><?php echo sprintf(__('Welcome, %s', 'greenshift-business-ai'), $current_user->display_name); ?></span>
                    <?php if (current_user_can('manage_options')): ?>
                        <a href="<?php echo admin_url('admin.php?page=gsba-settings'); ?>" class="gsba-btn gsba-btn-secondary">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php _e('Settings', 'greenshift-business-ai'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="gsba-stats-grid">
        <div class="gsba-stat-card">
            <div class="gsba-stat-icon">
                <span class="dashicons dashicons-building"></span>
            </div>
            <div class="gsba-stat-content">
                <div class="gsba-stat-number"><?php echo intval($user_stats['business_profiles']); ?></div>
                <div class="gsba-stat-label"><?php _e('Business Profiles', 'greenshift-business-ai'); ?></div>
            </div>
        </div>
        
        <div class="gsba-stat-card">
            <div class="gsba-stat-icon">
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="gsba-stat-content">
                <div class="gsba-stat-number"><?php echo intval($user_stats['generated_pages']); ?></div>
                <div class="gsba-stat-label"><?php _e('Generated Pages', 'greenshift-business-ai'); ?></div>
            </div>
        </div>
        
        <div class="gsba-stat-card">
            <div class="gsba-stat-icon">
                <span class="dashicons dashicons-layout"></span>
            </div>
            <div class="gsba-stat-content">
                <div class="gsba-stat-number"><?php echo intval($user_stats['available_templates']); ?></div>
                <div class="gsba-stat-label"><?php _e('Available Templates', 'greenshift-business-ai'); ?></div>
            </div>
        </div>
        
        <div class="gsba-stat-card gsba-stat-primary">
            <div class="gsba-stat-icon">
                <span class="dashicons dashicons-plus-alt2"></span>
            </div>
            <div class="gsba-stat-content">
                <div class="gsba-stat-number"><?php _e('New', 'greenshift-business-ai'); ?></div>
                <div class="gsba-stat-label">
                    <button class="gsba-btn gsba-btn-primary" id="gsba-new-page-btn">
                        <?php _e('Generate Page', 'greenshift-business-ai'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="gsba-dashboard-content">
        <!-- Recent Pages -->
        <div class="gsba-content-section">
            <div class="gsba-section-header">
                <h2><?php _e('Recent Generated Pages', 'greenshift-business-ai'); ?></h2>
                <a href="#" class="gsba-link"><?php _e('View All', 'greenshift-business-ai'); ?></a>
            </div>
            
            <div class="gsba-pages-grid">
                <?php if (!empty($recent_pages)): ?>
                    <?php foreach ($recent_pages as $page): ?>
                        <div class="gsba-page-card">
                            <div class="gsba-page-header">
                                <h3 class="gsba-page-title"><?php echo esc_html($page->post_title); ?></h3>
                                <div class="gsba-page-status">
                                    <span class="gsba-status gsba-status-<?php echo $page->post_status; ?>">
                                        <?php echo ucfirst($page->post_status); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="gsba-page-info">
                                <div class="gsba-page-meta">
                                    <span class="gsba-meta-item">
                                        <span class="dashicons dashicons-building"></span>
                                        <?php echo esc_html($page->business_name); ?>
                                    </span>
                                    <span class="gsba-meta-item">
                                        <span class="dashicons dashicons-layout"></span>
                                        <?php echo esc_html($page->template_name); ?>
                                    </span>
                                    <span class="gsba-meta-item">
                                        <span class="dashicons dashicons-calendar-alt"></span>
                                        <?php echo human_time_diff(strtotime($page->created_at), current_time('timestamp')) . ' ago'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="gsba-page-actions">
                                <a href="<?php echo get_permalink($page->page_id); ?>" target="_blank" class="gsba-btn gsba-btn-small">
                                    <span class="dashicons dashicons-external"></span>
                                    <?php _e('View', 'greenshift-business-ai'); ?>
                                </a>
                                <a href="<?php echo get_edit_post_link($page->page_id); ?>" class="gsba-btn gsba-btn-small gsba-btn-secondary">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php _e('Edit', 'greenshift-business-ai'); ?>
                                </a>
                                <button class="gsba-btn gsba-btn-small gsba-btn-danger" data-action="delete-page" data-page-id="<?php echo $page->page_id; ?>">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php _e('Delete', 'greenshift-business-ai'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="gsba-empty-state">
                        <div class="gsba-empty-icon">
                            <span class="dashicons dashicons-admin-page"></span>
                        </div>
                        <h3><?php _e('No pages generated yet', 'greenshift-business-ai'); ?></h3>
                        <p><?php _e('Get started by creating your first business page with AI.', 'greenshift-business-ai'); ?></p>
                        <button class="gsba-btn gsba-btn-primary" id="gsba-get-started-btn">
                            <?php _e('Get Started', 'greenshift-business-ai'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Page Generator Modal -->
<div id="gsba-generator-modal" class="gsba-modal" style="display: none;">
    <div class="gsba-modal-overlay"></div>
    <div class="gsba-modal-content">
        <div class="gsba-modal-header">
            <h2><?php _e('Generate Business Page', 'greenshift-business-ai'); ?></h2>
            <button class="gsba-modal-close" aria-label="<?php esc_attr_e('Close', 'greenshift-business-ai'); ?>">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        
        <div class="gsba-modal-body">
            <form id="gsba-generator-form" class="gsba-generator-form">
                <!-- Step 1: Template Selection -->
                <div class="gsba-form-step gsba-step-active" data-step="1">
                    <div class="gsba-step-header">
                        <h3><?php _e('Choose Template', 'greenshift-business-ai'); ?></h3>
                        <p><?php _e('Select the type of page you want to create', 'greenshift-business-ai'); ?></p>
                    </div>
                    
                    <div class="gsba-templates-grid">
                        <?php foreach ($templates as $template): ?>
                            <div class="gsba-template-card" data-template-id="<?php echo $template->id; ?>">
                                <div class="gsba-template-preview">
                                    <img src="<?php echo esc_url($template->preview_image ?: GSBA_PLUGIN_URL . 'assets/images/template-default.jpg'); ?>" 
                                         alt="<?php echo esc_attr($template->name); ?>" />
                                    <div class="gsba-template-overlay">
                                        <span class="gsba-template-type"><?php echo ucfirst($template->type); ?></span>
                                    </div>
                                </div>
                                <div class="gsba-template-info">
                                    <h4 class="gsba-template-name"><?php echo esc_html($template->name); ?></h4>
                                    <p class="gsba-template-description"><?php echo esc_html($template->description); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="gsba-step-actions">
                        <button type="button" class="gsba-btn gsba-btn-primary" id="gsba-next-step" disabled>
                            <?php _e('Next: Business Info', 'greenshift-business-ai'); ?>
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Business Information -->
                <div class="gsba-form-step" data-step="2">
                    <div class="gsba-step-header">
                        <h3><?php _e('Business Information', 'greenshift-business-ai'); ?></h3>
                        <p><?php _e('Tell us about your business', 'greenshift-business-ai'); ?></p>
                    </div>
                    
                    <div class="gsba-form-grid">
                        <div class="gsba-form-group gsba-form-group-full">
                            <label for="business_name"><?php _e('Business Name', 'greenshift-business-ai'); ?> *</label>
                            <input type="text" id="business_name" name="business_name" required 
                                   placeholder="<?php esc_attr_e('Enter your business name', 'greenshift-business-ai'); ?>">
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="business_type"><?php _e('Business Type', 'greenshift-business-ai'); ?> *</label>
                            <select id="business_type" name="business_type" required>
                                <option value=""><?php _e('Select business type', 'greenshift-business-ai'); ?></option>
                                <option value="restaurant"><?php _e('Restaurant', 'greenshift-business-ai'); ?></option>
                                <option value="retail_store"><?php _e('Retail Store', 'greenshift-business-ai'); ?></option>
                                <option value="technology"><?php _e('Technology', 'greenshift-business-ai'); ?></option>
                                <option value="healthcare"><?php _e('Healthcare', 'greenshift-business-ai'); ?></option>
                                <option value="consulting"><?php _e('Consulting', 'greenshift-business-ai'); ?></option>
                                <option value="real_estate"><?php _e('Real Estate', 'greenshift-business-ai'); ?></option>
                                <option value="law_firm"><?php _e('Law Firm', 'greenshift-business-ai'); ?></option>
                                <option value="fitness"><?php _e('Fitness', 'greenshift-business-ai'); ?></option>
                                <option value="education"><?php _e('Education', 'greenshift-business-ai'); ?></option>
                                <option value="automotive"><?php _e('Automotive', 'greenshift-business-ai'); ?></option>
                                <option value="construction"><?php _e('Construction', 'greenshift-business-ai'); ?></option>
                                <option value="beauty"><?php _e('Beauty', 'greenshift-business-ai'); ?></option>
                                <option value="travel"><?php _e('Travel', 'greenshift-business-ai'); ?></option>
                                <option value="financial"><?php _e('Financial', 'greenshift-business-ai'); ?></option>
                                <option value="marketing"><?php _e('Marketing', 'greenshift-business-ai'); ?></option>
                            </select>
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="tagline"><?php _e('Tagline (Optional)', 'greenshift-business-ai'); ?></label>
                            <input type="text" id="tagline" name="tagline" 
                                   placeholder="<?php esc_attr_e('Your business tagline', 'greenshift-business-ai'); ?>">
                        </div>
                        
                        <div class="gsba-form-group gsba-form-group-full">
                            <label for="description"><?php _e('Business Description', 'greenshift-business-ai'); ?> *</label>
                            <textarea id="description" name="description" required rows="4"
                                      placeholder="<?php esc_attr_e('Describe what your business does, what makes it special, and what services you offer...', 'greenshift-business-ai'); ?>"></textarea>
                        </div>
                    </div>
                    
                    <div class="gsba-step-actions">
                        <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-prev-step">
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                            <?php _e('Previous', 'greenshift-business-ai'); ?>
                        </button>
                        <button type="button" class="gsba-btn gsba-btn-primary" id="gsba-next-step-2">
                            <?php _e('Next: Contact Info', 'greenshift-business-ai'); ?>
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Contact & Branding -->
                <div class="gsba-form-step" data-step="3">
                    <div class="gsba-step-header">
                        <h3><?php _e('Contact & Branding', 'greenshift-business-ai'); ?></h3>
                        <p><?php _e('Add your contact information and branding', 'greenshift-business-ai'); ?></p>
                    </div>
                    
                    <div class="gsba-form-grid">
                        <div class="gsba-form-group">
                            <label for="phone"><?php _e('Phone Number', 'greenshift-business-ai'); ?></label>
                            <input type="tel" id="phone" name="phone" 
                                   placeholder="<?php esc_attr_e('+1 (555) 123-4567', 'greenshift-business-ai'); ?>">
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="email"><?php _e('Email Address', 'greenshift-business-ai'); ?></label>
                            <input type="email" id="email" name="email" 
                                   placeholder="<?php esc_attr_e('contact@yourbusiness.com', 'greenshift-business-ai'); ?>">
                        </div>
                        
                        <div class="gsba-form-group gsba-form-group-full">
                            <label for="address"><?php _e('Business Address', 'greenshift-business-ai'); ?></label>
                            <textarea id="address" name="address" rows="2"
                                      placeholder="<?php esc_attr_e('123 Main Street, City, State, ZIP', 'greenshift-business-ai'); ?>"></textarea>
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="website_url"><?php _e('Website URL', 'greenshift-business-ai'); ?></label>
                            <input type="url" id="website_url" name="website_url" 
                                   placeholder="<?php esc_attr_e('https://yourbusiness.com', 'greenshift-business-ai'); ?>">
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="logo_url"><?php _e('Logo URL', 'greenshift-business-ai'); ?></label>
                            <div class="gsba-logo-input">
                                <input type="url" id="logo_url" name="logo_url" 
                                       placeholder="<?php esc_attr_e('https://yoursite.com/logo.png', 'greenshift-business-ai'); ?>">
                                <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-upload-logo">
                                    <?php _e('Upload', 'greenshift-business-ai'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="primary_color"><?php _e('Primary Color', 'greenshift-business-ai'); ?></label>
                            <input type="color" id="primary_color" name="primary_color" value="#667eea">
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="secondary_color"><?php _e('Secondary Color', 'greenshift-business-ai'); ?></label>
                            <input type="color" id="secondary_color" name="secondary_color" value="#764ba2">
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="gsba-form-section">
                        <h4><?php _e('Social Media (Optional)', 'greenshift-business-ai'); ?></h4>
                        <div class="gsba-form-grid">
                            <div class="gsba-form-group">
                                <label for="social_facebook"><?php _e('Facebook', 'greenshift-business-ai'); ?></label>
                                <input type="url" id="social_facebook" name="social_facebook" 
                                       placeholder="<?php esc_attr_e('https://facebook.com/yourbusiness', 'greenshift-business-ai'); ?>">
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="social_instagram"><?php _e('Instagram', 'greenshift-business-ai'); ?></label>
                                <input type="url" id="social_instagram" name="social_instagram" 
                                       placeholder="<?php esc_attr_e('https://instagram.com/yourbusiness', 'greenshift-business-ai'); ?>">
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="social_whatsapp"><?php _e('WhatsApp', 'greenshift-business-ai'); ?></label>
                                <input type="tel" id="social_whatsapp" name="social_whatsapp" 
                                       placeholder="<?php esc_attr_e('+1234567890', 'greenshift-business-ai'); ?>">
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="social_linkedin"><?php _e('LinkedIn', 'greenshift-business-ai'); ?></label>
                                <input type="url" id="social_linkedin" name="social_linkedin" 
                                       placeholder="<?php esc_attr_e('https://linkedin.com/company/yourbusiness', 'greenshift-business-ai'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="gsba-step-actions">
                        <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-prev-step-2">
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                            <?php _e('Previous', 'greenshift-business-ai'); ?>
                        </button>
                        <button type="submit" class="gsba-btn gsba-btn-primary gsba-btn-generate" id="gsba-generate-btn">
                            <span class="gsba-btn-text"><?php _e('Generate Page with AI', 'greenshift-business-ai'); ?></span>
                            <span class="gsba-btn-loading" style="display: none;">
                                <span class="gsba-spinner"></span>
                                <?php _e('Generating...', 'greenshift-business-ai'); ?>
                            </span>
                            <span class="dashicons dashicons-admin-site-alt3"></span>
                        </button>
                    </div>
                </div>
                
                <input type="hidden" id="selected_template_id" name="template_id" value="">
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="gsba-success-modal" class="gsba-modal" style="display: none;">
    <div class="gsba-modal-overlay"></div>
    <div class="gsba-modal-content gsba-modal-success">
        <div class="gsba-modal-header">
            <div class="gsba-success-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <h2><?php _e('Page Generated Successfully!', 'greenshift-business-ai'); ?></h2>
            <button class="gsba-modal-close">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        
        <div class="gsba-modal-body">
            <p><?php _e('Your business page has been generated with AI. You can now view, edit, or publish it.', 'greenshift-business-ai'); ?></p>
            
            <div class="gsba-success-actions">
                <a href="#" id="gsba-view-page" class="gsba-btn gsba-btn-primary" target="_blank">
                    <span class="dashicons dashicons-external"></span>
                    <?php _e('View Page', 'greenshift-business-ai'); ?>
                </a>
                <a href="#" id="gsba-edit-page" class="gsba-btn gsba-btn-secondary">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Edit Page', 'greenshift-business-ai'); ?>
                </a>
                <button class="gsba-btn gsba-btn-outline" id="gsba-generate-another">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Generate Another', 'greenshift-business-ai'); ?>
                </button>
            </div>
        </div>
    </div>
</div>