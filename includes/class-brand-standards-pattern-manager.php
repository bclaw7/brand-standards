<?php
/**
 * Pattern Management System for Brand Standards Plugin
 */

class Brand_Standards_Pattern_Manager {
    private $pattern_page_mapping = [];

    public function __construct() {
        $this->init_hooks();
        $this->setup_pattern_mapping();
    }

    private function init_hooks() {
        add_action('init', [$this, 'register_block_patterns']);
        add_action('save_post_brand_standard', [$this, 'maybe_apply_pattern'], 10, 3);
        add_filter('default_content', [$this, 'set_default_content'], 10, 2);
    }

    private function setup_pattern_mapping() {
        // Map page titles to their corresponding pattern files
        $this->pattern_page_mapping = [
            'Mission and Vision' => 'mission-vision',
            'Logo' => 'logo-guidelines',
            'Colors' => 'color-palette',
            'Typography' => 'typography',
            'Elements' => 'brand-elements',
            'Photography' => 'photography',
            'Brand Standards' => 'brand-overview'
        ];
    }

    public function register_block_patterns() {
        // Register pattern category
        if (!WP_Block_Pattern_Categories_Registry::get_instance()->is_registered('brand-standards')) {
            register_block_pattern_category(
                'brand-standards',
                ['label' => __('Brand Standards', 'brand-standards')]
            );
        }

        // Register each pattern from the patterns directory
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
        // Only proceed if this is a new post
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
        $file_path = plugin_dir_path(__FILE__) . "../patterns/{$pattern_name}.html";
        if (file_exists($file_path)) {
            return file_get_contents($file_path);
        }
        return false;
    }

    private function get_default_pattern_content() {
        $default_file = plugin_dir_path(__FILE__) . "../patterns/default-section.html";
        if (file_exists($default_file)) {
            return file_get_contents($default_file);
        }
        
        // Fallback default pattern if file doesn't exist
        return '<!-- wp:brand-standards/brand-guide-section {"leftColumnWidth":33.33} -->
        <div class="wp-block-brand-standards-brand-guide-section">
            <div class="wp-block-columns">
                <div class="wp-block-column" style="flex-basis:33.33%">
                    <h2>New Section</h2>
                </div>
                <div class="wp-block-column" style="flex-basis:66.67%">
                    <p>Add your content here.</p>
                </div>
            </div>
        </div>
        <!-- /wp:brand-standards/brand-guide-section -->';
    }
}

// Initialize the pattern manager
function init_brand_standards_patterns() {
    new Brand_Standards_Pattern_Manager();
}
add_action('plugins_loaded', 'init_brand_standards_patterns');