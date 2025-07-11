<?php
/**
 * SEO Optimizer Class
 * Handles SEO optimization for generated pages
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSBA_SEO_Optimizer {
    
    public function optimize_page($page_id, $business_data, $ai_content) {
        // Get template for SEO config
        $template_id = get_post_meta($page_id, '_gsba_template_id', true);
        $db = new GSBA_Database();
        $template = $db->get_template($template_id);
        
        if (!$template) {
            return false;
        }
        
        $seo_config = json_decode($template->seo_config, true);
        
        // Generate SEO data
        $seo_data = $this->generate_seo_data($seo_config, $business_data, $ai_content);
        
        // Apply SEO optimizations
        $this->set_meta_title($page_id, $seo_data['meta_title']);
        $this->set_meta_description($page_id, $seo_data['meta_description']);
        $this->set_meta_keywords($page_id, $seo_data['keywords']);
        $this->set_open_graph_data($page_id, $seo_data);
        $this->set_schema_markup($page_id, $business_data, $seo_data);
        
        // Save SEO data to post meta
        update_post_meta($page_id, '_gsba_seo_data', wp_json_encode($seo_data));
        
        return true;
    }
    
    private function generate_seo_data($seo_config, $business_data, $ai_content) {
        $seo_data = array();
        
        // Combine all data for placeholder replacement
        $all_data = array_merge($business_data, $ai_content);
        
        foreach ($seo_config as $key => $template) {
            $seo_data[$key] = $this->replace_seo_placeholders($template, $all_data, $business_data);
        }
        
        // Ensure proper lengths
        $seo_data['meta_title'] = $this->optimize_title_length($seo_data['meta_title']);
        $seo_data['meta_description'] = $this->optimize_description_length($seo_data['meta_description']);
        
        return $seo_data;
    }
    
    private function replace_seo_placeholders($template, $all_data, $business_data) {
        // Handle special placeholders
        $template = str_replace('{business_location}', $this->extract_location($business_data['address'] ?? ''), $template);
        $template = str_replace('{current_year}', date('Y'), $template);
        
        // Replace standard placeholders
        foreach ($all_data as $key => $value) {
            if (is_string($value)) {
                $template = str_replace('{' . $key . '}', $value, $template);
            }
        }
        
        // Clean up remaining placeholders
        $template = preg_replace('/\{[^}]+\}/', '', $template);
        
        return trim($template);
    }
    
    private function optimize_title_length($title, $max_length = 60) {
        if (strlen($title) <= $max_length) {
            return $title;
        }
        
        // Try to truncate at word boundary
        $truncated = substr($title, 0, $max_length);
        $last_space = strrpos($truncated, ' ');
        
        if ($last_space !== false && $last_space > $max_length * 0.8) {
            return substr($title, 0, $last_space);
        }
        
        return substr($title, 0, $max_length - 3) . '...';
    }
    
    private function optimize_description_length($description, $max_length = 160) {
        if (strlen($description) <= $max_length) {
            return $description;
        }
        
        // Try to truncate at sentence boundary
        $sentences = preg_split('/(?<=[.!?])\s+/', $description);
        $result = '';
        
        foreach ($sentences as $sentence) {
            if (strlen($result . $sentence) <= $max_length) {
                $result .= ($result ? ' ' : '') . $sentence;
            } else {
                break;
            }
        }
        
        if (empty($result)) {
            // Fallback to word boundary
            $truncated = substr($description, 0, $max_length);
            $last_space = strrpos($truncated, ' ');
            
            if ($last_space !== false && $last_space > $max_length * 0.8) {
                $result = substr($description, 0, $last_space) . '...';
            } else {
                $result = substr($description, 0, $max_length - 3) . '...';
            }
        }
        
        return $result;
    }
    
    private function set_meta_title($page_id, $title) {
        // Support for popular SEO plugins
        if (function_exists('YoastSEO')) {
            update_post_meta($page_id, '_yoast_wpseo_title', $title);
        } elseif (class_exists('RankMath')) {
            update_post_meta($page_id, 'rank_math_title', $title);
        } elseif (class_exists('AIOSEO')) {
            update_post_meta($page_id, '_aioseo_title', $title);
        }
        
        // Fallback: custom meta
        update_post_meta($page_id, '_gsba_meta_title', $title);
    }
    
    private function set_meta_description($page_id, $description) {
        // Support for popular SEO plugins
        if (function_exists('YoastSEO')) {
            update_post_meta($page_id, '_yoast_wpseo_metadesc', $description);
        } elseif (class_exists('RankMath')) {
            update_post_meta($page_id, 'rank_math_description', $description);
        } elseif (class_exists('AIOSEO')) {
            update_post_meta($page_id, '_aioseo_description', $description);
        }
        
        // Fallback: custom meta
        update_post_meta($page_id, '_gsba_meta_description', $description);
    }
    
    private function set_meta_keywords($page_id, $keywords) {
        // Some SEO plugins still use keywords
        if (class_exists('RankMath')) {
            update_post_meta($page_id, 'rank_math_focus_keyword', $keywords);
        }
        
        // Custom meta
        update_post_meta($page_id, '_gsba_meta_keywords', $keywords);
    }
    
    private function set_open_graph_data($page_id, $seo_data) {
        $og_data = array(
            'og_title' => $seo_data['og_title'] ?? $seo_data['meta_title'],
            'og_description' => $seo_data['og_description'] ?? $seo_data['meta_description'],
            'og_image' => $seo_data['og_image'] ?? '',
            'og_type' => 'website'
        );
        
        // Support for popular SEO plugins
        if (function_exists('YoastSEO')) {
            update_post_meta($page_id, '_yoast_wpseo_opengraph-title', $og_data['og_title']);
            update_post_meta($page_id, '_yoast_wpseo_opengraph-description', $og_data['og_description']);
            if ($og_data['og_image']) {
                update_post_meta($page_id, '_yoast_wpseo_opengraph-image', $og_data['og_image']);
            }
        } elseif (class_exists('RankMath')) {
            update_post_meta($page_id, 'rank_math_facebook_title', $og_data['og_title']);
            update_post_meta($page_id, 'rank_math_facebook_description', $og_data['og_description']);
            if ($og_data['og_image']) {
                update_post_meta($page_id, 'rank_math_facebook_image', $og_data['og_image']);
            }
        }
        
        // Custom meta
        foreach ($og_data as $key => $value) {
            update_post_meta($page_id, '_gsba_' . $key, $value);
        }
    }
    
    private function set_schema_markup($page_id, $business_data, $seo_data) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $this->get_business_schema_type($business_data['business_type']),
            'name' => $business_data['business_name'],
            'description' => $business_data['description'],
            'url' => get_permalink($page_id)
        );
        
        // Add logo
        if (!empty($business_data['logo_url'])) {
            $schema['logo'] = $business_data['logo_url'];
            $schema['image'] = $business_data['logo_url'];
        }
        
        // Add contact information
        if (!empty($business_data['phone']) || !empty($business_data['email'])) {
            $schema['contactPoint'] = array(
                '@type' => 'ContactPoint'
            );
            
            if (!empty($business_data['phone'])) {
                $schema['contactPoint']['telephone'] = $business_data['phone'];
            }
            
            if (!empty($business_data['email'])) {
                $schema['contactPoint']['email'] = $business_data['email'];
            }
        }
        
        // Add address
        if (!empty($business_data['address'])) {
            $schema['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $business_data['address']
            );
        }
        
        // Add social media
        if (!empty($business_data['social_media'])) {
            $social_urls = array_filter($business_data['social_media']);
            if (!empty($social_urls)) {
                $schema['sameAs'] = array_values($social_urls);
            }
        }
        
        // Save schema markup
        update_post_meta($page_id, '_gsba_schema_markup', wp_json_encode($schema));
        
        // Add to head via action
        add_action('wp_head', function() use ($schema, $page_id) {
            if (is_page($page_id)) {
                echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
            }
        });
    }
    
    private function get_business_schema_type($business_type) {
        $schema_types = array(
            'restaurant' => 'Restaurant',
            'retail_store' => 'Store',
            'healthcare' => 'MedicalOrganization',
            'law_firm' => 'LegalService',
            'real_estate' => 'RealEstateAgent',
            'automotive' => 'AutomotiveBusiness',
            'beauty' => 'BeautySalon',
            'fitness' => 'SportsActivityLocation',
            'education' => 'EducationalOrganization',
            'financial' => 'FinancialService',
            'construction' => 'GeneralContractor',
            'travel' => 'TravelAgency'
        );
        
        return $schema_types[$business_type] ?? 'LocalBusiness';
    }
    
    private function extract_location($address) {
        if (empty($address)) {
            return '';
        }
        
        // Simple location extraction
        $parts = array_map('trim', explode(',', $address));
        
        // Return last part (usually city/state)
        return end($parts);
    }
    
    public function add_meta_tags_to_head() {
        add_action('wp_head', array($this, 'output_custom_meta_tags'));
    }
    
    public function output_custom_meta_tags() {
        if (!is_singular('page')) {
            return;
        }
        
        global $post;
        
        // Check if this is a generated page
        if (!get_post_meta($post->ID, '_gsba_generated', true)) {
            return;
        }
        
        // Only output if no SEO plugin is handling it
        if (function_exists('YoastSEO') || class_exists('RankMath') || class_exists('AIOSEO')) {
            return;
        }
        
        // Output custom meta tags
        $title = get_post_meta($post->ID, '_gsba_meta_title', true);
        $description = get_post_meta($post->ID, '_gsba_meta_description', true);
        $keywords = get_post_meta($post->ID, '_gsba_meta_keywords', true);
        
        if ($title) {
            echo '<meta name="title" content="' . esc_attr($title) . '">' . "\n";
        }
        
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
        
        if ($keywords) {
            echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
        }
        
        // Output Open Graph tags
        $og_title = get_post_meta($post->ID, '_gsba_og_title', true);
        $og_description = get_post_meta($post->ID, '_gsba_og_description', true);
        $og_image = get_post_meta($post->ID, '_gsba_og_image', true);
        
        if ($og_title) {
            echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
        }
        
        if ($og_description) {
            echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        }
        
        if ($og_image) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        }
        
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink($post->ID)) . '">' . "\n";
        
        // Output Schema markup
        $schema = get_post_meta($post->ID, '_gsba_schema_markup', true);
        if ($schema) {
            echo '<script type="application/ld+json">' . $schema . '</script>' . "\n";
        }
    }
    
    public function get_seo_analysis($page_id) {
        $seo_data = json_decode(get_post_meta($page_id, '_gsba_seo_data', true), true);
        
        if (!$seo_data) {
            return array(
                'score' => 0,
                'issues' => array('No SEO data found')
            );
        }
        
        $issues = array();
        $score = 100;
        
        // Check title length
        if (strlen($seo_data['meta_title']) > 60) {
            $issues[] = 'Title is too long (over 60 characters)';
            $score -= 10;
        } elseif (strlen($seo_data['meta_title']) < 30) {
            $issues[] = 'Title is too short (under 30 characters)';
            $score -= 5;
        }
        
        // Check description length
        if (strlen($seo_data['meta_description']) > 160) {
            $issues[] = 'Meta description is too long (over 160 characters)';
            $score -= 10;
        } elseif (strlen($seo_data['meta_description']) < 120) {
            $issues[] = 'Meta description is too short (under 120 characters)';
            $score -= 5;
        }
        
        // Check for keywords
        if (empty($seo_data['keywords'])) {
            $issues[] = 'No focus keywords set';
            $score -= 5;
        }
        
        // Check Open Graph
        if (empty($seo_data['og_image'])) {
            $issues[] = 'No Open Graph image set';
            $score -= 5;
        }
        
        return array(
            'score' => max(0, $score),
            'issues' => $issues,
            'data' => $seo_data
        );
    }
}