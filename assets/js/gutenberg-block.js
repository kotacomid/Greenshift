/**
 * GreenShift Business AI Generator - Gutenberg Block
 */

(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, ToggleControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('greenshift-business-ai/generator', {
        title: __('Business AI Generator', 'greenshift-business-ai'),
        description: __('AI-powered business content generator. Users can input business details to generate professional content.', 'greenshift-business-ai'),
        icon: 'admin-site-alt3',
        category: 'common',
        keywords: [
            __('AI', 'greenshift-business-ai'),
            __('business', 'greenshift-business-ai'),
            __('generator', 'greenshift-business-ai'),
            __('content', 'greenshift-business-ai')
        ],
        supports: {
            align: ['wide', 'full'],
            html: false
        },
        attributes: {
            title: {
                type: 'string',
                default: __('Business AI Generator', 'greenshift-business-ai')
            },
            showExamples: {
                type: 'boolean',
                default: true
            },
            style: {
                type: 'string',
                default: 'default'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, showExamples, style } = attributes;

            return el(Fragment, {},
                // Inspector Controls (Sidebar)
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Generator Settings', 'greenshift-business-ai'),
                        initialOpen: true
                    },
                        el(TextControl, {
                            label: __('Title', 'greenshift-business-ai'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Show Examples', 'greenshift-business-ai'),
                            checked: showExamples,
                            onChange: function(value) {
                                setAttributes({ showExamples: value });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Style', 'greenshift-business-ai'),
                            value: style,
                            options: [
                                { label: __('Default', 'greenshift-business-ai'), value: 'default' },
                                { label: __('Compact', 'greenshift-business-ai'), value: 'compact' },
                                { label: __('Modern', 'greenshift-business-ai'), value: 'modern' }
                            ],
                            onChange: function(value) {
                                setAttributes({ style: value });
                            }
                        })
                    )
                ),

                // Block Preview (Editor View)
                el('div', {
                    className: 'gsba-block-preview',
                    style: {
                        border: '2px dashed #667eea',
                        borderRadius: '8px',
                        padding: '40px 20px',
                        textAlign: 'center',
                        background: 'linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%)',
                        margin: '20px 0'
                    }
                },
                    el('div', {
                        style: {
                            fontSize: '24px',
                            marginBottom: '10px'
                        }
                    }, '🤖'),
                    el('h3', {
                        style: {
                            color: '#667eea',
                            margin: '0 0 10px 0',
                            fontSize: '20px'
                        }
                    }, title),
                    el('p', {
                        style: {
                            color: '#4a5568',
                            margin: '0 0 15px 0',
                            fontSize: '14px'
                        }
                    }, __('AI-powered business content generator will appear here on the frontend.', 'greenshift-business-ai')),
                    el('div', {
                        style: {
                            display: 'flex',
                            justifyContent: 'center',
                            gap: '10px',
                            fontSize: '12px',
                            color: '#718096'
                        }
                    },
                        el('span', {}, showExamples ? '✓ Examples shown' : '✗ Examples hidden'),
                        el('span', {}, `Style: ${style}`)
                    )
                )
            );
        },

        save: function() {
            // Return null to render via PHP
            return null;
        }
    });

})(window.wp);