<?php
/**
 * Brand Standards Settings Page
 */

// Add settings menu
function brand_standards_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=brand_standard',
        'Brand Standards Settings',
        'Settings',
        'manage_options',
        'brand-standards-settings',
        'brand_standards_settings_page_content'
    );
}
add_action('admin_menu', 'brand_standards_add_settings_page');

// Define default colors
function brand_standards_get_default_colors() {
    return [
        'base' => '#ffffff',      // Light background
        'contrast' => '#000000',  // Dark text/elements
        'primary' => '#1a4548',   // Main brand color
        'secondary' => '#3e838c', // Supporting brand color
        'accent' => '#f4d03f'     // Highlight/call-to-action color
    ];
}

// Color descriptions for the settings page
function brand_standards_get_color_descriptions() {
    return [
        'base' => 'Main background color, typically light for readability',
        'contrast' => 'Main text color, should contrast well with base color',
        'primary' => 'Primary brand color, used for main elements and headers',
        'secondary' => 'Secondary brand color, used for supporting elements',
        'accent' => 'Accent color for highlights and call-to-action elements'
    ];
}

// Sanitize colors
function brand_standards_sanitize_colors($input) {
    if (!is_array($input)) {
        return brand_standards_get_default_colors();
    }

    $defaults = brand_standards_get_default_colors();
    $output = [];

    foreach ($defaults as $key => $default_value) {
        if (isset($input[$key])) {
            $color = sanitize_hex_color($input[$key]);
            $output[$key] = $color ? $color : $default_value;
        } else {
            $output[$key] = $default_value;
        }
    }

    return $output;
}

// Sanitize CSS
function brand_standards_sanitize_css($css) {
    if (empty($css)) {
        return '';
    }
    return wp_strip_all_tags($css);
}

// Get logo URL helper function
function brand_standards_get_logo_url() {
    // First check for custom logo in settings
    $custom_logo = get_option('brand_standards_custom_logo');
    if (!empty($custom_logo)) {
        return $custom_logo;
    }

    // Then check for theme custom logo
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_image = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo_image) {
            return $logo_image[0];
        }
    }

    return false;
}


// Register settings
function brand_standards_register_settings() {
    // Register color settings
    register_setting(
        'brand_standards_settings',
        'brand_standards_colors',
        [
            'type' => 'array',
            'default' => brand_standards_get_default_colors(),
            'sanitize_callback' => 'brand_standards_sanitize_colors'
        ]
    );

    // Register logo setting
    register_setting(
        'brand_standards_settings',
        'brand_standards_custom_logo',
        [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ]
    );

    // Register custom CSS setting
    register_setting(
        'brand_standards_settings',
        'brand_standards_custom_css',
        [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'brand_standards_sanitize_css'
        ]
    );
}
add_action('admin_init', 'brand_standards_register_settings');

