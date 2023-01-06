document.addEventListener("DOMContentLoaded", function() {

    // listen for changes in our carbon fields UI
    var mutationObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            svbogo_UpdateAdminButtons();
        });
    });
    mutationObserver.observe(jQuery('#carbon_fields_container_buy_one_get_one .cf-field__body')[0], {
        childList: true,
        subtree: true,
    });
   
    // update buttons on page load
    svbogo_UpdateAdminButtons();
    
    // update titles of offers to be more descriptive and add disabled classes where applicable
    var $offers = jQuery('.cf-complex__group');
    $offers.each(function() {
    
        // update title and add product links
        var $triggerProductEl = jQuery(this).find('select[name*="[_trigger_product_id]"]');
        var $freeProductEl = jQuery(this).find('select[name*="[_free_product_id]"]');
        var triggerProductLink = '<a href="/wp-admin/post.php?post=' + $triggerProductEl.val() + '&action=edit">' + $triggerProductEl.find('option:selected').text() + '</a>';
        var freeProductLink = '<a href="/wp-admin/post.php?post=' + $freeProductEl.val() + '&action=edit">' + $freeProductEl.find('option:selected').text() + '</a>';
        jQuery(this).find('.cf-complex__group-title').html('Buy one ' + triggerProductLink + ' get one ' + freeProductLink);
        
        // add disabled class to any disabled offers
        if (jQuery(this).find('input[type="checkbox"][name*="[_inactive]"]').val() == 'yes') {
            jQuery(this).addClass('sv-offer-disabled');
            jQuery(this).find('.cf-complex__group-index').addClass('sv-offer-disabled');
            jQuery(this).find('.cf-complex__group-title').prepend('[DISABLED] ');
        } else {
            // set status colour
            jQuery(this).find('.cf-complex__group-index').addClass('sv-offer-active');
        }

    });

});

function svbogo_UpdateAdminButtons() {
    setTimeout(function() {
        // improve UI with extra save button and better labels
        var $offerButton = jQuery("#carbon_fields_container_buy_one_get_one button.button.cf-complex__inserter-button:contains('Add Offer')");
        // check if we've already altered the buttons before changing
        if (!$offerButton.text().includes('Add another +')) {
            $offerButton.html('Add another +');
            $offerButton.before('<input type="submit" value="Save all" name="publish" class="sv-bogo-button button button-primary button-large"></input>');
        }
    }, 50);
}