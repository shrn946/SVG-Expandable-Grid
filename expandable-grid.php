<?php
/*
Plugin Name: SVG Expandable Grid
Description: A WordPress plugin to implement an expandable Post grid feature.[wp_expandable_grid][wp_expandable_grid category="podcast" posts_per_page="5"] 
Version: 1.0
Author: Hassan Naqvi
*/

function enqueue_expandable_grid_scripts_styles() {
    // Get the URL of the plugin directory
    $plugin_url = plugin_dir_url(__FILE__);

    // Enqueue scripts
    wp_enqueue_script('trianglify', $plugin_url . 'js/vendors/trianglify.min.js', array(), null, true);
    wp_enqueue_script('tweenmax', $plugin_url . 'js/vendors/TweenMax.min.js', array(), null, true);
    wp_enqueue_script('scrolltoplugin', $plugin_url . 'js/vendors/ScrollToPlugin.min.js', array(), null, true);
    wp_enqueue_script('cash', $plugin_url . 'js/vendors/cash.min.js', array(), null, true);
    wp_enqueue_script('cardcircle', $plugin_url . 'js/Card-circle.js', array(), null, true);
    wp_enqueue_script('demo', $plugin_url . 'js/demo.js', array(), null, true);

    // Enqueue styles
    wp_enqueue_style('normalize', $plugin_url . 'css/normalize.css');
    wp_enqueue_style('font-awesome', $plugin_url . 'fonts/font-awesome-4.3.0/css/font-awesome.min.css');
    wp_enqueue_style('demo', $plugin_url . 'css/demo.css');
    wp_enqueue_style('card', $plugin_url . 'css/card.css');
    wp_enqueue_style('pattern', $plugin_url . 'css/pattern.css');
}

// Hook scripts and styles to WordPress
add_action('wp_enqueue_scripts', 'enqueue_expandable_grid_scripts_styles');


function wp_expandable_grid_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(
        array(
            'category' => '', // Default to empty, no specific category
            'posts_per_page' => -1, // Default to show all posts
            'orderby' => 'date',
            'order' => 'DESC',
        ),
        $atts,
        'wp_expandable_grid'
    );

    // Query arguments
    $args = array(
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );

    // If a specific category is provided, add it to the query
    if (!empty($atts['category'])) {
        $args['category_name'] = $atts['category'];
    }

    // WP Query
    $latest_posts = new WP_Query($args);

    // Start of HTML Output
    $output = '<div class="container">';
    $output .= '<div class="content-f">';
    $output .= '<div class="pattern pattern--hidden"></div>';
    $output .= '<div class="wrapper">';

    // Loop through posts
    if ($latest_posts->have_posts()) :
        $count = 1; // Initialize counter for unique IDs
        while ($latest_posts->have_posts()) : $latest_posts->the_post();

            // Individual Post Card
            $output .= '<div class="card">';
            $output .= '<div class="card__container card__container--closed">';

            // Post Image with Unique ClipPath ID
            $clipPathID = 'clipPath' . $count;

            // Check if the post has a featured image, otherwise use fallback
            $image_url = (has_post_thumbnail()) ? get_the_post_thumbnail_url() : plugin_dir_url(__FILE__) . 'fallback.jpg';

            $output .= '<svg class="card__image" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1920 500" preserveAspectRatio="xMidYMid slice">';
            $output .= '<defs>';
            $output .= '<clipPath id="' . $clipPathID . '">';
            $output .= '<circle class="clip" cx="960" cy="250" r="992"></circle>';
            $output .= '</clipPath>';
            $output .= '</defs>';
            $output .= '<image clip-path="url(#' . $clipPathID . ')" width="1920px" height="500" xlink:href="' . esc_url($image_url) . '"></image>';
            $output .= '</svg>';

            // Post Content
            $output .= '<div class="card__content">';
            $output .= '<i class="card__btn-close fa fa-times"></i>';
            $output .= '<div class="card__caption">';
            $output .= '<h2 class="card__title">' . get_the_title() . '</h2>';
           
            $output .= '</div>';
            $output .= '<div class="card__copy">';
            $output .= '<div class="meta">';
            $output .= get_avatar(get_the_author_meta('ID'), 32);
            $output .= '<span class="meta__author">' . get_the_author() . '</span>';
            $output .= '<span class="meta__date">' . get_the_date() . '</span>';
            $output .= '</div>';
            $output .= get_the_content();
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            $count++; // Increment the counter for the next iteration

        endwhile;

        // Reset post data
        wp_reset_postdata();
    else :
        // No posts found
        $output .= '<p>No posts found.</p>';
    endif;

    // End of HTML Output
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';

    // Return the HTML output
    return $output;
}

// Register the shortcode
add_shortcode('wp_expandable_grid', 'wp_expandable_grid_shortcode');
