<?php
/**
 * AI Generator Class
 * Handles AI API calls for business content generation
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSBA_AI_Generator {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('gsba_settings', array());
    }
    
    /**
     * Generate business content using AI
     */
    public function generate_content($business_name, $business_type, $business_description, $content_type, $ai_model = 'openai') {
        $prompt = $this->build_prompt($business_name, $business_type, $business_description, $content_type);
        
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
    
    /**
     * Build AI prompt based on business information and content type
     */
    private function build_prompt($business_name, $business_type, $business_description, $content_type) {
        $prompts = array(
            'about_us' => "Write a compelling 'About Us' section for {$business_name}, a {$business_type} business. Description: {$business_description}. Make it professional, engaging, and highlight the unique value proposition.",
            
            'mission_vision' => "Create a Mission and Vision statement for {$business_name}, a {$business_type} business. Description: {$business_description}. Make it inspiring and aligned with the business goals.",
            
            'services' => "List and describe the main services offered by {$business_name}, a {$business_type} business. Description: {$business_description}. Make each service compelling and customer-focused.",
            
            'hero_section' => "Write a powerful hero section (headline, subheadline, and call-to-action) for {$business_name}, a {$business_type} business. Description: {$business_description}. Make it attention-grabbing and conversion-focused.",
            
            'features' => "List the key features and benefits of {$business_name}, a {$business_type} business. Description: {$business_description}. Focus on what makes this business unique and valuable to customers.",
            
            'testimonials' => "Create 3 realistic customer testimonials for {$business_name}, a {$business_type} business. Description: {$business_description}. Include customer names and their positive experiences.",
            
            'faq' => "Generate 5 frequently asked questions and answers for {$business_name}, a {$business_type} business. Description: {$business_description}. Address common customer concerns and inquiries.",
            
            'contact_info' => "Create a professional contact section content for {$business_name}, a {$business_type} business. Description: {$business_description}. Include placeholder information and encouraging contact text.",
            
            'blog_post' => "Write a 500-word blog post for {$business_name}, a {$business_type} business. Description: {$business_description}. Make it informative, SEO-friendly, and relevant to the target audience.",
            
            'product_description' => "Write compelling product/service descriptions for {$business_name}, a {$business_type} business. Description: {$business_description}. Focus on benefits and value proposition."
        );
        
        $base_prompt = isset($prompts[$content_type]) ? $prompts[$content_type] : $prompts['about_us'];
        
        return $base_prompt . "\n\nPlease write in a professional, engaging tone that would appeal to potential customers. Format the content with proper HTML structure for web use.";
    }
    
    /**
     * Generate content using OpenAI API
     */
    private function generate_with_openai($prompt) {
        $api_key = isset($this->settings['openai_api_key']) ? $this->settings['openai_api_key'] : '';
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('OpenAI API key not configured. Please set it in the plugin settings.', 'greenshift-business-ai')
            );
        }
        
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        );
        
        $body = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'max_tokens' => isset($this->settings['max_tokens']) ? intval($this->settings['max_tokens']) : 1000,
            'temperature' => isset($this->settings['temperature']) ? floatval($this->settings['temperature']) : 0.7
        );
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => __('API request failed: ', 'greenshift-business-ai') . $response->get_error_message()
            );
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return array(
                'success' => true,
                'data' => array(
                    'content' => $data['choices'][0]['message']['content'],
                    'tokens_used' => isset($data['usage']['total_tokens']) ? $data['usage']['total_tokens'] : 0
                )
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Invalid response from OpenAI API.', 'greenshift-business-ai')
            );
        }
    }
    
    /**
     * Generate content using Claude API
     */
    private function generate_with_claude($prompt) {
        $api_key = isset($this->settings['claude_api_key']) ? $this->settings['claude_api_key'] : '';
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('Claude API key not configured. Please set it in the plugin settings.', 'greenshift-business-ai')
            );
        }
        
        $headers = array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key,
            'anthropic-version' => '2023-06-01'
        );
        
        $body = array(
            'model' => 'claude-3-sonnet-20240229',
            'max_tokens' => isset($this->settings['max_tokens']) ? intval($this->settings['max_tokens']) : 1000,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            )
        );
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => __('API request failed: ', 'greenshift-business-ai') . $response->get_error_message()
            );
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if (isset($data['content'][0]['text'])) {
            return array(
                'success' => true,
                'data' => array(
                    'content' => $data['content'][0]['text'],
                    'tokens_used' => isset($data['usage']['output_tokens']) ? $data['usage']['output_tokens'] : 0
                )
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Invalid response from Claude API.', 'greenshift-business-ai')
            );
        }
    }
    
    /**
     * Generate content using Gemini API
     */
    private function generate_with_gemini($prompt) {
        $api_key = isset($this->settings['gemini_api_key']) ? $this->settings['gemini_api_key'] : '';
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('Gemini API key not configured. Please set it in the plugin settings.', 'greenshift-business-ai')
            );
        }
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'maxOutputTokens' => isset($this->settings['max_tokens']) ? intval($this->settings['max_tokens']) : 1000,
                'temperature' => isset($this->settings['temperature']) ? floatval($this->settings['temperature']) : 0.7
            )
        );
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $api_key;
        
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => __('API request failed: ', 'greenshift-business-ai') . $response->get_error_message()
            );
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return array(
                'success' => true,
                'data' => array(
                    'content' => $data['candidates'][0]['content']['parts'][0]['text'],
                    'tokens_used' => isset($data['usageMetadata']['totalTokenCount']) ? $data['usageMetadata']['totalTokenCount'] : 0
                )
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Invalid response from Gemini API.', 'greenshift-business-ai')
            );
        }
    }
}