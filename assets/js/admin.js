/**
 * GreenShift Business AI Generator - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initAdminSettings();
    });

    function initAdminSettings() {
        // Show/hide API key values
        $('.gsba-toggle-password').on('click', function() {
            const $input = $(this).siblings('input[type="password"], input[type="text"]');
            const currentType = $input.attr('type');
            
            if (currentType === 'password') {
                $input.attr('type', 'text');
                $(this).text('Hide');
            } else {
                $input.attr('type', 'password');
                $(this).text('Show');
            }
        });

        // Test API connection
        $('.gsba-test-api').on('click', function() {
            const $button = $(this);
            const apiType = $button.data('api');
            const $input = $button.siblings('input');
            const apiKey = $input.val();

            if (!apiKey) {
                alert('Please enter an API key first.');
                return;
            }

            $button.prop('disabled', true).text('Testing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'gsba_test_api_connection',
                    api_type: apiType,
                    api_key: apiKey,
                    nonce: $('#gsba_admin_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        $button.after('<span class="gsba-test-success">✓ Connection successful</span>');
                    } else {
                        $button.after('<span class="gsba-test-error">✗ ' + response.data + '</span>');
                    }
                },
                error: function() {
                    $button.after('<span class="gsba-test-error">✗ Test failed</span>');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Test Connection');
                    setTimeout(function() {
                        $('.gsba-test-success, .gsba-test-error').fadeOut().remove();
                    }, 3000);
                }
            });
        });

        // Form validation
        $('.gsba-admin-form').on('submit', function() {
            return validateAdminForm();
        });
    }

    function validateAdminForm() {
        let isValid = true;
        const $form = $('.gsba-admin-form');
        
        // Clear previous errors
        $('.gsba-admin-error').remove();

        // Check if at least one API key is provided
        const hasOpenAI = $form.find('[name="openai_api_key"]').val().trim();
        const hasClaude = $form.find('[name="claude_api_key"]').val().trim();
        const hasGemini = $form.find('[name="gemini_api_key"]').val().trim();

        if (!hasOpenAI && !hasClaude && !hasGemini) {
            showAdminError('Please configure at least one API key to use the plugin.');
            isValid = false;
        }

        // Validate max tokens
        const maxTokens = parseInt($form.find('[name="max_tokens"]').val());
        if (maxTokens < 100 || maxTokens > 4000) {
            showAdminError('Max tokens must be between 100 and 4000.');
            isValid = false;
        }

        // Validate temperature
        const temperature = parseFloat($form.find('[name="temperature"]').val());
        if (temperature < 0 || temperature > 1) {
            showAdminError('Temperature must be between 0 and 1.');
            isValid = false;
        }

        return isValid;
    }

    function showAdminError(message) {
        const errorHtml = '<div class="notice notice-error gsba-admin-error"><p>' + message + '</p></div>';
        $('.wrap h1').after(errorHtml);
    }

})(jQuery);