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
}
add_action( 'init', 'brand_standards_register_block_patterns' );