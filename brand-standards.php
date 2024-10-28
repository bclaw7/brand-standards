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

// Define plugin constants
define('BRAND_STANDARDS_PATH', plugin_dir_path(__FILE__));
define('BRAND_STANDARDS_URL', plugin_dir_url(__FILE__));

// Debug information - this will write to the error log when the plugin loads
error_log('Plugin Path: ' . BRAND_STANDARDS_PATH);
error_log('Vendor Path: ' . BRAND_STANDARDS_PATH . 'vendor/autoload.php');
error_log('Vendor exists: ' . (file_exists(BRAND_STANDARDS_PATH . 'vendor/autoload.php') ? 'yes' : 'no'));

// Require dependencies
if (file_exists(BRAND_STANDARDS_PATH . 'vendor/autoload.php')) {
    require_once BRAND_STANDARDS_PATH . 'vendor/autoload.php';
    require_once BRAND_STANDARDS_PATH . 'includes/google-drive-integration.php';
} else {
    // Add admin notice if vendor directory is missing
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Brand Standards plugin: Vendor directory is missing. Please reinstall the plugin.</p></div>';
    });
}

require_once BRAND_STANDARDS_PATH . 'includes/block-patterns.php';
require_once BRAND_STANDARDS_PATH . 'includes/debug.php';

function create_block_brand_standards_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_brand_standards_block_init' );

function brand_standards_enqueue_styles() {
    wp_enqueue_style( 'brand-standards-style', plugin_dir_url( __FILE__ ) . 'css/brand-standards.css' );
    wp_enqueue_style( 'campaign-assets-styles', plugin_dir_url( __FILE__ ) . 'css/campaign-assets.css' );
}
add_action( 'wp_enqueue_scripts', 'brand_standards_enqueue_styles' );

function brand_standards_register_post_type() {
    $args = array(
        'public'    => true,
        'label'     => 'Brand Standards',
        'menu_icon' => 'dashicons-book-alt',
        'supports'  => array( 'title', 'editor', 'custom-fields', 'revisions', 'thumbnail', 'page-attributes' ),
        'show_in_rest' => true,
    );
    register_post_type( 'brand_standard', $args );
}
add_action( 'init', 'brand_standards_register_post_type' );

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
add_action( 'add_meta_boxes', 'brand_standards_add_custom_fields' );

function brand_standards_nav_title_callback( $post ) {
    wp_nonce_field( 'brand_standards_nav_title', 'brand_standards_nav_title_nonce' );
    $value = get_post_meta( $post->ID, '_nav_title', true );
    echo '<label for="brand_standards_nav_title_field">Navigation Title</label>';
    echo '<input type="text" id="brand_standards_nav_title_field" name="brand_standards_nav_title_field" value="' . esc_attr( $value ) . '" size="25" />';
    echo '<p class="description">Leave blank to use the page title in navigation.</p>';
}

