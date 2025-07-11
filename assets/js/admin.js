/**
 * GreenShift Business AI Generator - Admin JavaScript
 */

(function($) {
    'use strict';

    const GSBA = {
        init: function() {
            this.bindEvents();
            this.initModals();
            this.initForms();
            this.initTemplateSelector();
            this.initFileUploads();
            this.initApiTesting();
        },

        bindEvents: function() {
            // Dashboard events
            $(document).on('click', '#gsba-new-page-btn, #gsba-get-started-btn', this.openGeneratorModal);
            $(document).on('click', '#gsba-generate-another', this.openGeneratorModal);
            
            // Template events  
            $(document).on('click', '#gsba-add-template-btn, #gsba-add-template-btn-card', this.openTemplateModal);
            $(document).on('click', '[data-action="edit-template"]', this.editTemplate);
            $(document).on('click', '[data-action="delete-template"]', this.deleteTemplate);
            $(document).on('click', '[data-action="preview-template"]', this.previewTemplate);
            
            // Page management
            $(document).on('click', '[data-action="delete-page"]', this.deletePage);
            
            // Modal events
            $(document).on('click', '.gsba-modal-close, .gsba-modal-overlay', this.closeModal);
            $(document).on('click', '#gsba-cancel-template', this.closeModal);
            
            // Form navigation
            $(document).on('click', '#gsba-next-step', this.nextStep);
            $(document).on('click', '#gsba-next-step-2', this.nextStep);
            $(document).on('click', '#gsba-prev-step', this.prevStep);
            $(document).on('click', '#gsba-prev-step-2', this.prevStep);
            
            // Form submissions
            $(document).on('submit', '#gsba-generator-form', this.generateBusinessPage);
            $(document).on('submit', '#gsba-template-form', this.saveTemplate);
            
            // Template helpers
            $(document).on('click', '#gsba-load-sample-structure', this.loadSampleStructure);
            $(document).on('click', '#gsba-validate-structure', this.validateJsonStructure);
            
            // API testing
            $(document).on('click', '.gsba-test-api', this.testApiConnection);
            $(document).on('click', '.gsba-toggle-password', this.togglePasswordVisibility);
        },

        initModals: function() {
            // Ensure modals are hidden on page load
            $('.gsba-modal').hide();
        },

        initForms: function() {
            // Initialize form validation
            this.setupFormValidation();
            
            // Auto-save form data to localStorage
            this.setupAutoSave();
        },

        initTemplateSelector: function() {
            $(document).on('click', '.gsba-template-card', function() {
                $('.gsba-template-card').removeClass('gsba-selected');
                $(this).addClass('gsba-selected');
                
                const templateId = $(this).data('template-id');
                $('#selected_template_id').val(templateId);
                $('#gsba-next-step').prop('disabled', false);
            });
        },

        initFileUploads: function() {
            // Logo upload
            $(document).on('click', '#gsba-upload-logo', function(e) {
                e.preventDefault();
                GSBA.openMediaUploader(function(url) {
                    $('#logo_url').val(url);
                });
            });
            
            // Template preview image upload
            $(document).on('click', '#gsba-upload-preview', function(e) {
                e.preventDefault();
                GSBA.openMediaUploader(function(url) {
                    $('#template_preview_image').val(url);
                });
            });
        },

        initApiTesting: function() {
            // Initialize API testing status
            this.checkApiStatuses();
        },

        openGeneratorModal: function(e) {
            e.preventDefault();
            
            // Reset form and show first step
            $('#gsba-generator-form')[0].reset();
            $('.gsba-form-step').removeClass('gsba-step-active');
            $('.gsba-form-step[data-step="1"]').addClass('gsba-step-active');
            $('.gsba-template-card').removeClass('gsba-selected');
            $('#gsba-next-step').prop('disabled', true);
            
            $('#gsba-generator-modal').fadeIn(300);
            $('body').addClass('modal-open');
        },

        openTemplateModal: function(e) {
            e.preventDefault();
            
            // Reset form
            $('#gsba-template-form')[0].reset();
            $('#template_id').val('');
            $('#gsba-modal-title').text(gsba_admin.messages.add_template || 'Add New Template');
            
            $('#gsba-template-modal').fadeIn(300);
            $('body').addClass('modal-open');
        },

        editTemplate: function(e) {
            e.preventDefault();
            
            const templateId = $(this).data('template-id');
            
            // Load template data via AJAX
            $.post(gsba_admin.ajax_url, {
                action: 'gsba_get_template',
                template_id: templateId,
                nonce: gsba_admin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    GSBA.populateTemplateForm(response.data);
                    $('#gsba-modal-title').text('Edit Template');
                    $('#gsba-template-modal').fadeIn(300);
                    $('body').addClass('modal-open');
                } else {
                    GSBA.showNotice(response.data, 'error');
                }
            })
            .fail(function() {
                GSBA.showNotice('Failed to load template data.', 'error');
            });
        },

        deleteTemplate: function(e) {
            e.preventDefault();
            
            if (!confirm(gsba_admin.messages.confirm_delete)) {
                return;
            }
            
            const templateId = $(this).data('template-id');
            const $button = $(this);
            
            $button.prop('disabled', true);
            
            $.post(gsba_admin.ajax_url, {
                action: 'gsba_delete_template',
                template_id: templateId,
                nonce: gsba_admin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    $button.closest('.gsba-template-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                    GSBA.showNotice(response.data, 'success');
                } else {
                    GSBA.showNotice(response.data, 'error');
                    $button.prop('disabled', false);
                }
            })
            .fail(function() {
                GSBA.showNotice('Failed to delete template.', 'error');
                $button.prop('disabled', false);
            });
        },

        previewTemplate: function(e) {
            e.preventDefault();
            
            const templateId = $(this).data('template-id');
            
            // Load template preview via AJAX
            $.post(gsba_admin.ajax_url, {
                action: 'gsba_preview_template',
                template_id: templateId,
                nonce: gsba_admin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    $('#gsba-preview-content').html(response.data);
                    $('#gsba-preview-modal').fadeIn(300);
                    $('body').addClass('modal-open');
                } else {
                    GSBA.showNotice(response.data, 'error');
                }
            })
            .fail(function() {
                GSBA.showNotice('Failed to load template preview.', 'error');
            });
        },

        deletePage: function(e) {
            e.preventDefault();
            
            if (!confirm(gsba_admin.messages.confirm_delete)) {
                return;
            }
            
            const pageId = $(this).data('page-id');
            const $button = $(this);
            
            $button.prop('disabled', true);
            
            $.post(gsba_admin.ajax_url, {
                action: 'gsba_delete_generated_page',
                page_id: pageId,
                nonce: gsba_admin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    $button.closest('.gsba-page-card').fadeOut(300, function() {
                        $(this).remove();
                    });
                    GSBA.showNotice(response.data, 'success');
                } else {
                    GSBA.showNotice(response.data, 'error');
                    $button.prop('disabled', false);
                }
            })
            .fail(function() {
                GSBA.showNotice('Failed to delete page.', 'error');
                $button.prop('disabled', false);
            });
        },

        closeModal: function(e) {
            if (e.target === this || $(e.target).hasClass('gsba-modal-close') || $(e.target).parent().hasClass('gsba-modal-close')) {
                $('.gsba-modal').fadeOut(300);
                $('body').removeClass('modal-open');
            }
        },

        nextStep: function(e) {
            e.preventDefault();
            
            const currentStep = $('.gsba-step-active');
            const currentStepNum = parseInt(currentStep.data('step'));
            
            // Validate current step
            if (!GSBA.validateStep(currentStepNum)) {
                return;
            }
            
            // Move to next step
            const nextStep = currentStepNum + 1;
            currentStep.removeClass('gsba-step-active');
            $(`.gsba-form-step[data-step="${nextStep}"]`).addClass('gsba-step-active');
        },

        prevStep: function(e) {
            e.preventDefault();
            
            const currentStep = $('.gsba-step-active');
            const currentStepNum = parseInt(currentStep.data('step'));
            
            // Move to previous step
            const prevStep = currentStepNum - 1;
            currentStep.removeClass('gsba-step-active');
            $(`.gsba-form-step[data-step="${prevStep}"]`).addClass('gsba-step-active');
        },

        validateStep: function(stepNumber) {
            let isValid = true;
            
            switch (stepNumber) {
                case 1:
                    // Check if template is selected
                    if (!$('#selected_template_id').val()) {
                        GSBA.showNotice('Please select a template.', 'error');
                        isValid = false;
                    }
                    break;
                    
                case 2:
                    // Validate required business info
                    const requiredFields = ['business_name', 'business_type', 'description'];
                    
                    requiredFields.forEach(function(fieldName) {
                        const $field = $(`#${fieldName}`);
                        if (!$field.val().trim()) {
                            $field.addClass('error');
                            isValid = false;
                        } else {
                            $field.removeClass('error');
                        }
                    });
                    
                    if (!isValid) {
                        GSBA.showNotice('Please fill in all required fields.', 'error');
                    }
                    break;
            }
            
            return isValid;
        },

        generateBusinessPage: function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $('#gsba-generate-btn');
            
            // Show loading state
            $submitBtn.addClass('gsba-btn-loading');
            $submitBtn.prop('disabled', true);
            
            // Collect form data
            const formData = new FormData($form[0]);
            formData.append('action', 'gsba_generate_business_page');
            formData.append('nonce', gsba_admin.nonce);
            
            $.ajax({
                url: gsba_admin.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 120000 // 2 minutes timeout
            })
            .done(function(response) {
                if (response.success) {
                    // Show success modal
                    GSBA.showSuccessModal(response.data);
                    $('#gsba-generator-modal').fadeOut(300);
                } else {
                    GSBA.showNotice(response.data, 'error');
                }
            })
            .fail(function(xhr, status, error) {
                let message = 'Failed to generate page.';
                if (status === 'timeout') {
                    message = 'Request timed out. Please try again.';
                }
                GSBA.showNotice(message, 'error');
            })
            .always(function() {
                $submitBtn.removeClass('gsba-btn-loading');
                $submitBtn.prop('disabled', false);
            });
        },

        saveTemplate: function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $('#gsba-save-template');
            
            // Validate JSON structure
            const blockStructure = $('#template_block_structure').val();
            if (blockStructure && !GSBA.isValidJson(blockStructure)) {
                GSBA.showNotice('Invalid JSON in block structure.', 'error');
                return;
            }
            
            // Show loading state
            $submitBtn.addClass('gsba-btn-loading');
            $submitBtn.prop('disabled', true);
            
            // Collect form data
            const formData = GSBA.serializeTemplateForm($form);
            formData.action = 'gsba_save_template';
            formData.nonce = gsba_admin.nonce;
            
            $.post(gsba_admin.ajax_url, formData)
            .done(function(response) {
                if (response.success) {
                    GSBA.showNotice(response.data, 'success');
                    $('#gsba-template-modal').fadeOut(300);
                    // Reload page to show updated template
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    GSBA.showNotice(response.data, 'error');
                }
            })
            .fail(function() {
                GSBA.showNotice('Failed to save template.', 'error');
            })
            .always(function() {
                $submitBtn.removeClass('gsba-btn-loading');
                $submitBtn.prop('disabled', false);
            });
        },

        loadSampleStructure: function(e) {
            e.preventDefault();
            
            const templateType = $('#template_type').val();
            if (!templateType) {
                GSBA.showNotice('Please select a template type first.', 'error');
                return;
            }
            
            // Get sample structure from script tag
            const sampleStructures = JSON.parse($('#gsba-sample-structures').text());
            if (sampleStructures[templateType]) {
                const structure = JSON.stringify(sampleStructures[templateType].blocks, null, 2);
                $('#template_block_structure').val(structure);
                GSBA.showNotice('Sample structure loaded.', 'success');
            } else {
                GSBA.showNotice('No sample structure available for this type.', 'error');
            }
        },

        validateJsonStructure: function(e) {
            e.preventDefault();
            
            const jsonText = $('#template_block_structure').val();
            if (!jsonText.trim()) {
                GSBA.showNotice('Please enter JSON structure first.', 'error');
                return;
            }
            
            if (GSBA.isValidJson(jsonText)) {
                GSBA.showNotice('JSON structure is valid.', 'success');
            } else {
                GSBA.showNotice('Invalid JSON structure. Please check syntax.', 'error');
            }
        },

        testApiConnection: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const model = $button.data('model');
            const $input = $button.siblings('input');
            const apiKey = $input.val();
            
            if (!apiKey.trim()) {
                GSBA.showNotice('Please enter API key first.', 'error');
                return;
            }
            
            $button.prop('disabled', true).text('Testing...');
            
            $.post(gsba_admin.ajax_url, {
                action: 'gsba_test_api',
                model: model,
                api_key: apiKey,
                nonce: gsba_admin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    GSBA.showNotice(`${model.toUpperCase()} API connection successful.`, 'success');
                } else {
                    GSBA.showNotice(`${model.toUpperCase()} API test failed: ${response.data}`, 'error');
                }
            })
            .fail(function() {
                GSBA.showNotice('API test request failed.', 'error');
            })
            .always(function() {
                $button.prop('disabled', false).text('Test');
            });
        },

        togglePasswordVisibility: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $input = $button.siblings('input');
            
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $button.text('Hide');
            } else {
                $input.attr('type', 'password');
                $button.text('Show');
            }
        },

        showSuccessModal: function(data) {
            $('#gsba-view-page').attr('href', data.page_url);
            $('#gsba-edit-page').attr('href', data.edit_url);
            $('#gsba-success-modal').fadeIn(300);
        },

        openMediaUploader: function(callback) {
            if (typeof wp !== 'undefined' && wp.media) {
                const mediaUploader = wp.media({
                    title: 'Select Image',
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    callback(attachment.url);
                });
                
                mediaUploader.open();
            } else {
                const url = prompt('Enter image URL:');
                if (url) {
                    callback(url);
                }
            }
        },

        populateTemplateForm: function(data) {
            $('#template_id').val(data.id);
            $('#template_name').val(data.name);
            $('#template_type').val(data.type);
            $('#template_description').val(data.description);
            $('#template_preview_image').val(data.preview_image);
            $('#template_block_structure').val(data.block_structure);
            
            // Populate SEO fields
            if (data.seo_config) {
                const seoConfig = typeof data.seo_config === 'string' ? 
                    JSON.parse(data.seo_config) : data.seo_config;
                
                $('#seo_meta_title').val(seoConfig.meta_title || '');
                $('#seo_meta_description').val(seoConfig.meta_description || '');
                $('#seo_keywords').val(seoConfig.keywords || '');
                $('#seo_og_title').val(seoConfig.og_title || '');
                $('#seo_og_description').val(seoConfig.og_description || '');
                $('#seo_og_image').val(seoConfig.og_image || '');
            }
        },

        serializeTemplateForm: function($form) {
            const formData = {};
            
            // Basic fields
            $form.find('input, select, textarea').each(function() {
                const $field = $(this);
                const name = $field.attr('name');
                if (name && name !== 'block_structure') {
                    formData[name] = $field.val();
                }
            });
            
            // Block structure
            formData.block_structure = $('#template_block_structure').val();
            
            // SEO configuration
            formData.seo_config = JSON.stringify({
                meta_title: $('#seo_meta_title').val(),
                meta_description: $('#seo_meta_description').val(),
                keywords: $('#seo_keywords').val(),
                og_title: $('#seo_og_title').val(),
                og_description: $('#seo_og_description').val(),
                og_image: $('#seo_og_image').val()
            });
            
            return formData;
        },

        setupFormValidation: function() {
            // Real-time validation
            $(document).on('blur', 'input[required], select[required], textarea[required]', function() {
                const $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
        },

        setupAutoSave: function() {
            // Auto-save form data to localStorage
            $(document).on('input change', '#gsba-generator-form input, #gsba-generator-form select, #gsba-generator-form textarea', function() {
                const formData = $('#gsba-generator-form').serializeArray();
                localStorage.setItem('gsba_form_data', JSON.stringify(formData));
            });
            
            // Restore form data on page load
            const savedData = localStorage.getItem('gsba_form_data');
            if (savedData) {
                try {
                    const formData = JSON.parse(savedData);
                    formData.forEach(function(field) {
                        $(`#${field.name}`).val(field.value);
                    });
                } catch (e) {
                    // Invalid saved data, clear it
                    localStorage.removeItem('gsba_form_data');
                }
            }
        },

        checkApiStatuses: function() {
            // Check which API keys are configured
            $('.gsba-test-api').each(function() {
                const $button = $(this);
                const $input = $button.siblings('input');
                
                if ($input.val().trim()) {
                    $button.removeClass('gsba-btn-disabled');
                } else {
                    $button.addClass('gsba-btn-disabled');
                }
            });
        },

        isValidJson: function(str) {
            try {
                JSON.parse(str);
                return true;
            } catch (e) {
                return false;
            }
        },

        showNotice: function(message, type) {
            type = type || 'info';
            
            // Remove existing notices
            $('.gsba-notice').remove();
            
            // Create notice element
            const $notice = $(`
                <div class="gsba-notice gsba-notice-${type} notice notice-${type} is-dismissible">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);
            
            // Add to page
            if ($('.wrap').length) {
                $('.wrap').eq(0).prepend($notice);
            } else {
                $('body').prepend($notice);
            }
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Manual dismiss
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        GSBA.init();
        
        // Handle WordPress media uploader
        if (typeof wp !== 'undefined' && wp.media) {
            wp.media.view.Modal.prototype.on('escape', function() {
                // Handle ESC key in media modal
            });
        }
    });

    // Handle window resize
    $(window).on('resize', function() {
        // Adjust modal sizes if needed
    });

    // Prevent form submission on Enter key (except in textareas)
    $(document).on('keypress', 'input:not(textarea)', function(e) {
        if (e.which === 13) {
            e.preventDefault();
        }
    });

    // Clear localStorage on successful form submission
    $(document).on('gsba_form_success', function() {
        localStorage.removeItem('gsba_form_data');
    });

})(jQuery);