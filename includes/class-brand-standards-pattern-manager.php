<?php
/**
 * Pattern Management System for Brand Standards Plugin
 */

class Brand_Standards_Pattern_Manager {
    private $patterns = [];
    private $is_divi_active = false;

    public function __construct() {
        $this->is_divi_active = $this->check_if_divi_active();
        $this->init_hooks();
    }

    private function init_hooks() {
        if ($this->is_divi_active) {
            add_action('divi_extensions_init', [$this, 'register_divi_layouts']);
        } else {
            add_action('init', [$this, 'register_block_patterns']);
        }
        add_action('admin_init', [$this, 'maybe_create_default_patterns']);
    }

    private function check_if_divi_active() {
        return function_exists('et_setup_theme');
    }

    public function register_block_patterns() {
        // Register pattern category if not already registered
        if (!WP_Block_Pattern_Categories_Registry::get_instance()->is_registered('brand-standards')) {
            register_block_pattern_category(
                'brand-standards',
                ['label' => __('Brand Standards', 'brand-standards')]
            );
        }

        // Register each pattern
        foreach ($this->get_patterns() as $pattern) {
            register_block_pattern(
                'brand-standards/' . $pattern['name'],
                [
                    'title' => $pattern['title'],
                    'description' => $pattern['description'],
                    'content' => $pattern['content'],
                    'categories' => ['brand-standards'],
                    'keywords' => $pattern['keywords'] ?? [],
                ]
            );
        }
    }

    public function register_divi_layouts() {
        // Convert block patterns to Divi layouts
        foreach ($this->get_patterns() as $pattern) {
            $layout_data = $this->convert_to_divi_layout($pattern);
            $this->save_divi_layout($layout_data);
        }
    }

    private function convert_to_divi_layout($pattern) {
        // Convert block pattern content to Divi layout structure
        // This is a simplified example - you'll need to implement proper conversion logic
        return [
            'name' => $pattern['title'],
            'content' => $this->blocks_to_divi($pattern['content']),
            'category' => 'Brand Standards'
        ];
    }

    private function blocks_to_divi($block_content) {
        // Convert Gutenberg blocks to Divi modules
        // This is where you'll implement the conversion logic
        $divi_content = [];
        
        // Example conversion of brand guide section to Divi
        if (strpos($block_content, 'wp-block-brand-standards-brand-guide-section') !== false) {
            $divi_content = [
                'type' => 'row',
                'columns' => [
                    [
                        'type' => 'column',
                        'width' => '1_3',
                        'content' => [
                            'type' => 'text',
                            'content' => '<!-- Add heading content -->'
                        ]
                    ],
                    [
                        'type' => 'column',
                        'width' => '2_3',
                        'content' => [
                            'type' => 'text',
                            'content' => '<!-- Add main content -->'
                        ]
                    ]
                ]
            ];
        }

        return json_encode($divi_content);
    }

    private function save_divi_layout($layout_data) {
        if (class_exists('ET_Builder_Library')) {
            // Save layout to Divi Library
            $post_data = [
                'post_title' => $layout_data['name'],
                'post_content' => $layout_data['content'],
                'post_status' => 'publish',
                'post_type' => 'et_pb_layout',
                'tax_input' => [
                    'layout_category' => [$layout_data['category']]
                ]
            ];

            wp_insert_post($post_data);
        }
    }

    private function get_patterns() {
        return [
            'mission-vision' => [
                'name' => 'mission-vision',
                'title' => __('Mission and Vision', 'brand-standards'),
                'description' => __('Mission and vision statement layout with supporting content.', 'brand-standards'),
                'content' => $this->get_mission_vision_pattern(),
                'keywords' => ['mission', 'vision', 'values']
            ],
            'logo-guidelines' => [
                'name' => 'logo-guidelines',
                'title' => __('Logo Guidelines', 'brand-standards'),
                'description' => __('Logo usage guidelines with examples.', 'brand-standards'),
                'content' => $this->get_logo_guidelines_pattern(),
                'keywords' => ['logo', 'branding', 'identity']
            ],
            'color-palette' => [
                'name' => 'color-palette',
                'title' => __('Color Palette', 'brand-standards'),
                'description' => __('Brand color palette with usage examples.', 'brand-standards'),
                'content' => $this->get_color_palette_pattern(),
                'keywords' => ['colors', 'palette', 'brand colors']
            ],
            // Add more patterns as needed
        ];
    }

    private function get_mission_vision_pattern() {
        return '<!-- wp:brand-standards/brand-guide-section {"leftColumnWidth":33.33,"heading":"Mission and Vision"} -->
        <div class="wp-block-brand-standards-brand-guide-section">
            <div class="wp-block-columns">
                <div class="wp-block-column" style="flex-basis:33.33%">
                    <h2>Mission and Vision</h2>
                </div>
                <div class="wp-block-column" style="flex-basis:66.67%">
                    <!-- wp:heading {"level":3} -->
                    <h3>Our Mission</h3>
                    <!-- /wp:heading -->
                    <!-- wp:paragraph -->
                    <p>Enter your mission statement here.</p>
                    <!-- /wp:paragraph -->
                    <!-- wp:heading {"level":3} -->
                    <h3>Our Vision</h3>
                    <!-- /wp:heading -->
                    <!-- wp:paragraph -->
                    <p>Enter your vision statement here.</p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
        </div>
        <!-- /wp:brand-standards/brand-guide-section -->';
    }

    private function get_logo_guidelines_pattern() {
        // Similar pattern structure for logo guidelines
        return '<!-- wp:brand-standards/brand-guide-section -->...<!-- /wp:brand-standards/brand-guide-section -->';
    }

    private function get_color_palette_pattern() {
        // Similar pattern structure for color palette
        return '<!-- wp:brand-standards/brand-guide-section -->...<!-- /wp:brand-standards/brand-guide-section -->';
    }

    public function maybe_create_default_patterns() {
        $patterns_created = get_option('brand_standards_patterns_created');
        
        if (!$patterns_created) {
            if ($this->is_divi_active) {
                $this->register_divi_layouts();
            } else {
                $this->register_block_patterns();
            }
            update_option('brand_standards_patterns_created', true);
        }
    }
}

// Initialize the pattern manager
function init_brand_standards_patterns() {
    new Brand_Standards_Pattern_Manager();
}
add_action('plugins_loaded', 'init_brand_standards_patterns');