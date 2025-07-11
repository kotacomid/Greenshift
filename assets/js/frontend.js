/**
 * GreenShift Business AI Generator - Frontend JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initBusinessGenerator();
    });

    function initBusinessGenerator() {
        $('.gsba-business-generator').each(function() {
            const $container = $(this);
            const $form = $container.find('#gsba-business-form');
            const $generateBtn = $container.find('#gsba-generate-btn');
            const $result = $container.find('#gsba-result');
            const $error = $container.find('#gsba-error');
            const $copyBtn = $container.find('#gsba-copy-btn');
            const $regenerateBtn = $container.find('#gsba-regenerate-btn');

            // Form submission
            $form.on('submit', function(e) {
                e.preventDefault();
                generateContent($container);
            });

            // Copy to clipboard
            $copyBtn.on('click', function() {
                copyToClipboard($container);
            });

            // Regenerate content
            $regenerateBtn.on('click', function() {
                generateContent($container);
            });

            // Auto-resize textarea
            $container.find('textarea').on('input', function() {
                autoResizeTextarea(this);
            });

            // Form validation on input
            $form.find('input, select, textarea').on('blur', function() {
                validateField($(this));
            });
        });
    }

    function generateContent($container) {
        const $form = $container.find('#gsba-business-form');
        const $generateBtn = $container.find('#gsba-generate-btn');
        const $result = $container.find('#gsba-result');
        const $error = $container.find('#gsba-error');
        const $btnText = $generateBtn.find('.gsba-btn-text');
        const $spinner = $generateBtn.find('.gsba-loading-spinner');

        // Validate form
        if (!validateForm($form)) {
            return;
        }

        // Get form data
        const formData = {
            action: 'gsba_generate_business_content',
            nonce: gsba_ajax.nonce,
            business_name: $form.find('[name="business_name"]').val().trim(),
            business_type: $form.find('[name="business_type"]').val(),
            business_description: $form.find('[name="business_description"]').val().trim(),
            content_type: $form.find('[name="content_type"]').val(),
            ai_model: $form.find('[name="ai_model"]').val()
        };

        // Update UI - loading state
        $generateBtn.prop('disabled', true);
        $btnText.hide();
        $spinner.show();
        $result.hide();
        $error.hide();

        // Make AJAX request
        $.ajax({
            url: gsba_ajax.ajax_url,
            type: 'POST',
            data: formData,
            timeout: 60000, // 60 seconds timeout
            success: function(response) {
                if (response.success) {
                    displayResult($container, response.data);
                } else {
                    displayError($container, response.data || gsba_ajax.error_text);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = gsba_ajax.error_text;
                
                if (status === 'timeout') {
                    errorMessage = 'Request timed out. Please try again.';
                } else if (xhr.responseJSON && xhr.responseJSON.data) {
                    errorMessage = xhr.responseJSON.data;
                }
                
                displayError($container, errorMessage);
            },
            complete: function() {
                // Reset button state
                $generateBtn.prop('disabled', false);
                $btnText.show();
                $spinner.hide();
            }
        });
    }

    function validateForm($form) {
        let isValid = true;
        const requiredFields = $form.find('[required]');

        requiredFields.each(function() {
            const $field = $(this);
            if (!validateField($field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    function validateField($field) {
        const value = $field.val().trim();
        const isRequired = $field.attr('required') !== undefined;
        let isValid = true;

        // Remove existing error styles
        $field.removeClass('gsba-field-error');
        $field.siblings('.gsba-field-error-message').remove();

        if (isRequired && !value) {
            isValid = false;
            showFieldError($field, 'This field is required.');
        } else if ($field.attr('name') === 'business_name' && value.length < 2) {
            isValid = false;
            showFieldError($field, 'Business name must be at least 2 characters.');
        } else if ($field.attr('name') === 'business_description' && value.length < 10) {
            isValid = false;
            showFieldError($field, 'Please provide a more detailed description (at least 10 characters).');
        }

        return isValid;
    }

    function showFieldError($field, message) {
        $field.addClass('gsba-field-error');
        $field.after('<div class="gsba-field-error-message">' + message + '</div>');
    }

    function displayResult($container, data) {
        const $result = $container.find('#gsba-result');
        const $resultContent = $container.find('#gsba-result-content');
        const $tokensInfo = $container.find('.gsba-tokens-info');
        
        // Display content
        $resultContent.html(data.content);
        
        // Show tokens info if available
        if (data.tokens_used) {
            $tokensInfo.text(`Tokens used: ${data.tokens_used}`);
        } else {
            $tokensInfo.text('');
        }
        
        // Show result container
        $result.show();
        
        // Scroll to result
        $result[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function displayError($container, message) {
        const $error = $container.find('#gsba-error');
        const $errorContent = $container.find('.gsba-error-content');
        
        $errorContent.text(message);
        $error.show();
        
        // Scroll to error
        $error[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function copyToClipboard($container) {
        const $resultContent = $container.find('#gsba-result-content');
        const $copyBtn = $container.find('#gsba-copy-btn');
        const originalText = $copyBtn.text();
        
        // Get text content (strip HTML)
        const textContent = $resultContent.text();
        
        // Try to copy using modern clipboard API
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textContent).then(function() {
                showCopySuccess($copyBtn, originalText);
            }).catch(function() {
                fallbackCopyToClipboard(textContent, $copyBtn, originalText);
            });
        } else {
            fallbackCopyToClipboard(textContent, $copyBtn, originalText);
        }
    }

    function fallbackCopyToClipboard(text, $copyBtn, originalText) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess($copyBtn, originalText);
        } catch (err) {
            $copyBtn.text('Copy failed');
            setTimeout(function() {
                $copyBtn.text(originalText);
            }, 2000);
        }
        
        document.body.removeChild(textArea);
    }

    function showCopySuccess($copyBtn, originalText) {
        $copyBtn.text('Copied!');
        setTimeout(function() {
            $copyBtn.text(originalText);
        }, 2000);
    }

    function autoResizeTextarea(textarea) {
        const $textarea = $(textarea);
        $textarea.css('height', 'auto');
        $textarea.css('height', textarea.scrollHeight + 'px');
    }

    // Initialize textareas on page load
    $(document).ready(function() {
        $('.gsba-textarea').each(function() {
            autoResizeTextarea(this);
        });
    });

})(jQuery);

// Add CSS for field validation errors
(function() {
    const style = document.createElement('style');
    style.textContent = `
        .gsba-field-error {
            border-color: #e53e3e !important;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
        }
        
        .gsba-field-error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .gsba-business-generator .gsba-field-error-message {
            display: block;
        }
    `;
    document.head.appendChild(style);
})();