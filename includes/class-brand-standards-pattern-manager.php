<?php
/**
 * Pattern Management System for Brand Standards Plugin
 */

 class Brand_Standards_Pattern_Manager {
    private $pattern_page_mapping = [
        'Mission and Vision' => 'mission-vision',
        'Logo' => 'logo-guidelines',
        'Colors' => 'color-palette',
        'Typography' => 'typography',
        'Elements' => 'brand-elements',
        'Photography' => 'photography',
        'Brand Standards' => 'brand-overview'
    ];

    public function __construct() {
        $this->register_block_patterns();
    }

    public function register_block_patterns() {
        // Register pattern category if it doesn't exist
        if (!WP_Block_Pattern_Categories_Registry::get_instance()->is_registered('brand-standards')) {
            register_block_pattern_category(
                'brand-standards',
                ['label' => __('Brand Standards', 'brand-standards')]
            );
        }

        // Register each pattern
        foreach ($this->pattern_page_mapping as $title => $filename) {
            $pattern_content = $this->get_pattern_content($filename);
            if ($pattern_content) {
                register_block_pattern(
                    'brand-standards/' . $filename,
                    [
                        'title' => $title,
                        'description' => sprintf(__('%s section template', 'brand-standards'), $title),
                        'content' => $pattern_content,
                        'categories' => ['brand-standards']
                    ]
                );
            }
        }
    }

    public function maybe_apply_pattern($post_id, $post, $update) {
        // Only proceed for new posts
        if ($update || wp_is_post_revision($post_id)) {
            return;
        }

        $post_title = get_the_title($post_id);
        if (isset($this->pattern_page_mapping[$post_title])) {
            $pattern_content = $this->get_pattern_content($this->pattern_page_mapping[$post_title]);
            if ($pattern_content) {
                wp_update_post([
                    'ID' => $post_id,
                    'post_content' => $pattern_content
                ]);
            }
        }
    }

    public function set_default_content($content, $post) {
        if ($post->post_type !== 'brand_standard') {
            return $content;
        }

        $post_title = $post->post_title;
        if (isset($this->pattern_page_mapping[$post_title])) {
            $pattern_content = $this->get_pattern_content($this->pattern_page_mapping[$post_title]);
            if ($pattern_content) {
                return $pattern_content;
            }
        }

        return $this->get_default_pattern_content();
    }

    private function get_pattern_content($pattern_name) {
        $file_path = plugin_dir_path(dirname(__FILE__)) . "patterns/{$pattern_name}.html";
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            // Ensure content is properly formatted for WordPress
            $content = wp_kses_post($content);
            return $content;
        }
        return false;
    }

    private function get_default_pattern_content() {
        return '<!-- wp:brand-standards/brand-guide-section {"leftColumnWidth":33.33,"heading":"New Section"} -->
        <div class="wp-block-brand-standards-brand-guide-section">
            <div class="wp-block-columns">
                <div class="wp-block-column" style="flex-basis:33.33%">
                    <h2>New Section</h2>
                </div>
                <div class="wp-block-column" style="flex-basis:66.67%">
                    <!-- wp:paragraph -->
                    <p>Add your content here.</p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
        </div>
        <!-- /wp:brand-standards/brand-guide-section -->';
    }
}