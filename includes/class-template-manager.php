<?php
/**
 * Template Manager Class
 * Manages business page templates
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSBA_Template_Manager {
    
    private $db;
    
    public function __construct() {
        $this->db = new GSBA_Database();
    }
    
    public function install_default_templates() {
        $templates = $this->get_default_templates();
        
        foreach ($templates as $template) {
            // Check if template already exists
            $existing = $this->db->get_all_templates($template['type']);
            $exists = false;
            
            foreach ($existing as $existing_template) {
                if ($existing_template->name === $template['name']) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $template['created_by'] = 1; // Admin user
                $this->db->save_template($template);
            }
        }
    }
    
    public function get_default_templates() {
        return array(
            $this->get_landing_template(),
            $this->get_about_template(),
            $this->get_pricing_template()
        );
    }
    
    public function get_all_templates() {
        return $this->db->get_all_templates();
    }
    
    public function save_template($data) {
        $template_data = array(
            'name' => sanitize_text_field($data['name']),
            'type' => sanitize_text_field($data['type']),
            'description' => sanitize_textarea_field($data['description']),
            'preview_image' => esc_url_raw($data['preview_image']),
            'block_structure' => wp_json_encode($data['block_structure']),
            'seo_config' => wp_json_encode($data['seo_config']),
            'created_by' => get_current_user_id(),
            'status' => 'active'
        );
        
        if (isset($data['id'])) {
            $template_data['id'] = intval($data['id']);
        }
        
        try {
            $template_id = $this->db->save_template($template_data);
            return array(
                'success' => true,
                'message' => __('Template saved successfully.', 'greenshift-business-ai'),
                'template_id' => $template_id
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => __('Failed to save template: ', 'greenshift-business-ai') . $e->getMessage()
            );
        }
    }
    
    public function delete_template($template_id) {
        return $this->db->delete_template($template_id);
    }
    
    public function get_template($template_id) {
        return $this->db->get_template($template_id);
    }
    
    private function get_landing_template() {
        return array(
            'name' => 'Modern Business Landing',
            'type' => 'landing',
            'description' => 'Professional landing page with hero section, features, about preview, and contact form. Perfect for service businesses.',
            'preview_image' => GSBA_PLUGIN_URL . 'assets/images/templates/landing-preview.jpg',
            'block_structure' => $this->get_landing_blocks(),
            'seo_config' => array(
                'meta_title' => '{business_name} - {hero_headline}',
                'meta_description' => '{hero_subheading} Contact us for professional {business_type} services.',
                'keywords' => '{business_type}, {business_name}, professional services',
                'og_title' => '{business_name} - {hero_headline}',
                'og_description' => '{hero_subheading}',
                'og_image' => '{hero_image_url}'
            )
        );
    }
    
    private function get_about_template() {
        return array(
            'name' => 'Professional About Us',
            'type' => 'about',
            'description' => 'Comprehensive about page showcasing company story, mission, vision, values, and team information.',
            'preview_image' => GSBA_PLUGIN_URL . 'assets/images/templates/about-preview.jpg',
            'block_structure' => $this->get_about_blocks(),
            'seo_config' => array(
                'meta_title' => 'About {business_name} - {page_title}',
                'meta_description' => '{company_story}',
                'keywords' => 'about {business_name}, {business_type} company, our story',
                'og_title' => 'About {business_name}',
                'og_description' => '{mission_statement}',
                'og_image' => '{logo_url}'
            )
        );
    }
    
    private function get_pricing_template() {
        return array(
            'name' => 'Clear Pricing Page',
            'type' => 'pricing',
            'description' => 'Transparent pricing page with service packages, feature comparison, and FAQ section.',
            'preview_image' => GSBA_PLUGIN_URL . 'assets/images/templates/pricing-preview.jpg',
            'block_structure' => $this->get_pricing_blocks(),
            'seo_config' => array(
                'meta_title' => '{business_name} Pricing - {pricing_headline}',
                'meta_description' => '{pricing_subheading} View our transparent pricing for {business_type} services.',
                'keywords' => '{business_name} pricing, {business_type} cost, service packages',
                'og_title' => '{business_name} Pricing',
                'og_description' => '{pricing_subheading}',
                'og_image' => '{logo_url}'
            )
        );
    }
    
    private function get_landing_blocks() {
        return array(
            // Hero Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'hero-section',
                    'background' => 'linear-gradient(135deg, {primary_color}, {secondary_color})',
                    'padding' => '100px 20px',
                    'minHeight' => '100vh',
                    'display' => 'flex',
                    'alignItems' => 'center'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array(
                            'align' => 'center',
                            'gap' => '50px'
                        ),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '60%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/image',
                                        'attrs' => array(
                                            'url' => '{logo_url}',
                                            'alt' => '{business_name} logo',
                                            'width' => '120px',
                                            'style' => array('margin' => array('bottom' => '30px'))
                                        )
                                    ),
                                    array(
                                        'blockName' => 'core/heading',
                                        'attrs' => array(
                                            'level' => 1,
                                            'content' => '{hero_headline}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '3.5rem', 'fontWeight' => '700'),
                                                'color' => array('text' => '#ffffff'),
                                                'spacing' => array('margin' => array('bottom' => '20px'))
                                            ),
                                            'className' => 'gsba-fade-in-up'
                                        )
                                    ),
                                    array(
                                        'blockName' => 'core/paragraph',
                                        'attrs' => array(
                                            'content' => '{hero_subheading}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '1.25rem', 'lineHeight' => '1.6'),
                                                'color' => array('text' => 'rgba(255,255,255,0.9)'),
                                                'spacing' => array('margin' => array('bottom' => '40px'))
                                            ),
                                            'className' => 'gsba-fade-in-up gsba-delay-200'
                                        )
                                    ),
                                    array(
                                        'blockName' => 'greenshift-blocks/button',
                                        'attrs' => array(
                                            'text' => '{hero_cta_text}',
                                            'url' => '#contact',
                                            'style' => array(
                                                'color' => array('background' => '#ffffff', 'text' => '{primary_color}'),
                                                'border' => array('radius' => '50px'),
                                                'spacing' => array('padding' => array('top' => '15px', 'bottom' => '15px', 'left' => '35px', 'right' => '35px'))
                                            ),
                                            'className' => 'gsba-fade-in-up gsba-delay-400'
                                        )
                                    )
                                )
                            ),
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '40%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/image',
                                        'attrs' => array(
                                            'url' => '{hero_image_url}',
                                            'alt' => '{business_name} hero image',
                                            'style' => array('border' => array('radius' => '20px')),
                                            'className' => 'gsba-fade-in-right'
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // Features Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'features-section',
                    'padding' => '100px 20px',
                    'background' => '#f8fafc'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 2,
                            'content' => '{features_heading}',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '2.5rem', 'fontWeight' => '600'),
                                'spacing' => array('margin' => array('bottom' => '60px'))
                            ),
                            'className' => 'gsba-fade-in-up'
                        )
                    ),
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('gap' => '40px'),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '33.33%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/infobox',
                                        'attrs' => array(
                                            'icon' => '{feature_1_icon}',
                                            'title' => '{feature_1_title}',
                                            'description' => '{feature_1_description}',
                                            'style' => 'modern',
                                            'iconSize' => '60px',
                                            'textAlign' => 'center',
                                            'className' => 'gsba-fade-in-up'
                                        )
                                    )
                                )
                            ),
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '33.33%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/infobox',
                                        'attrs' => array(
                                            'icon' => '{feature_2_icon}',
                                            'title' => '{feature_2_title}',
                                            'description' => '{feature_2_description}',
                                            'style' => 'modern',
                                            'iconSize' => '60px',
                                            'textAlign' => 'center',
                                            'className' => 'gsba-fade-in-up gsba-delay-200'
                                        )
                                    )
                                )
                            ),
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '33.33%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/infobox',
                                        'attrs' => array(
                                            'icon' => '{feature_3_icon}',
                                            'title' => '{feature_3_title}',
                                            'description' => '{feature_3_description}',
                                            'style' => 'modern',
                                            'iconSize' => '60px',
                                            'textAlign' => 'center',
                                            'className' => 'gsba-fade-in-up gsba-delay-400'
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // About Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'about-section',
                    'padding' => '100px 20px'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('align' => 'center', 'gap' => '60px'),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '50%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/heading',
                                        'attrs' => array(
                                            'level' => 2,
                                            'content' => '{about_heading}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '2.25rem', 'fontWeight' => '600'),
                                                'spacing' => array('margin' => array('bottom' => '30px'))
                                            ),
                                            'className' => 'gsba-fade-in-left'
                                        )
                                    ),
                                    array(
                                        'blockName' => 'core/paragraph',
                                        'attrs' => array(
                                            'content' => '{about_description}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '1.125rem', 'lineHeight' => '1.7'),
                                                'spacing' => array('margin' => array('bottom' => '30px'))
                                            ),
                                            'className' => 'gsba-fade-in-left gsba-delay-200'
                                        )
                                    ),
                                    array(
                                        'blockName' => 'greenshift-blocks/button',
                                        'attrs' => array(
                                            'text' => 'Learn More About Us',
                                            'url' => '/about',
                                            'style' => array(
                                                'color' => array('background' => '{primary_color}', 'text' => '#ffffff'),
                                                'border' => array('radius' => '8px'),
                                                'spacing' => array('padding' => array('top' => '12px', 'bottom' => '12px', 'left' => '24px', 'right' => '24px'))
                                            ),
                                            'className' => 'gsba-fade-in-left gsba-delay-400'
                                        )
                                    )
                                )
                            ),
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '50%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/image',
                                        'attrs' => array(
                                            'url' => '{about_image_url}',
                                            'alt' => 'About {business_name}',
                                            'style' => array('border' => array('radius' => '15px')),
                                            'className' => 'gsba-fade-in-right'
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // Contact Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'contact-section',
                    'padding' => '100px 20px',
                    'background' => '{primary_color}',
                    'style' => array('color' => array('text' => '#ffffff'))
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 2,
                            'content' => '{contact_heading}',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '2.5rem', 'fontWeight' => '600'),
                                'color' => array('text' => '#ffffff'),
                                'spacing' => array('margin' => array('bottom' => '20px'))
                            ),
                            'className' => 'gsba-fade-in-up'
                        )
                    ),
                    array(
                        'blockName' => 'core/paragraph',
                        'attrs' => array(
                            'content' => '{contact_info}',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '1.125rem'),
                                'color' => array('text' => 'rgba(255,255,255,0.9)'),
                                'spacing' => array('margin' => array('bottom' => '40px'))
                            ),
                            'className' => 'gsba-fade-in-up gsba-delay-200'
                        )
                    ),
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('justify' => 'center'),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '60%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/row',
                                        'attrs' => array('gap' => '20px', 'justify' => 'center'),
                                        'innerBlocks' => array(
                                            array(
                                                'blockName' => 'greenshift-blocks/column',
                                                'attrs' => array('width' => 'auto'),
                                                'innerBlocks' => array(
                                                    array(
                                                        'blockName' => 'greenshift-blocks/button',
                                                        'attrs' => array(
                                                            'text' => 'Call Us: {phone}',
                                                            'url' => 'tel:{phone}',
                                                            'style' => array(
                                                                'color' => array('background' => '#ffffff', 'text' => '{primary_color}'),
                                                                'border' => array('radius' => '50px'),
                                                                'spacing' => array('padding' => array('top' => '12px', 'bottom' => '12px', 'left' => '25px', 'right' => '25px'))
                                                            ),
                                                            'className' => 'gsba-fade-in-up gsba-delay-400'
                                                        )
                                                    )
                                                )
                                            ),
                                            array(
                                                'blockName' => 'greenshift-blocks/column',
                                                'attrs' => array('width' => 'auto'),
                                                'innerBlocks' => array(
                                                    array(
                                                        'blockName' => 'greenshift-blocks/button',
                                                        'attrs' => array(
                                                            'text' => 'Email Us',
                                                            'url' => 'mailto:{email}',
                                                            'style' => array(
                                                                'color' => array('background' => 'transparent', 'text' => '#ffffff'),
                                                                'border' => array('color' => '#ffffff', 'width' => '2px', 'radius' => '50px'),
                                                                'spacing' => array('padding' => array('top' => '12px', 'bottom' => '12px', 'left' => '25px', 'right' => '25px'))
                                                            ),
                                                            'className' => 'gsba-fade-in-up gsba-delay-600'
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }
    
    private function get_about_blocks() {
        return array(
            // Hero Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'about-hero',
                    'padding' => '80px 20px',
                    'textAlign' => 'center',
                    'background' => 'linear-gradient(135deg, {primary_color}, {secondary_color})'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/image',
                        'attrs' => array(
                            'url' => '{logo_url}',
                            'alt' => '{business_name} logo',
                            'width' => '100px',
                            'style' => array('margin' => array('bottom' => '30px'))
                        )
                    ),
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 1,
                            'content' => '{page_title}',
                            'style' => array(
                                'typography' => array('fontSize' => '3rem', 'fontWeight' => '700'),
                                'color' => array('text' => '#ffffff')
                            )
                        )
                    )
                )
            ),
            
            // Company Story Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'company-story',
                    'padding' => '100px 20px'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('justify' => 'center'),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '80%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/heading',
                                        'attrs' => array(
                                            'level' => 2,
                                            'content' => 'Our Story',
                                            'textAlign' => 'center',
                                            'style' => array(
                                                'typography' => array('fontSize' => '2.5rem', 'fontWeight' => '600'),
                                                'spacing' => array('margin' => array('bottom' => '40px'))
                                            )
                                        )
                                    ),
                                    array(
                                        'blockName' => 'core/paragraph',
                                        'attrs' => array(
                                            'content' => '{company_story}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '1.25rem', 'lineHeight' => '1.8'),
                                                'spacing' => array('margin' => array('bottom' => '40px'))
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // Mission & Vision Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'mission-vision',
                    'padding' => '80px 20px',
                    'background' => '#f8fafc'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('gap' => '60px'),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '50%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/heading',
                                        'attrs' => array(
                                            'level' => 3,
                                            'content' => 'Our Mission',
                                            'style' => array(
                                                'typography' => array('fontSize' => '2rem', 'fontWeight' => '600'),
                                                'spacing' => array('margin' => array('bottom' => '20px'))
                                            )
                                        )
                                    ),
                                    array(
                                        'blockName' => 'core/paragraph',
                                        'attrs' => array(
                                            'content' => '{mission_statement}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '1.125rem', 'lineHeight' => '1.7')
                                            )
                                        )
                                    )
                                )
                            ),
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '50%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/heading',
                                        'attrs' => array(
                                            'level' => 3,
                                            'content' => 'Our Vision',
                                            'style' => array(
                                                'typography' => array('fontSize' => '2rem', 'fontWeight' => '600'),
                                                'spacing' => array('margin' => array('bottom' => '20px'))
                                            )
                                        )
                                    ),
                                    array(
                                        'blockName' => 'core/paragraph',
                                        'attrs' => array(
                                            'content' => '{vision_statement}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '1.125rem', 'lineHeight' => '1.7')
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // Core Values Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'core-values',
                    'padding' => '100px 20px'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 2,
                            'content' => 'Our Core Values',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '2.5rem', 'fontWeight' => '600'),
                                'spacing' => array('margin' => array('bottom' => '60px'))
                            )
                        )
                    ),
                    array(
                        'blockName' => 'core/paragraph',
                        'attrs' => array(
                            'content' => '{core_values}',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '1.125rem', 'lineHeight' => '1.8')
                            )
                        )
                    )
                )
            ),
            
            // Team Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'team-section',
                    'padding' => '80px 20px',
                    'background' => '#f8fafc'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 2,
                            'content' => 'Our Team',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '2.5rem', 'fontWeight' => '600'),
                                'spacing' => array('margin' => array('bottom' => '40px'))
                            )
                        )
                    ),
                    array(
                        'blockName' => 'core/paragraph',
                        'attrs' => array(
                            'content' => '{team_intro}',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '1.125rem', 'lineHeight' => '1.7')
                            )
                        )
                    )
                )
            ),
            
            // Call to Action
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'about-cta',
                    'padding' => '80px 20px',
                    'textAlign' => 'center',
                    'background' => '{primary_color}'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/paragraph',
                        'attrs' => array(
                            'content' => '{call_to_action}',
                            'style' => array(
                                'typography' => array('fontSize' => '1.25rem'),
                                'color' => array('text' => '#ffffff'),
                                'spacing' => array('margin' => array('bottom' => '30px'))
                            )
                        )
                    ),
                    array(
                        'blockName' => 'greenshift-blocks/button',
                        'attrs' => array(
                            'text' => 'Get In Touch',
                            'url' => '#contact',
                            'style' => array(
                                'color' => array('background' => '#ffffff', 'text' => '{primary_color}'),
                                'border' => array('radius' => '50px'),
                                'spacing' => array('padding' => array('top' => '15px', 'bottom' => '15px', 'left' => '30px', 'right' => '30px'))
                            )
                        )
                    )
                )
            )
        );
    }
    
    private function get_pricing_blocks() {
        return array(
            // Hero Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'pricing-hero',
                    'padding' => '80px 20px',
                    'textAlign' => 'center',
                    'background' => 'linear-gradient(135deg, {primary_color}, {secondary_color})'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 1,
                            'content' => '{pricing_headline}',
                            'style' => array(
                                'typography' => array('fontSize' => '3rem', 'fontWeight' => '700'),
                                'color' => array('text' => '#ffffff'),
                                'spacing' => array('margin' => array('bottom' => '20px'))
                            )
                        )
                    ),
                    array(
                        'blockName' => 'core/paragraph',
                        'attrs' => array(
                            'content' => '{pricing_subheading}',
                            'style' => array(
                                'typography' => array('fontSize' => '1.25rem'),
                                'color' => array('text' => 'rgba(255,255,255,0.9)')
                            )
                        )
                    )
                )
            ),
            
            // Pricing Plans Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'pricing-plans',
                    'padding' => '100px 20px'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('gap' => '30px'),
                        'innerBlocks' => array(
                            // Basic Plan
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '33.33%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/container',
                                        'attrs' => array(
                                            'padding' => '40px 30px',
                                            'background' => '#ffffff',
                                            'border' => array('width' => '1px', 'color' => '#e2e8f0', 'radius' => '15px'),
                                            'boxShadow' => '0 4px 20px rgba(0,0,0,0.1)',
                                            'textAlign' => 'center'
                                        ),
                                        'innerBlocks' => array(
                                            array(
                                                'blockName' => 'core/paragraph',
                                                'attrs' => array(
                                                    'content' => '{basic_plan}',
                                                    'style' => array('typography' => array('fontSize' => '1.125rem'))
                                                )
                                            )
                                        )
                                    )
                                )
                            ),
                            // Standard Plan
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '33.33%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/container',
                                        'attrs' => array(
                                            'padding' => '40px 30px',
                                            'background' => '{primary_color}',
                                            'border' => array('radius' => '15px'),
                                            'boxShadow' => '0 8px 30px rgba(0,0,0,0.15)',
                                            'textAlign' => 'center',
                                            'style' => array('color' => array('text' => '#ffffff'))
                                        ),
                                        'innerBlocks' => array(
                                            array(
                                                'blockName' => 'core/paragraph',
                                                'attrs' => array(
                                                    'content' => '<strong>MOST POPULAR</strong>',
                                                    'style' => array(
                                                        'typography' => array('fontSize' => '0.875rem', 'fontWeight' => '600'),
                                                        'spacing' => array('margin' => array('bottom' => '20px'))
                                                    )
                                                )
                                            ),
                                            array(
                                                'blockName' => 'core/paragraph',
                                                'attrs' => array(
                                                    'content' => '{standard_plan}',
                                                    'style' => array('typography' => array('fontSize' => '1.125rem'))
                                                )
                                            )
                                        )
                                    )
                                )
                            ),
                            // Premium Plan
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '33.33%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'greenshift-blocks/container',
                                        'attrs' => array(
                                            'padding' => '40px 30px',
                                            'background' => '#ffffff',
                                            'border' => array('width' => '1px', 'color' => '#e2e8f0', 'radius' => '15px'),
                                            'boxShadow' => '0 4px 20px rgba(0,0,0,0.1)',
                                            'textAlign' => 'center'
                                        ),
                                        'innerBlocks' => array(
                                            array(
                                                'blockName' => 'core/paragraph',
                                                'attrs' => array(
                                                    'content' => '{premium_plan}',
                                                    'style' => array('typography' => array('fontSize' => '1.125rem'))
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // FAQ Section
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'pricing-faq',
                    'padding' => '80px 20px',
                    'background' => '#f8fafc'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/heading',
                        'attrs' => array(
                            'level' => 2,
                            'content' => 'Frequently Asked Questions',
                            'textAlign' => 'center',
                            'style' => array(
                                'typography' => array('fontSize' => '2.5rem', 'fontWeight' => '600'),
                                'spacing' => array('margin' => array('bottom' => '50px'))
                            )
                        )
                    ),
                    array(
                        'blockName' => 'greenshift-blocks/row',
                        'attrs' => array('justify' => 'center'),
                        'innerBlocks' => array(
                            array(
                                'blockName' => 'greenshift-blocks/column',
                                'attrs' => array('width' => '80%'),
                                'innerBlocks' => array(
                                    array(
                                        'blockName' => 'core/paragraph',
                                        'attrs' => array(
                                            'content' => '{pricing_faq}',
                                            'style' => array(
                                                'typography' => array('fontSize' => '1.125rem', 'lineHeight' => '1.8')
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            
            // Contact CTA
            array(
                'blockName' => 'greenshift-blocks/container',
                'attrs' => array(
                    'id' => 'pricing-cta',
                    'padding' => '80px 20px',
                    'textAlign' => 'center',
                    'background' => '{primary_color}'
                ),
                'innerBlocks' => array(
                    array(
                        'blockName' => 'core/paragraph',
                        'attrs' => array(
                            'content' => '{contact_cta}',
                            'style' => array(
                                'typography' => array('fontSize' => '1.25rem'),
                                'color' => array('text' => '#ffffff'),
                                'spacing' => array('margin' => array('bottom' => '30px'))
                            )
                        )
                    ),
                    array(
                        'blockName' => 'greenshift-blocks/button',
                        'attrs' => array(
                            'text' => 'Get Custom Quote',
                            'url' => '#contact',
                            'style' => array(
                                'color' => array('background' => '#ffffff', 'text' => '{primary_color}'),
                                'border' => array('radius' => '50px'),
                                'spacing' => array('padding' => array('top' => '15px', 'bottom' => '15px', 'left' => '30px', 'right' => '30px'))
                            )
                        )
                    )
                )
            )
        );
    }
}