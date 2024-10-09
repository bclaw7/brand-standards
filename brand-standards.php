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
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_brand_standards_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_brand_standards_block_init' );

require_once plugin_dir_path( __FILE__ ) . 'includes/block-patterns.php';

function brand_standards_register_post_type() {
    $args = array(
        'public'    => true,
        'label'     => 'Brand Standards',
        'menu_icon' => 'dashicons-book-alt',
        'supports'  => array( 'title', 'editor', 'custom-fields', 'revisions', 'thumbnail' ),
        'show_in_rest' => true,
    );
    register_post_type( 'brand_standard', $args );
}
add_action( 'init', 'brand_standards_register_post_type' );

function brand_standards_enqueue_styles() {
    wp_enqueue_style( 'brand-standards-style', plugin_dir_url( __FILE__ ) . 'css/brand-standards.css' );
}
add_action( 'wp_enqueue_scripts', 'brand_standards_enqueue_styles' );

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

function brand_standards_create_pages() {
    $pages = array(
        'How to use' => 'This page provides instructions on how to use our brand standards.',
        'Mission and vision' => 'This page outlines our company\'s mission and vision.',
        'Logo' => 'This page provides guidelines for using our company logo.',
        'Colors' => 'This page details our brand\'s color palette and usage guidelines.',
        'Typography' => 'This page outlines our brand\'s typography standards.',
        'Elements' => 'This page showcases various brand elements and their usage.',
        'Photography' => 'This page provides guidelines for photography in our brand communications.'
    );

    foreach ( $pages as $page_title => $page_description ) {
        $existing_page = get_page_by_title( $page_title, OBJECT, 'brand_standard' );

        if ( ! $existing_page ) {
            $page_content = '<!-- wp:paragraph -->
<p>' . $page_description . '</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Please edit this page to add your specific brand guidelines.</p>
<!-- /wp:paragraph -->';

            $page_data = array(
                'post_title'    => $page_title,
                'post_content'  => $page_content,
                'post_status'   => 'publish',
                'post_type'     => 'brand_standard',
            );

            wp_insert_post( $page_data );
        }
    }
}

function brand_standards_activate() {
    brand_standards_register_post_type();
    brand_standards_create_pages();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'brand_standards_activate' );