function brand_standards_save_custom_fields( $post_id ) {
    if ( ! isset( $_POST['brand_standards_nav_title_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['brand_standards_nav_title_nonce'], 'brand_standards_nav_title' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['brand_standards_nav_title_field'] ) ) {
        update_post_meta( $post_id, '_nav_title', sanitize_text_field( $_POST['brand_standards_nav_title_field'] ) );
    }
}
add_action( 'save_post', 'brand_standards_save_custom_fields' );

function brand_standards_create_pages() {
    $pages = array(
        'Brand Standards' => 'This page provides instructions on how to use our brand standards.',
        'Mission and Vision' => 'This page outlines our company\'s mission and vision.',
        'Logo' => 'This page provides guidelines for using our company logo.',
        'Colors' => 'This page details our brand\'s color palette and usage guidelines.',
        'Typography' => 'This page outlines our brand\'s typography standards.',
        'Elements' => 'This page showcases various brand elements and their usage.',
        'Photography' => 'This page provides guidelines for photography in our brand communications.'
    );

    foreach ( $pages as $page_title => $page_description ) {
        $existing_page = get_page_by_title( $page_title, OBJECT, 'brand_standard' );

        if ( ! $existing_page ) {
            $page_content = '<!-- wp:brand-standards/brand-guide-section {"leftColumnWidth":33.33,"heading":"' . esc_attr($page_title) . '"} -->
            <div class="wp-block-brand-standards-brand-guide-section">
                <div class="wp-block-columns">
                    <div class="wp-block-column" style="flex-basis:33.33%">
                        <h2>' . esc_html($page_title) . '</h2>
                    </div>
                    <div class="wp-block-column" style="flex-basis:66.67%">
                        <!-- wp:paragraph -->
                        <p>' . esc_html($page_description) . '</p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
            </div>
            <!-- /wp:brand-standards/brand-guide-section -->';

            $page_data = array(
                'post_title'    => $page_title,
                'post_content'  => $page_content,
                'post_status'   => 'publish',
                'post_type'     => 'brand_standard',
            );

            $post_id = wp_insert_post( $page_data );

            if ( $page_title === 'Brand Standards' ) {
                update_post_meta( $post_id, '_nav_title', 'How to Use' );
            }
        }
    }
}

function brand_standards_custom_template( $template ) {
    if ( is_singular( 'brand_standard' ) ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/single-brand-standard.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}
add_filter( 'single_template', 'brand_standards_custom_template' );


function brand_standards_register_campaign_post_type() {
    $args = array(
        'public'    => true,
        'label'     => 'Campaigns',
        'menu_icon' => 'dashicons-megaphone',
        'supports'  => array('title', 'editor', 'custom-fields', 'revisions', 'thumbnail'),
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite'     => array('slug' => 'campaigns'),
    );
    register_post_type('campaign', $args);
}
add_action('init', 'brand_standards_register_campaign_post_type');

function brand_standards_add_campaign_custom_fields() {
    add_meta_box(
        'campaign_details',
        'Campaign Details',
        'brand_standards_campaign_details_callback',
        'campaign',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'brand_standards_add_campaign_custom_fields');

function brand_standards_campaign_details_callback($post) {
    wp_nonce_field('campaign_details', 'campaign_details_nonce');
    
    $start_date = get_post_meta($post->ID, '_campaign_start_date', true);
    $end_date = get_post_meta($post->ID, '_campaign_end_date', true);
    $drive_folder_id = get_post_meta($post->ID, '_campaign_drive_folder_id', true);
    
    echo '<p><label for="campaign_start_date">Start Date:</label> ';
    echo '<input type="date" id="campaign_start_date" name="campaign_start_date" value="' . esc_attr($start_date) . '"></p>';
    
    echo '<p><label for="campaign_end_date">End Date:</label> ';
    echo '<input type="date" id="campaign_end_date" name="campaign_end_date" value="' . esc_attr($end_date) . '"></p>';
    
    echo '<p><label for="campaign_drive_folder_id">Google Drive Folder ID:</label> ';
    echo '<input type="text" id="campaign_drive_folder_id" name="campaign_drive_folder_id" value="' . esc_attr($drive_folder_id) . '"></p>';
}

function brand_standards_save_campaign_details($post_id) {
    if (!isset($_POST['campaign_details_nonce']) || !wp_verify_nonce($_POST['campaign_details_nonce'], 'campaign_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['campaign_start_date'])) {
        update_post_meta($post_id, '_campaign_start_date', sanitize_text_field($_POST['campaign_start_date']));
    }
    
    if (isset($_POST['campaign_end_date'])) {
        update_post_meta($post_id, '_campaign_end_date', sanitize_text_field($_POST['campaign_end_date']));
    }
    
    if (isset($_POST['campaign_drive_folder_id'])) {
        update_post_meta($post_id, '_campaign_drive_folder_id', sanitize_text_field($_POST['campaign_drive_folder_id']));
    }
}
add_action('save_post', 'brand_standards_save_campaign_details');

function brand_standards_activate() {
    brand_standards_register_post_type();
    brand_standards_create_pages();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'brand_standards_activate' );