// Settings page content
function brand_standards_settings_page_content() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get current settings
    $colors = get_option('brand_standards_colors', brand_standards_get_default_colors());
    $color_descriptions = brand_standards_get_color_descriptions();
    $custom_logo = get_option('brand_standards_custom_logo');
    $custom_css = get_option('brand_standards_custom_css');

    // Get current theme logo
    $theme_logo_url = '';
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_image = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo_image) {
            $theme_logo_url = $logo_image[0];
        }
    }

    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php settings_errors(); ?>
        
        <form method="post" action="options.php">
            <?php settings_fields('brand_standards_settings'); ?>
            
            <div class="brand-standards-settings-grid">
                <!-- Colors Section -->
                <div class="settings-section">
                    <h2>Brand Colors</h2>
                    <div class="color-palette-preview">
                        <?php foreach ($colors as $color_key => $color_value): ?>
                            <div class="color-preview" style="background-color: <?php echo esc_attr($color_value); ?>">
                                <span><?php echo esc_html(ucfirst($color_key)); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <table class="form-table">
                        <?php foreach ($colors as $color_key => $color_value): ?>
                            <tr>
                                <th scope="row"><?php echo esc_html(ucfirst($color_key)); ?></th>
                                <td>
                                    <div class="color-input-group">
                                        <input type="color" 
                                               name="brand_standards_colors[<?php echo esc_attr($color_key); ?>]" 
                                               value="<?php echo esc_attr($color_value); ?>"
                                               class="color-picker">
                                        <code class="color-value"><?php echo esc_html($color_value); ?></code>
                                        <p class="color-description">
                                            <?php echo esc_html($color_descriptions[$color_key]); ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="button" id="reset_colors">Reset to Defaults</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Logo Section -->
                <div class="settings-section">
                    <h2>Logo Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Current Theme Logo</th>
                            <td>
                                <?php if ($theme_logo_url): ?>
                                    <img src="<?php echo esc_url($theme_logo_url); ?>" 
                                         alt="Theme Logo" 
                                         style="max-width: 200px; height: auto;">
                                <?php else: ?>
                                    <p>No theme logo set</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Custom Logo</th>
                            <td>
                                <div class="logo-preview">
                                    <?php if ($custom_logo): ?>
                                        <img src="<?php echo esc_url($custom_logo); ?>" 
                                             alt="Custom Logo" 
                                             style="max-width: 200px; height: auto;">
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" 
                                       name="brand_standards_custom_logo" 
                                       id="brand_standards_custom_logo" 
                                       value="<?php echo esc_attr($custom_logo); ?>">
                                <button type="button" class="button" id="upload_logo_button">
                                    <?php echo $custom_logo ? 'Change Logo' : 'Upload Logo'; ?>
                                </button>
                                <?php if ($custom_logo): ?>
                                    <button type="button" class="button" id="remove_logo_button">
                                        Remove Logo
                                    </button>
                                <?php endif; ?>
                                <p class="description">
                                    Upload a custom logo or use your theme's logo. 
                                    If you upload a custom logo, it will override the theme logo.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Custom CSS Section -->
                <div class="settings-section">
                    <h2>Custom CSS</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Custom CSS</th>
                            <td>
                                <textarea name="brand_standards_custom_css" 
                                          rows="10" 
                                          class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
                                <p class="description">
                                    Add custom CSS to override default styles. This CSS will be added after the default styles.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                </div>

<?php submit_button(); ?>
</form>
</div>

<style>
.brand-standards-settings-grid {
display: grid;
gap: 2rem;
margin: 2rem 0;
}
.settings-section {
background: white;
padding: 1.5rem;
border: 1px solid #ccc;
border-radius: 4px;
}
.color-palette-preview {
display: flex;
gap: 1rem;
margin-bottom: 2rem;
padding: 1rem;
background: #f0f0f0;
border-radius: 4px;
}
.color-preview {
width: 100px;
height: 100px;
border-radius: 4px;
display: flex;
align-items: center;
justify-content: center;
color: white;
text-shadow: 0 0 3px rgba(0,0,0,0.5);
font-weight: bold;
}
.color-input-group {
display: flex;
align-items: center;
gap: 1rem;
}
.color-picker {
width: 60px;
height: 40px;
padding: 0;
border: none;
cursor: pointer;
}
.color-value {
font-size: 14px;
background: #f0f0f0;
padding: 4px 8px;
border-radius: 4px;
}
.color-description {
margin: 0.5rem 0 0;
font-style: italic;
color: #666;
}
#reset_colors {
margin-top: 1rem;
}
</style>

