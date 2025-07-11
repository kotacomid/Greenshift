<?php
/**
 * AI Generator Class
 * Generates business content using various AI models
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSBA_AI_Generator {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('gsba_settings', array());
    }
    
    public function generate_business_content($business_data, $template_id) {
        // Get template
        $db = new GSBA_Database();
        $template = $db->get_template($template_id);
        
        if (!$template) {
            return array(
                'success' => false,
                'message' => __('Template not found.', 'greenshift-business-ai')
            );
        }
        
        // Generate content based on template type
        switch ($template->type) {
            case 'landing':
                return $this->generate_landing_content($business_data, $template);
            case 'about':
                return $this->generate_about_content($business_data, $template);
            case 'pricing':
                return $this->generate_pricing_content($business_data, $template);
            default:
                return $this->generate_generic_content($business_data, $template);
        }
    }
    
    private function generate_landing_content($business_data, $template) {
        $ai_model = $this->settings['default_ai_model'] ?? 'openai';
        
        $prompts = array(
            'hero_headline' => $this->build_prompt(
                "Create a powerful, attention-grabbing headline for {business_name}, a {business_type} business. Make it compelling, benefit-focused, and under 10 words.",
                $business_data
            ),
            'hero_subheading' => $this->build_prompt(
                "Write a compelling 2-sentence subheading that explains what {business_name} does and the main benefit for customers. Business: {description}",
                $business_data
            ),
            'hero_cta_text' => $this->build_prompt(
                "Create a strong call-to-action button text for {business_name} ({business_type}). Make it action-oriented and under 4 words.",
                $business_data
            ),
            'features_heading' => $this->build_prompt(
                "Create a heading for the features section of {business_name} website. Make it benefit-focused and engaging.",
                $business_data
            ),
            'feature_1' => $this->build_prompt(
                "Create the FIRST key feature/benefit of {business_name} ({business_type}). Include a short title (3-4 words) and 2-sentence description. Focus on the main unique value. Business: {description}",
                $business_data
            ),
            'feature_2' => $this->build_prompt(
                "Create the SECOND key feature/benefit of {business_name} ({business_type}). Include a short title (3-4 words) and 2-sentence description. Focus on quality or service excellence. Business: {description}",
                $business_data
            ),
            'feature_3' => $this->build_prompt(
                "Create the THIRD key feature/benefit of {business_name} ({business_type}). Include a short title (3-4 words) and 2-sentence description. Focus on customer satisfaction or results. Business: {description}",
                $business_data
            ),
            'about_heading' => $this->build_prompt(
                "Create a heading for the about section of {business_name}. Make it personal and trustworthy.",
                $business_data
            ),
            'about_description' => $this->build_prompt(
                "Write a 3-sentence about section for {business_name} ({business_type}). Include their story, what makes them special, and their commitment to customers. Business: {description}",
                $business_data
            ),
            'contact_heading' => $this->build_prompt(
                "Create a heading for the contact section of {business_name}. Make it inviting and action-oriented.",
                $business_data
            ),
            'contact_info' => $this->build_prompt(
                "Write 2 sentences encouraging people to contact {business_name}. Include what they can expect when they get in touch.",
                $business_data
            )
        );
        
        $generated_content = array();
        
        foreach ($prompts as $key => $prompt) {
            $result = $this->generate_with_ai($prompt, $ai_model);
            if (!$result['success']) {
                return $result;
            }
            
            // Process specific content types
            if (in_array($key, ['feature_1', 'feature_2', 'feature_3'])) {
                $generated_content[$key] = $this->parse_feature_content($result['data']['content']);
            } else {
                $generated_content[$key] = $result['data']['content'];
            }
        }
        
        // Add business-specific data
        $generated_content['business_name'] = $business_data['business_name'];
        $generated_content['logo_url'] = $business_data['logo_url'];
        $generated_content['primary_color'] = $business_data['primary_color'] ?: '#667eea';
        $generated_content['secondary_color'] = $business_data['secondary_color'] ?: '#764ba2';
        $generated_content['phone'] = $business_data['phone'];
        $generated_content['email'] = $business_data['email'];
        
        // Generate placeholder images
        $generated_content['hero_image_url'] = $this->get_placeholder_image('hero', $business_data['business_type']);
        $generated_content['about_image_url'] = $this->get_placeholder_image('about', $business_data['business_type']);
        
        // Generate feature icons
        $generated_content['feature_1_icon'] = $this->get_feature_icon(1, $business_data['business_type']);
        $generated_content['feature_2_icon'] = $this->get_feature_icon(2, $business_data['business_type']);
        $generated_content['feature_3_icon'] = $this->get_feature_icon(3, $business_data['business_type']);
        
        return array(
            'success' => true,
            'data' => $generated_content
        );
    }
    
    private function generate_about_content($business_data, $template) {
        $ai_model = $this->settings['default_ai_model'] ?? 'openai';
        
        $prompts = array(
            'page_title' => $this->build_prompt(
                "Create a page title for the About Us page of {business_name}. Make it engaging and professional.",
                $business_data
            ),
            'company_story' => $this->build_prompt(
                "Write a compelling 4-sentence company story for {business_name} ({business_type}). Include how they started, their mission, and what drives them. Business: {description}",
                $business_data
            ),
            'mission_statement' => $this->build_prompt(
                "Write a clear and inspiring mission statement for {business_name} ({business_type}). Keep it 1-2 sentences. Business: {description}",
                $business_data
            ),
            'vision_statement' => $this->build_prompt(
                "Write a forward-looking vision statement for {business_name} ({business_type}). Keep it 1-2 sentences about their future goals. Business: {description}",
                $business_data
            ),
            'core_values' => $this->build_prompt(
                "List 3 core values for {business_name} ({business_type}). For each value, provide a title (1-2 words) and a 1-sentence description. Business: {description}",
                $business_data
            ),
            'team_intro' => $this->build_prompt(
                "Write a 2-sentence introduction about the team at {business_name}. Focus on their expertise and commitment to customers.",
                $business_data
            ),
            'call_to_action' => $this->build_prompt(
                "Write a compelling call-to-action for the end of the About Us page of {business_name}. Encourage visitors to get in touch or learn more about their services.",
                $business_data
            )
        );
        
        $generated_content = array();
        
        foreach ($prompts as $key => $prompt) {
            $result = $this->generate_with_ai($prompt, $ai_model);
            if (!$result['success']) {
                return $result;
            }
            $generated_content[$key] = $result['data']['content'];
        }
        
        // Add business data
        $generated_content['business_name'] = $business_data['business_name'];
        $generated_content['logo_url'] = $business_data['logo_url'];
        $generated_content['primary_color'] = $business_data['primary_color'] ?: '#667eea';
        $generated_content['secondary_color'] = $business_data['secondary_color'] ?: '#764ba2';
        
        return array(
            'success' => true,
            'data' => $generated_content
        );
    }
    
    private function generate_pricing_content($business_data, $template) {
        $ai_model = $this->settings['default_ai_model'] ?? 'openai';
        
        $prompts = array(
            'page_title' => $this->build_prompt(
                "Create a page title for the pricing page of {business_name}. Make it clear and compelling.",
                $business_data
            ),
            'pricing_headline' => $this->build_prompt(
                "Create a compelling headline for the pricing section of {business_name} ({business_type}). Focus on value and transparency.",
                $business_data
            ),
            'pricing_subheading' => $this->build_prompt(
                "Write a 2-sentence subheading explaining the pricing approach of {business_name}. Emphasize value and customer benefits.",
                $business_data
            ),
            'basic_plan' => $this->build_prompt(
                "Create a BASIC service package for {business_name} ({business_type}). Include: package name, 3 key features, and suggested pricing range. Business: {description}",
                $business_data
            ),
            'standard_plan' => $this->build_prompt(
                "Create a STANDARD service package for {business_name} ({business_type}). Include: package name, 4 key features, and suggested pricing range. This should be the most popular option. Business: {description}",
                $business_data
            ),
            'premium_plan' => $this->build_prompt(
                "Create a PREMIUM service package for {business_name} ({business_type}). Include: package name, 5 key features, and suggested pricing range. Focus on comprehensive service. Business: {description}",
                $business_data
            ),
            'pricing_faq' => $this->build_prompt(
                "Create 3 frequently asked questions about pricing for {business_name} ({business_type}). Include questions and helpful answers about payment, refunds, or service details.",
                $business_data
            ),
            'contact_cta' => $this->build_prompt(
                "Write a compelling call-to-action encouraging people to contact {business_name} for a custom quote or consultation.",
                $business_data
            )
        );
        
        $generated_content = array();
        
        foreach ($prompts as $key => $prompt) {
            $result = $this->generate_with_ai($prompt, $ai_model);
            if (!$result['success']) {
                return $result;
            }
            $generated_content[$key] = $result['data']['content'];
        }
        
        // Add business data
        $generated_content['business_name'] = $business_data['business_name'];
        $generated_content['logo_url'] = $business_data['logo_url'];
        $generated_content['primary_color'] = $business_data['primary_color'] ?: '#667eea';
        $generated_content['secondary_color'] = $business_data['secondary_color'] ?: '#764ba2';
        
        return array(
            'success' => true,
            'data' => $generated_content
        );
    }
    
    private function generate_generic_content($business_data, $template) {
        $ai_model = $this->settings['default_ai_model'] ?? 'openai';
        
        $prompt = $this->build_prompt(
            "Create comprehensive website content for {business_name}, a {business_type} business. Include: headline, description, key features, and call-to-action. Business: {description}",
            $business_data
        );
        
        $result = $this->generate_with_ai($prompt, $ai_model);
        if (!$result['success']) {
            return $result;
        }
        
        return array(
            'success' => true,
            'data' => array(
                'content' => $result['data']['content'],
                'business_name' => $business_data['business_name'],
                'logo_url' => $business_data['logo_url'],
                'primary_color' => $business_data['primary_color'] ?: '#667eea',
                'secondary_color' => $business_data['secondary_color'] ?: '#764ba2'
            )
        );
    }
    
    private function build_prompt($prompt_template, $business_data) {
        $replacements = array(
            '{business_name}' => $business_data['business_name'],
            '{business_type}' => $business_data['business_type'],
            '{description}' => $business_data['description'],
            '{tagline}' => $business_data['tagline'] ?? '',
            '{location}' => $this->extract_location($business_data['address'] ?? ''),
        );
        
        $prompt = str_replace(array_keys($replacements), array_values($replacements), $prompt_template);
        
        // Add context and formatting instructions
        $prompt .= "\n\nIMPORTANT: Write in professional, engaging tone. Use proper HTML formatting if needed. Be specific to the business type and avoid generic phrases.";
        
        return $prompt;
    }
    
    private function generate_with_ai($prompt, $ai_model) {
        switch ($ai_model) {
            case 'openai':
                return $this->generate_with_openai($prompt);
            case 'claude':
                return $this->generate_with_claude($prompt);
            case 'gemini':
                return $this->generate_with_gemini($prompt);
            default:
                return array(
                    'success' => false,
                    'message' => __('Invalid AI model selected.', 'greenshift-business-ai')
                );
        }
    }
    
    private function generate_with_openai($prompt) {
        $api_key = $this->settings['openai_api_key'] ?? '';
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('OpenAI API key not configured.', 'greenshift-business-ai')
            );
        }
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'timeout' => 60,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key
            ),
            'body' => wp_json_encode(array(
                'model' => 'gpt-3.5-turbo',
                'messages' => array(
                    array('role' => 'user', 'content' => $prompt)
                ),
                'max_tokens' => intval($this->settings['max_tokens'] ?? 1000),
                'temperature' => floatval($this->settings['temperature'] ?? 0.7)
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return array(
                'success' => true,
                'data' => array(
                    'content' => trim($data['choices'][0]['message']['content']),
                    'tokens_used' => $data['usage']['total_tokens'] ?? 0
                )
            );
        }
        
        return array(
            'success' => false,
            'message' => __('Invalid response from OpenAI.', 'greenshift-business-ai')
        );
    }
    
    private function generate_with_claude($prompt) {
        $api_key = $this->settings['claude_api_key'] ?? '';
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('Claude API key not configured.', 'greenshift-business-ai')
            );
        }
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'timeout' => 60,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01'
            ),
            'body' => wp_json_encode(array(
                'model' => 'claude-3-sonnet-20240229',
                'max_tokens' => intval($this->settings['max_tokens'] ?? 1000),
                'messages' => array(
                    array('role' => 'user', 'content' => $prompt)
                )
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['content'][0]['text'])) {
            return array(
                'success' => true,
                'data' => array(
                    'content' => trim($data['content'][0]['text']),
                    'tokens_used' => $data['usage']['output_tokens'] ?? 0
                )
            );
        }
        
        return array(
            'success' => false,
            'message' => __('Invalid response from Claude.', 'greenshift-business-ai')
        );
    }
    
    private function generate_with_gemini($prompt) {
        $api_key = $this->settings['gemini_api_key'] ?? '';
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('Gemini API key not configured.', 'greenshift-business-ai')
            );
        }
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $api_key;
        
        $response = wp_remote_post($url, array(
            'timeout' => 60,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode(array(
                'contents' => array(
                    array('parts' => array(array('text' => $prompt)))
                ),
                'generationConfig' => array(
                    'maxOutputTokens' => intval($this->settings['max_tokens'] ?? 1000),
                    'temperature' => floatval($this->settings['temperature'] ?? 0.7)
                )
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return array(
                'success' => true,
                'data' => array(
                    'content' => trim($data['candidates'][0]['content']['parts'][0]['text']),
                    'tokens_used' => $data['usageMetadata']['totalTokenCount'] ?? 0
                )
            );
        }
        
        return array(
            'success' => false,
            'message' => __('Invalid response from Gemini.', 'greenshift-business-ai')
        );
    }
    
    private function parse_feature_content($content) {
        // Try to extract title and description from AI response
        $lines = explode("\n", trim($content));
        
        if (count($lines) >= 2) {
            return array(
                'title' => trim($lines[0]),
                'description' => trim(implode("\n", array_slice($lines, 1)))
            );
        }
        
        return array(
            'title' => 'Feature',
            'description' => $content
        );
    }
    
    private function extract_location($address) {
        // Simple location extraction from address
        $parts = explode(',', $address);
        return count($parts) > 1 ? trim(end($parts)) : '';
    }
    
    private function get_placeholder_image($type, $business_type) {
        // Return appropriate placeholder image URLs
        $base_url = 'https://images.unsplash.com/';
        
        $image_keywords = array(
            'restaurant' => 'restaurant-food',
            'technology' => 'technology-office',
            'healthcare' => 'medical-healthcare',
            'retail_store' => 'shopping-retail',
            'consulting' => 'business-meeting',
            'real_estate' => 'house-property',
            'law_firm' => 'office-professional',
            'fitness' => 'gym-fitness',
            'education' => 'classroom-learning',
            'automotive' => 'car-automotive',
            'construction' => 'construction-building',
            'beauty' => 'beauty-salon',
            'travel' => 'travel-vacation',
            'financial' => 'finance-banking',
            'marketing' => 'marketing-creative'
        );
        
        $keyword = $image_keywords[$business_type] ?? 'business-professional';
        
        if ($type === 'hero') {
            return $base_url . "photo-1560472354-b33ff0c44a43?w=800&h=600&fit=crop&q=80"; // Generic business
        } else {
            return $base_url . "photo-1556761175-4b46a572b786?w=600&h=400&fit=crop&q=80"; // Generic about
        }
    }
    
    private function get_feature_icon($number, $business_type) {
        $icons = array(
            'restaurant' => array('🍽️', '👨‍🍳', '⭐'),
            'technology' => array('💻', '🚀', '⚡'),
            'healthcare' => array('🏥', '👩‍⚕️', '❤️'),
            'retail_store' => array('🛍️', '💎', '🎁'),
            'consulting' => array('📊', '💡', '🎯'),
            'real_estate' => array('🏠', '🗝️', '📍'),
            'law_firm' => array('⚖️', '📜', '🛡️'),
            'fitness' => array('💪', '🏃‍♂️', '🎯'),
            'education' => array('📚', '🎓', '✨'),
            'automotive' => array('🚗', '🔧', '⚡'),
            'construction' => array('🏗️', '🔨', '📐'),
            'beauty' => array('💄', '✨', '💅'),
            'travel' => array('✈️', '🌍', '🎒'),
            'financial' => array('💰', '📈', '🛡️'),
            'marketing' => array('📢', '🎨', '📊')
        );
        
        $business_icons = $icons[$business_type] ?? array('⭐', '💼', '🚀');
        
        return $business_icons[$number - 1] ?? '⭐';
    }
}