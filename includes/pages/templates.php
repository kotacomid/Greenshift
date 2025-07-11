<?php
/**
 * Templates Management Page
 * Admin interface for managing business page templates
 */

if (!defined('ABSPATH')) {
    exit;
}

$template_manager = new GSBA_Template_Manager();
$templates = $template_manager->get_all_templates();

// Handle template actions
$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
$template_id = isset($_GET['template_id']) ? intval($_GET['template_id']) : 0;

if ($action === 'edit' && $template_id) {
    $current_template = $template_manager->get_template($template_id);
}
?>

<div class="wrap gsba-templates">
    <!-- Header -->
    <div class="gsba-templates-header">
        <h1 class="gsba-main-title">
            <span class="dashicons dashicons-layout"></span>
            <?php _e('Template Management', 'greenshift-business-ai'); ?>
        </h1>
        <p class="gsba-subtitle"><?php _e('Manage business page templates for AI generation', 'greenshift-business-ai'); ?></p>
        
        <div class="gsba-header-actions">
            <button class="gsba-btn gsba-btn-primary" id="gsba-add-template-btn">
                <span class="dashicons dashicons-plus-alt2"></span>
                <?php _e('Add New Template', 'greenshift-business-ai'); ?>
            </button>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="gsba-templates-grid">
        <?php foreach ($templates as $template): ?>
            <div class="gsba-template-item">
                <div class="gsba-template-preview">
                    <img src="<?php echo esc_url($template->preview_image ?: GSBA_PLUGIN_URL . 'assets/images/template-default.jpg'); ?>" 
                         alt="<?php echo esc_attr($template->name); ?>" />
                    <div class="gsba-template-overlay">
                        <div class="gsba-template-actions">
                            <button class="gsba-btn gsba-btn-small gsba-btn-primary" 
                                    data-action="edit-template" 
                                    data-template-id="<?php echo $template->id; ?>">
                                <span class="dashicons dashicons-edit"></span>
                                <?php _e('Edit', 'greenshift-business-ai'); ?>
                            </button>
                            <button class="gsba-btn gsba-btn-small gsba-btn-secondary" 
                                    data-action="preview-template" 
                                    data-template-id="<?php echo $template->id; ?>">
                                <span class="dashicons dashicons-visibility"></span>
                                <?php _e('Preview', 'greenshift-business-ai'); ?>
                            </button>
                            <button class="gsba-btn gsba-btn-small gsba-btn-danger" 
                                    data-action="delete-template" 
                                    data-template-id="<?php echo $template->id; ?>">
                                <span class="dashicons dashicons-trash"></span>
                                <?php _e('Delete', 'greenshift-business-ai'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="gsba-template-info">
                    <div class="gsba-template-header">
                        <h3 class="gsba-template-name"><?php echo esc_html($template->name); ?></h3>
                        <span class="gsba-template-type gsba-type-<?php echo esc_attr($template->type); ?>">
                            <?php echo ucfirst($template->type); ?>
                        </span>
                    </div>
                    
                    <p class="gsba-template-description"><?php echo esc_html($template->description); ?></p>
                    
                    <div class="gsba-template-meta">
                        <span class="gsba-meta-item">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <?php echo human_time_diff(strtotime($template->created_at), current_time('timestamp')) . ' ago'; ?>
                        </span>
                        <span class="gsba-meta-item">
                            <span class="dashicons dashicons-admin-users"></span>
                            <?php echo get_user_by('id', $template->created_by)->display_name; ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- Add New Template Card -->
        <div class="gsba-template-item gsba-add-template">
            <div class="gsba-add-template-content">
                <div class="gsba-add-icon">
                    <span class="dashicons dashicons-plus-alt2"></span>
                </div>
                <h3><?php _e('Add New Template', 'greenshift-business-ai'); ?></h3>
                <p><?php _e('Create a custom template for business page generation', 'greenshift-business-ai'); ?></p>
                <button class="gsba-btn gsba-btn-primary" id="gsba-add-template-btn-card">
                    <?php _e('Create Template', 'greenshift-business-ai'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div id="gsba-template-modal" class="gsba-modal" style="display: none;">
    <div class="gsba-modal-overlay"></div>
    <div class="gsba-modal-content gsba-modal-large">
        <div class="gsba-modal-header">
            <h2 id="gsba-modal-title"><?php _e('Add New Template', 'greenshift-business-ai'); ?></h2>
            <button class="gsba-modal-close" aria-label="<?php esc_attr_e('Close', 'greenshift-business-ai'); ?>">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        
        <div class="gsba-modal-body">
            <form id="gsba-template-form" class="gsba-template-form">
                <div class="gsba-template-form-grid">
                    <!-- Basic Information -->
                    <div class="gsba-form-section">
                        <h3><?php _e('Basic Information', 'greenshift-business-ai'); ?></h3>
                        
                        <div class="gsba-form-group">
                            <label for="template_name"><?php _e('Template Name', 'greenshift-business-ai'); ?> *</label>
                            <input type="text" id="template_name" name="name" required 
                                   placeholder="<?php esc_attr_e('e.g. Modern Business Landing', 'greenshift-business-ai'); ?>">
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="template_type"><?php _e('Template Type', 'greenshift-business-ai'); ?> *</label>
                            <select id="template_type" name="type" required>
                                <option value=""><?php _e('Select template type', 'greenshift-business-ai'); ?></option>
                                <option value="landing"><?php _e('Landing Page', 'greenshift-business-ai'); ?></option>
                                <option value="about"><?php _e('About Us Page', 'greenshift-business-ai'); ?></option>
                                <option value="pricing"><?php _e('Pricing Page', 'greenshift-business-ai'); ?></option>
                                <option value="contact"><?php _e('Contact Page', 'greenshift-business-ai'); ?></option>
                                <option value="services"><?php _e('Services Page', 'greenshift-business-ai'); ?></option>
                                <option value="portfolio"><?php _e('Portfolio Page', 'greenshift-business-ai'); ?></option>
                            </select>
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="template_description"><?php _e('Description', 'greenshift-business-ai'); ?></label>
                            <textarea id="template_description" name="description" rows="3"
                                      placeholder="<?php esc_attr_e('Describe what this template includes and when to use it...', 'greenshift-business-ai'); ?>"></textarea>
                        </div>
                        
                        <div class="gsba-form-group">
                            <label for="template_preview_image"><?php _e('Preview Image URL', 'greenshift-business-ai'); ?></label>
                            <div class="gsba-image-input">
                                <input type="url" id="template_preview_image" name="preview_image" 
                                       placeholder="<?php esc_attr_e('https://yoursite.com/preview.jpg', 'greenshift-business-ai'); ?>">
                                <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-upload-preview">
                                    <?php _e('Upload', 'greenshift-business-ai'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Block Structure -->
                    <div class="gsba-form-section gsba-form-section-full">
                        <h3><?php _e('Block Structure', 'greenshift-business-ai'); ?></h3>
                        <p class="description"><?php _e('Define the GreenShift block structure for this template. Use placeholders like {business_name}, {hero_headline}, etc.', 'greenshift-business-ai'); ?></p>
                        
                        <div class="gsba-code-editor">
                            <textarea id="template_block_structure" name="block_structure" rows="15"
                                      placeholder="<?php esc_attr_e('Enter JSON block structure...', 'greenshift-business-ai'); ?>"></textarea>
                        </div>
                        
                        <div class="gsba-template-helpers">
                            <button type="button" class="gsba-btn gsba-btn-outline" id="gsba-load-sample-structure">
                                <?php _e('Load Sample Structure', 'greenshift-business-ai'); ?>
                            </button>
                            <button type="button" class="gsba-btn gsba-btn-outline" id="gsba-validate-structure">
                                <?php _e('Validate JSON', 'greenshift-business-ai'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- SEO Configuration -->
                    <div class="gsba-form-section gsba-form-section-full">
                        <h3><?php _e('SEO Configuration', 'greenshift-business-ai'); ?></h3>
                        <p class="description"><?php _e('Define SEO meta templates. Use the same placeholders as in block structure.', 'greenshift-business-ai'); ?></p>
                        
                        <div class="gsba-seo-grid">
                            <div class="gsba-form-group">
                                <label for="seo_meta_title"><?php _e('Meta Title Template', 'greenshift-business-ai'); ?></label>
                                <input type="text" id="seo_meta_title" name="seo_meta_title" 
                                       placeholder="<?php esc_attr_e('{business_name} - {hero_headline}', 'greenshift-business-ai'); ?>">
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="seo_meta_description"><?php _e('Meta Description Template', 'greenshift-business-ai'); ?></label>
                                <textarea id="seo_meta_description" name="seo_meta_description" rows="2"
                                          placeholder="<?php esc_attr_e('{hero_subheading} Contact us for professional {business_type} services.', 'greenshift-business-ai'); ?>"></textarea>
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="seo_keywords"><?php _e('Keywords Template', 'greenshift-business-ai'); ?></label>
                                <input type="text" id="seo_keywords" name="seo_keywords" 
                                       placeholder="<?php esc_attr_e('{business_type}, {business_name}, professional services', 'greenshift-business-ai'); ?>">
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="seo_og_title"><?php _e('Open Graph Title', 'greenshift-business-ai'); ?></label>
                                <input type="text" id="seo_og_title" name="seo_og_title" 
                                       placeholder="<?php esc_attr_e('{business_name} - {hero_headline}', 'greenshift-business-ai'); ?>">
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="seo_og_description"><?php _e('Open Graph Description', 'greenshift-business-ai'); ?></label>
                                <textarea id="seo_og_description" name="seo_og_description" rows="2"
                                          placeholder="<?php esc_attr_e('{hero_subheading}', 'greenshift-business-ai'); ?>"></textarea>
                            </div>
                            
                            <div class="gsba-form-group">
                                <label for="seo_og_image"><?php _e('Open Graph Image', 'greenshift-business-ai'); ?></label>
                                <input type="text" id="seo_og_image" name="seo_og_image" 
                                       placeholder="<?php esc_attr_e('{hero_image_url}', 'greenshift-business-ai'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="gsba-modal-footer">
                    <button type="button" class="gsba-btn gsba-btn-secondary" id="gsba-cancel-template">
                        <?php _e('Cancel', 'greenshift-business-ai'); ?>
                    </button>
                    <button type="submit" class="gsba-btn gsba-btn-primary" id="gsba-save-template">
                        <span class="gsba-btn-text"><?php _e('Save Template', 'greenshift-business-ai'); ?></span>
                        <span class="gsba-btn-loading" style="display: none;">
                            <span class="gsba-spinner"></span>
                            <?php _e('Saving...', 'greenshift-business-ai'); ?>
                        </span>
                    </button>
                </div>
                
                <input type="hidden" id="template_id" name="id" value="">
            </form>
        </div>
    </div>
</div>

<!-- Template Preview Modal -->
<div id="gsba-preview-modal" class="gsba-modal" style="display: none;">
    <div class="gsba-modal-overlay"></div>
    <div class="gsba-modal-content gsba-modal-large">
        <div class="gsba-modal-header">
            <h2><?php _e('Template Preview', 'greenshift-business-ai'); ?></h2>
            <button class="gsba-modal-close">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        
        <div class="gsba-modal-body">
            <div id="gsba-preview-content">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Sample Structures for Reference -->
<script type="application/json" id="gsba-sample-structures">
{
    "landing": {
        "name": "Landing Page Structure",
        "blocks": [
            {
                "blockName": "greenshift-blocks/container",
                "attrs": {
                    "id": "hero-section",
                    "background": "linear-gradient(135deg, {primary_color}, {secondary_color})",
                    "padding": "100px 20px"
                },
                "innerBlocks": [
                    {
                        "blockName": "core/heading",
                        "attrs": {
                            "level": 1,
                            "content": "{hero_headline}",
                            "style": {
                                "color": {"text": "#ffffff"},
                                "typography": {"fontSize": "3rem"}
                            }
                        }
                    },
                    {
                        "blockName": "core/paragraph",
                        "attrs": {
                            "content": "{hero_subheading}",
                            "style": {
                                "color": {"text": "rgba(255,255,255,0.9)"}
                            }
                        }
                    }
                ]
            }
        ]
    },
    "about": {
        "name": "About Page Structure",
        "blocks": [
            {
                "blockName": "greenshift-blocks/container",
                "attrs": {
                    "padding": "80px 20px"
                },
                "innerBlocks": [
                    {
                        "blockName": "core/heading",
                        "attrs": {
                            "level": 1,
                            "content": "{page_title}",
                            "textAlign": "center"
                        }
                    },
                    {
                        "blockName": "core/paragraph",
                        "attrs": {
                            "content": "{company_story}"
                        }
                    }
                ]
            }
        ]
    },
    "pricing": {
        "name": "Pricing Page Structure",
        "blocks": [
            {
                "blockName": "greenshift-blocks/container",
                "attrs": {
                    "padding": "80px 20px"
                },
                "innerBlocks": [
                    {
                        "blockName": "core/heading",
                        "attrs": {
                            "level": 1,
                            "content": "{pricing_headline}",
                            "textAlign": "center"
                        }
                    },
                    {
                        "blockName": "core/paragraph",
                        "attrs": {
                            "content": "{pricing_subheading}",
                            "textAlign": "center"
                        }
                    }
                ]
            }
        ]
    }
}
</script>