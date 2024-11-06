<?php
/**
 * Plugin Name:       Brand Standards
 * Description:       Instant brand standard guidelines.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            Byron Lawlis
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       brand-standards
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-brand-standards-pattern-manager.php';

function init_brand_standards_patterns() {
    new Brand_Standards_Pattern_Manager();
}
add_action('init', 'init_brand_standards_patterns', 20);

function brand_standards_enqueue_styles() {
    wp_enqueue_style('brand-standards-style', plugin_dir_url(__FILE__) . 'css/brand-standards.css');
}
add_action('wp_enqueue_scripts', 'brand_standards_enqueue_styles');

function brand_standards_register_post_type() {
    $args = array(
        'public'    => true,
        'label'     => 'Brand Standards',
        'menu_icon' => 'dashicons-book-alt',
        'supports'  => array('title', 'editor', 'custom-fields', 'revisions', 'thumbnail', 'page-attributes'),
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'brand-standards'),
        // Add template support
        'template' => array(
            array('brand-standards/brand-guide-section', array())
        ),
        'template_lock' => false
    );
    register_post_type('brand_standard', $args);
}
add_action('init', 'brand_standards_register_post_type');

// Register and handle templates
function brand_standards_register_templates() {
    $post_type_object = get_post_type_object('brand_standard');
    if ($post_type_object) {
        $post_type_object->template = array(
            array('brand-standards/brand-guide-section', array())
        );
    }
}
add_action('init', 'brand_standards_register_templates');

// Template loader function
function brand_standards_template_include($template) {
    if (is_singular('brand_standard')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-brand-standard.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'brand_standards_template_include');

function brand_standards_add_custom_fields() {
    add_meta_box(
        'brand_standards_nav_title',
        'Navigation Title',
        'brand_standards_nav_title_callback',
        'brand_standard',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'brand_standards_add_custom_fields');

function brand_standards_nav_title_callback($post) {
    wp_nonce_field('brand_standards_nav_title', 'brand_standards_nav_title_nonce');
    $value = get_post_meta($post->ID, '_nav_title', true);
    echo '<label for="brand_standards_nav_title_field">Navigation Title</label>';
    echo '<input type="text" id="brand_standards_nav_title_field" name="brand_standards_nav_title_field" value="' . esc_attr($value) . '" size="25" />';
    echo '<p class="description">Leave blank to use the page title in navigation.</p>';
}

function brand_standards_save_custom_fields($post_id) {
    if (!isset($_POST['brand_standards_nav_title_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['brand_standards_nav_title_nonce'], 'brand_standards_nav_title')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['brand_standards_nav_title_field'])) {
        update_post_meta($post_id, '_nav_title', sanitize_text_field($_POST['brand_standards_nav_title_field']));
    }
}
add_action('save_post', 'brand_standards_save_custom_fields');

// Create brand standard pages on activation
function brand_standards_create_pages() {
    $pages = array(
        'Brand Standards' => [
            'title' => 'Brand Standards',
            'description' => 'This page provides instructions on how to use our brand standards.',
            'nav_title' => 'How to Use'
        ],
        'Mission and Vision' => [
            'title' => 'Mission and Vision',
            'description' => 'This page outlines our company\'s mission and vision.',
            'nav_title' => ''
        ],
        'Logo' => [
            'title' => 'Logo',
            'description' => 'This page provides guidelines for using our company logo.',
            'nav_title' => ''
        ],
        'Colors' => [
            'title' => 'Colors',
            'description' => 'This page details our brand\'s color palette and usage guidelines.',
            'nav_title' => ''
        ],
        'Typography' => [
            'title' => 'Typography',
            'description' => 'This page outlines our brand\'s typography standards.',
            'nav_title' => ''
        ],
        'Elements' => [
            'title' => 'Elements',
            'description' => 'This page showcases various brand elements and their usage.',
            'nav_title' => ''
        ],
        'Photography' => [
            'title' => 'Photography',
            'description' => 'This page provides guidelines for photography in our brand communications.',
            'nav_title' => ''
        ]
    );

    // Initialize pattern manager
    $pattern_manager = new Brand_Standards_Pattern_Manager();

    foreach ($pages as $key => $page_data) {
        $existing_page = get_page_by_title($page_data['title'], OBJECT, 'brand_standard');

        if (!$existing_page) {
            $page_args = array(
                'post_title'    => $page_data['title'],
                'post_status'   => 'publish',
                'post_type'     => 'brand_standard',
                'post_content'  => '' // Content will be set by pattern manager
            );

            $post_id = wp_insert_post($page_args);

            if (!is_wp_error($post_id)) {
                // Set navigation title if specified
                if (!empty($page_data['nav_title'])) {
                    update_post_meta($post_id, '_nav_title', $page_data['nav_title']);
                }

                // Force pattern application
                $pattern_manager->maybe_apply_pattern($post_id, get_post($post_id), false);
            }
        }
    }
}

function brand_standards_activate() {
    brand_standards_register_post_type();
    brand_standards_create_pages();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'brand_standards_activate');