<?php
function brand_standards_register_block_pattern_category() {
    if ( function_exists( 'register_block_pattern_category' ) ) {
        register_block_pattern_category(
            'brand-standards',
            array( 'label' => __( 'Brand Standards', 'brand-standards' ) )
        );
    }
}
add_action( 'init', 'brand_standards_register_block_pattern_category' );

function brand_standards_register_block_patterns() {
    register_block_pattern(
        'brand-standards/logo-usage',
        array(
            'title'       => __( 'Logo Usage', 'brand-standards' ),
            'description' => _x( 'A section for logo usage guidelines', 'Block pattern description', 'brand-standards' ),
            'content'     => '<!-- wp:brand-standards/brand-guide-section {"sectionTitle":"Logo Usage","content":"<p>Our logo is the key visual representation of our brand. Use it consistently and prominently.</p>"} -->
                              <div class="wp-block-brand-standards-brand-guide-section">
                                <h2>Logo Usage</h2>
                                <div><p>Our logo is the key visual representation of our brand. Use it consistently and prominently.</p></div>
                              </div>
                              <!-- /wp:brand-standards/brand-guide-section -->',
            'categories'  => array( 'brand-standards' ),
        )
    );
}
add_action( 'init', 'brand_standards_register_block_patterns' );