<?php
/**
 * Page Generator Class
 * Creates WordPress pages from templates and AI content
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSBA_Page_Generator {
    
    public function create_business_page($business_data, $ai_content, $template_id) {
        try {
            // Get template
            $db = new GSBA_Database();
            $template = $db->get_template($template_id);
            
            if (!$template) {
                return array(
                    'success' => false,
                    'message' => __('Template not found.', 'greenshift-business-ai')
                );
            }
            
            // Parse template structure
            $block_structure = json_decode($template->block_structure, true);
            
            // Replace placeholders with AI content
            $processed_blocks = $this->process_blocks($block_structure, $ai_content, $business_data);
            
            // Create WordPress page
            $page_data = array(
                'post_title' => $this->generate_page_title($business_data, $template, $ai_content),
                'post_content' => serialize_blocks($processed_blocks),
                'post_status' => get_option('gsba_settings')['page_status'] ?? 'draft',
                'post_type' => 'page',
                'post_author' => get_current_user_id(),
                'meta_input' => array(
                    '_gsba_generated' => true,
                    '_gsba_template_id' => $template_id,
                    '_gsba_business_data' => wp_json_encode($business_data),
                    '_gsba_ai_content' => wp_json_encode($ai_content)
                )
            );
            
            $page_id = wp_insert_post($page_data);
            
            if (is_wp_error($page_id)) {
                return array(
                    'success' => false,
                    'message' => __('Failed to create page: ', 'greenshift-business-ai') . $page_id->get_error_message()
                );
            }
            
            // Set featured image if logo exists
            if (!empty($business_data['logo_url'])) {
                $this->set_featured_image_from_url($page_id, $business_data['logo_url']);
            }
            
            return array(
                'success' => true,
                'page_id' => $page_id,
                'message' => __('Page created successfully!', 'greenshift-business-ai')
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => __('Error creating page: ', 'greenshift-business-ai') . $e->getMessage()
            );
        }
    }
    
    private function process_blocks($blocks, $ai_content, $business_data) {
        $processed_blocks = array();
        
        foreach ($blocks as $block) {
            $processed_block = $this->process_single_block($block, $ai_content, $business_data);
            $processed_blocks[] = $processed_block;
        }
        
        return $processed_blocks;
    }
    
    private function process_single_block($block, $ai_content, $business_data) {
        // Process attributes
        if (isset($block['attrs'])) {
            $block['attrs'] = $this->replace_placeholders($block['attrs'], $ai_content, $business_data);
        }
        
        // Process inner blocks recursively
        if (isset($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            $block['innerBlocks'] = $this->process_blocks($block['innerBlocks'], $ai_content, $business_data);
        }
        
        return $block;
    }
    
    private function replace_placeholders($data, $ai_content, $business_data) {
        if (is_string($data)) {
            return $this->replace_string_placeholders($data, $ai_content, $business_data);
        } elseif (is_array($data)) {
            $processed = array();
            foreach ($data as $key => $value) {
                $processed[$key] = $this->replace_placeholders($value, $ai_content, $business_data);
            }
            return $processed;
        }
        
        return $data;
    }
    
    private function replace_string_placeholders($string, $ai_content, $business_data) {
        // Combine all data sources
        $all_data = array_merge($business_data, $ai_content);
        
        // Handle social media data
        if (isset($business_data['social_media']) && is_array($business_data['social_media'])) {
            foreach ($business_data['social_media'] as $platform => $url) {
                $all_data['social_' . $platform] = $url;
            }
        }
        
        // Handle feature data
        if (isset($ai_content['feature_1']) && is_array($ai_content['feature_1'])) {
            $all_data['feature_1_title'] = $ai_content['feature_1']['title'];
            $all_data['feature_1_description'] = $ai_content['feature_1']['description'];
        }
        if (isset($ai_content['feature_2']) && is_array($ai_content['feature_2'])) {
            $all_data['feature_2_title'] = $ai_content['feature_2']['title'];
            $all_data['feature_2_description'] = $ai_content['feature_2']['description'];
        }
        if (isset($ai_content['feature_3']) && is_array($ai_content['feature_3'])) {
            $all_data['feature_3_title'] = $ai_content['feature_3']['title'];
            $all_data['feature_3_description'] = $ai_content['feature_3']['description'];
        }
        
        // Replace placeholders
        foreach ($all_data as $key => $value) {
            if (is_string($value)) {
                $string = str_replace('{' . $key . '}', $value, $string);
            }
        }
        
        // Clean up any remaining placeholders
        $string = preg_replace('/\{[^}]+\}/', '', $string);
        
        return $string;
    }
    
    private function generate_page_title($business_data, $template, $ai_content) {
        $base_title = $business_data['business_name'];
        
        switch ($template->type) {
            case 'landing':
                return $base_title;
            case 'about':
                return 'About ' . $base_title;
            case 'pricing':
                return $base_title . ' - Pricing';
            default:
                return $base_title . ' - ' . ucfirst($template->type);
        }
    }
    
    private function set_featured_image_from_url($post_id, $image_url) {
        if (empty($image_url)) {
            return false;
        }
        
        try {
            // Download image
            $temp_file = download_url($image_url);
            
            if (is_wp_error($temp_file)) {
                return false;
            }
            
            // Prepare file array
            $file_array = array(
                'name' => basename($image_url),
                'tmp_name' => $temp_file
            );
            
            // Upload to media library
            $attachment_id = media_handle_sideload($file_array, $post_id);
            
            // Remove temp file
            @unlink($temp_file);
            
            if (is_wp_error($attachment_id)) {
                return false;
            }
            
            // Set as featured image
            return set_post_thumbnail($post_id, $attachment_id);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function update_page_content($page_id, $ai_content, $template_id) {
        // Get existing business data
        $business_data = json_decode(get_post_meta($page_id, '_gsba_business_data', true), true);
        
        if (!$business_data) {
            return array(
                'success' => false,
                'message' => __('Business data not found for this page.', 'greenshift-business-ai')
            );
        }
        
        // Get template
        $db = new GSBA_Database();
        $template = $db->get_template($template_id);
        
        if (!$template) {
            return array(
                'success' => false,
                'message' => __('Template not found.', 'greenshift-business-ai')
            );
        }
        
        // Process blocks with new content
        $block_structure = json_decode($template->block_structure, true);
        $processed_blocks = $this->process_blocks($block_structure, $ai_content, $business_data);
        
        // Update page
        $update_data = array(
            'ID' => $page_id,
            'post_content' => serialize_blocks($processed_blocks)
        );
        
        $result = wp_update_post($update_data);
        
        if (is_wp_error($result)) {
            return array(
                'success' => false,
                'message' => __('Failed to update page: ', 'greenshift-business-ai') . $result->get_error_message()
            );
        }
        
        // Update meta
        update_post_meta($page_id, '_gsba_ai_content', wp_json_encode($ai_content));
        
        return array(
            'success' => true,
            'message' => __('Page updated successfully!', 'greenshift-business-ai')
        );
    }
    
    public function duplicate_page($original_page_id, $new_business_data) {
        // Get original page data
        $original_post = get_post($original_page_id);
        $original_template_id = get_post_meta($original_page_id, '_gsba_template_id', true);
        $original_ai_content = json_decode(get_post_meta($original_page_id, '_gsba_ai_content', true), true);
        
        if (!$original_post || !$original_template_id || !$original_ai_content) {
            return array(
                'success' => false,
                'message' => __('Invalid source page for duplication.', 'greenshift-business-ai')
            );
        }
        
        // Create new page with updated business data
        return $this->create_business_page($new_business_data, $original_ai_content, $original_template_id);
    }
    
    public function get_page_preview_data($page_id) {
        $business_data = json_decode(get_post_meta($page_id, '_gsba_business_data', true), true);
        $ai_content = json_decode(get_post_meta($page_id, '_gsba_ai_content', true), true);
        $template_id = get_post_meta($page_id, '_gsba_template_id', true);
        
        $db = new GSBA_Database();
        $template = $db->get_template($template_id);
        
        return array(
            'business_data' => $business_data,
            'ai_content' => $ai_content,
            'template' => $template,
            'post' => get_post($page_id)
        );
    }
    
    public function export_page_data($page_id) {
        $preview_data = $this->get_page_preview_data($page_id);
        
        if (!$preview_data['business_data']) {
            return false;
        }
        
        return array(
            'export_date' => current_time('mysql'),
            'page_title' => $preview_data['post']->post_title,
            'page_url' => get_permalink($page_id),
            'business_data' => $preview_data['business_data'],
            'ai_content' => $preview_data['ai_content'],
            'template' => array(
                'name' => $preview_data['template']->name,
                'type' => $preview_data['template']->type
            )
        );
    }
}