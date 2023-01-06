<?php

if( !defined('ABSPATH') )
{
    exit; // Exit if accessed directly
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// The following hooks should cover all times when the cart is displayed or requested
add_action('template_redirect', 'svbogo_trigger_bogo_offers' );
add_action('woocommerce_checkout_update_order_review', 'svbogo_trigger_bogo_offers');
add_action('woocommerce_before_mini_cart_contents', 'svbogo_trigger_bogo_offers');

function svbogo_get_offer_key($offer) {
    return 'sv_bogo_key_' . $offer['trigger_product_id'] . '_' . $offer['free_product_id'];
}

function svbogo_restrict_bogo_quantity($product_quantity, $cart_item_key, $cart_item) {
    if (isset($cart_item['sv_bogo_offer_key'])) {
        return $cart_item['quantity'];
    }
    return $product_quantity;
}
// TODO - improve this. Not sure why this method had to be so overcomplicated and misses some items
function svbogo_restrict_bogo_remove($product_remove, $cart_item_key) {
    foreach (WC()->cart->get_cart() as $cartItem) {
        if ($cartItem['key'] == $cart_item_key && isset($cartItem['sv_bogo_offer_key'])) {
            return '';
        }
    }
    return $product_remove;
}

function svbogo_update_bogo_items($cartItems) {
    foreach($cartItems as $key => $value) {
        add_filter('woocommerce_cart_item_quantity', 'svbogo_restrict_bogo_quantity', $key, $value);
        add_filter('woocommerce_cart_item_remove_link', 'svbogo_restrict_bogo_remove', $key, 2);
    }
}

function svbogo_trigger_bogo_offers()
{

    // clear out all bogo offers before recalculating cart
    foreach (WC()->cart->get_cart() as $cartItem) {
        if (isset($cartItem['sv_bogo_offer_key'])) {
            WC()->cart->remove_cart_item($cartItem['key']);
        }
    }

    $bogoOffers = carbon_get_theme_option('svbogo_offers');

    // Check which active BOGO offers are related to an active cart item
    $activeBogos = [];
    foreach ($bogoOffers as $offer) {
        if (!$offer['inactive']) {
            foreach (WC()->cart->get_cart() as $cartItem) {
                if ($offer['trigger_product_id'] == $cartItem['product_id']) {
                    // increment the amount of times bogo offer will be triggered for this order
                    if (isset($offer['count'])) {
                        $offer['trigger_count'] = (int) $offer['trigger_count'] + (int) $cartItem['quantity'];
                    } else {
                        $offer['trigger_count'] = (int) $cartItem['quantity'];
                    }
                    // ensure we are adhering to the bogo offer limits set by the user
                    if ($offer['limit'] != false && $offer['trigger_count'] > $offer['limit']) {
                        $offer['trigger_count'] = $offer['limit'];
                    }
                    if (!empty($cartItem['variation_id'])) {
                        $offer['variation_id'] = $cartItem['variation_id'];
                    }
                    $activeBogos[] = $offer;
                }
            }
        }
    }

    $cartItems = [];

    // begin triggering bogo offers
    if (!empty($activeBogos)) {
        foreach ($activeBogos as $bogo) {

            $freeProductKey = WC()->cart->add_to_cart(
                $bogo['free_product_id'], // wc product id
                $bogo['trigger_count'], // quantity
                isset($bogo['variation_id']) ? $bogo['variation_id'] : null, // variation id
                null, // variation attributes
                array(
                    'sv_bogo_offer_key' => svbogo_get_offer_key($bogo) // custom data
                )
            );

            $freeProduct = WC()->cart->cart_contents[$freeProductKey];
            $freeProduct['data']->set_price(0);
            $freeProduct['data']->set_name('<small>(Buy one get one offer)</small><br>' . $freeProduct['data']->name);
            $cartItems[$freeProductKey] = $freeProduct;

        }

        svbogo_update_bogo_items($cartItems);

    }
    
}