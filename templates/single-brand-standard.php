<?php
/**
 * Template Name: Brand Standard Template
 * Template Post Type: brand_standard
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="brand-standard-container">
    <div class="sidebar">
        <?php
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        $site_title = get_bloginfo('name');
        if (has_custom_logo()) {
            echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="sidebar-logo">';
        } else {
            echo '<h1 class="sidebar-title">' . $site_title . '</h1>';
        }
        ?>
        <nav class="sidebar-nav">
            <?php
            $args = array(
                'post_type' => 'brand_standard',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            );
            $brand_standards = new WP_Query($args);
            if ($brand_standards->have_posts()) :
                while ($brand_standards->have_posts()) : $brand_standards->the_post();
                    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </nav>
    </div>
    
    <div class="content-area">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="cover-block">
                <h1><?php the_title(); ?></h1>
            </div>
            
            <div class="content-columns">
                <div class="left-column">
                    <h2>Section Title</h2>
                </div>
                <div class="right-column">
                    <?php the_content(); ?>
                </div>
            </div>
            
            <?php
            the_post_navigation(
                array(
                    'prev_text' => '&larr; %title',
                    'next_text' => '%title &rarr;',
                )
            );
            ?>
        <?php endwhile; endif; ?>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. All rights reserved.</p>
        <a href="<?php echo get_privacy_policy_url(); ?>">Privacy Policy</a>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>