<?php

if( !defined('ABSPATH') )
{
    exit; // Exit if accessed directly
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'svbogo_load_carbon_fields');
add_action('carbon_fields_register_fields', 'svbogo_create_options_page');

function svbogo_load_carbon_fields()
{
    \Carbon_Fields\Carbon_Fields::boot();
}

function svbogo_create_options_page()
{

    // Get woocommerce products
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1
    );
    $products = get_posts($args);

    // generate simple key/value array with product ids and names
    $productNames = [];
    foreach ($products as $product) {
        $productNames[$product->ID] = $product->post_title;
    }

    $offerLabels = [
        'plural_name' => 'Offers',
        'singular_name' => 'Offer',
    ];

    if (empty($products)) {

        // Just show an error if there are no woocommerce products
        Container::make('theme_options', __('Buy One Get One'))
            ->set_page_menu_position(30)
            ->set_icon('dashicons-cart')
            ->add_fields(array(
                Field::make( 'html', 'crb_html', 'Buy One Get One Error' )
                ->set_html('<div class="error notice"><p>Please create at least one WooCommerce product before using <strong>' . SV_BOGO_PLUGIN_NAME . '</strong></p></div><p><a href="/wp-admin/post-new.php?post_type=product">Add product</a></p>')
        ));

    } else {

        // show offers form
        Container::make('theme_options', __('Buy One Get One'))
            ->set_page_menu_position(30)
            ->set_icon('dashicons-cart')
            ->add_fields(array(
                Field::make( 'complex', 'svbogo_offers', __( 'Buy One Get One Offers' ) )
                    ->setup_labels($offerLabels)
                    ->add_fields('offer', array(
                        Field::make( 'checkbox', 'inactive', __( 'Disabled' ) ),
                        // TODO support variations
                        Field::make( 'select', 'trigger_product_id', __( 'Trigger Product' ) )
                        ->set_options($productNames)->set_help_text('This is the product that will trigger the BOGO offer when a customer adds it to their cart'),
                        Field::make( 'select', 'free_product_id', __( 'Free Product' ) )
                        ->set_options($productNames)->set_help_text('This is the product that the customer will get for free'),
                        Field::make( 'select', 'limit', __( 'Limit' ) )
                        ->set_options([
                            1 => 1,
                            2 => 2,
                            3 => 3,
                            4 => 4,
                            5 => 5,
                            10 => 10,
                            20 => 20,
                            50 => 50,
                            100 => 100,
                            false => 'No limit'
                        ])->set_help_text('How many times should the customer be able to receive the free product per transaction')
                    ))
            ));

    }

}
