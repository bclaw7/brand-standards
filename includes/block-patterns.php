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
        'brand-standards/default-section',
        array(
            'title'       => __( 'Default Brand Guide Section', 'brand-standards' ),
            'description' => _x( 'A default section for brand guidelines with columns', 'Block pattern description', 'brand-standards' ),
            'content'     => '<!-- wp:brand-standards/brand-guide-section {"leftColumnWidth":33.33,"heading":"Section Heading","content":"<p>Add your content here.</p>"} /-->',
            'categories'  => array( 'brand-standards' ),
        )
    );
    register_block_pattern(
        'brand-standards/logo-section',
        array(
          'title' => __('Logo Guidelines Section', 'brand-standards'),
          'description' => __('A section for displaying logo variations with tabs', 'brand-standards'),
          'categories' => array('brand-standards'),
          'content' => '<!-- wp:brand-standards/brand-guide-section -->
    <div class="wp-block-brand-standards-brand-guide-section">
      <div class="wp-block-columns">
        <div class="wp-block-column" style="flex-basis:33.33%">
          <h2>Logo Guidelines</h2>
        </div>
        <div class="wp-block-column" style="flex-basis:66.67%">
          <!-- wp:brand-standards/logo-tabs {"className":"alignwide"} /-->
        </div>
      </div>
    </div>
    <!-- /wp:brand-standards/brand-guide-section -->',
        )
      );
}
add_action( 'init', 'brand_standards_register_block_patterns' );