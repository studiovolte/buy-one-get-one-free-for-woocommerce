document.addEventListener("DOMContentLoaded", function() {
    // ensure remove link is not present for bogo items (some of them slip through the woocommerce_cart_item_remove_link filter)
    var $bogoItems = jQuery('.woocommerce-cart-form__cart-item a:contains("(Buy one get one offer)")');
    $bogoItems.closest('.woocommerce-cart-form__cart-item').find('.product-remove a').remove();
});