<script>
jQuery(document).ready(function($) {
// Handle color input changes
$('.color-picker').on('input', function() {
$(this).next('.color-value').text($(this).val());
updateColorPreview($(this));
});

// Update color preview
function updateColorPreview(input) {
var colorKey = input.attr('name').match(/\[(.*?)\]/)[1];
var colorValue = input.val();
$('.color-preview').each(function() {
    if ($(this).find('span').text().toLowerCase() === colorKey) {
        $(this).css('background-color', colorValue);
    }
});
}

// Reset colors to defaults
$('#reset_colors').on('click', function() {
var defaultColors = <?php echo json_encode(brand_standards_get_default_colors()); ?>;
Object.keys(defaultColors).forEach(function(key) {
    var input = $('input[name="brand_standards_colors[' + key + ']"]');
    input.val(defaultColors[key]);
    input.next('.color-value').text(defaultColors[key]);
    updateColorPreview(input);
});
});

        // Handle logo upload
        $('#upload_logo_button').on('click', function(e) {
            e.preventDefault();
            
            var frame = wp.media({
                title: 'Select or Upload Logo',
                button: {
                    text: 'Use this logo'
                },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#brand_standards_custom_logo').val(attachment.url);
                $('.logo-preview').html('<img src="' + attachment.url + '" alt="Custom Logo" style="max-width: 200px; height: auto;">');
                $('#upload_logo_button').text('Change Logo');
                
                if (!$('#remove_logo_button').length) {
                    $('#upload_logo_button').after('<button type="button" class="button" id="remove_logo_button">Remove Logo</button>');
                }
            });

            frame.open();
        });

        // Handle logo removal
        $(document).on('click', '#remove_logo_button', function(e) {
            e.preventDefault();
            $('#brand_standards_custom_logo').val('');
            $('.logo-preview').empty();
            $('#upload_logo_button').text('Upload Logo');
            $(this).remove();
        });
    });
    </script>
    <?php
}

// Enqueue custom styles
function brand_standards_enqueue_custom_styles() {
    if (is_singular('brand_standard')) {
        $colors = get_option('brand_standards_colors', brand_standards_get_default_colors());
        $custom_css = get_option('brand_standards_custom_css');
        
        $css = "
            .brand_standard-template-default {
                --bs-base: {$colors['base']};
                --bs-contrast: {$colors['contrast']};
                --bs-primary: {$colors['primary']};
                --bs-secondary: {$colors['secondary']};
                --bs-accent: {$colors['accent']};
            }
            
            .brand_standard-template-default .sidebar {
                background-color: var(--bs-base);
                color: var(--bs-contrast);
            }
            
            .brand_standard-template-default .sidebar-nav a {
                color: var(--bs-contrast);
            }
            
            .brand_standard-template-default .sidebar-nav a:hover {
                color: var(--bs-primary);
            }
            
            .brand_standard-template-default .cover-block {
                background-color: var(--bs-contrast);
                border-bottom: 20px solid var(--bs-primary);
            }
            
            .brand_standard-template-default .cover-block h1 {
                color: var(--bs-base) !important;
            }
            
            .brand_standard-template-default .site-footer {
                background-color: var(--bs-primary);
                color: var(--bs-base);
            }
            
            .brand_standard-template-default .footer-content a {
                color: var(--bs-accent);
            }
            
            .brand_standard-template-default .custom-navigation a {
                color: var(--bs-primary);
            }
            
            .brand_standard-template-default .custom-navigation a:hover {
                color: var(--bs-secondary);
            }
        ";
        
        if (!empty($custom_css)) {
            $css .= $custom_css;
        }
        
        wp_add_inline_style('brand-standards-style', $css);
    }
}
add_action('wp_enqueue_scripts', 'brand_standards_enqueue_custom_styles', 20);

// Update template to use custom logo
function brand_standards_update_logo_template($content) {
    $logo_url = brand_standards_get_logo_url();
    if ($logo_url) {
        echo '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="sidebar-logo">';
    } else {
        echo '<h1 class="sidebar-title">' . esc_html(get_bloginfo('name')) . '</h1>';
    }
}
// Remove the original logo code from template and add our function
remove_action('brand_standards_logo', 'brand_standards_display_logo');
add_action('brand_standards_logo', 'brand_standards_update_logo_template');