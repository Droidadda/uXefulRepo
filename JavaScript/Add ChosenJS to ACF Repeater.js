#@TODO: Update this- there is a better listener in Nebula Docs admin.js file!

jQuery(document).on('click', '.add-row-end, .acf-button-add, .add-row-before', function(){
    setTimeout(function(){
        jQuery('#acf-related_resources select').each(function(){
            if ( !jQuery(this).parents('tr').hasClass('row-clone') && !jQuery(this).hasClass('yes-chosen') ){
                jQuery(this).chosen({
                    disable_search_threshold: 5,
                    search_contains: true,
                    no_results_text: "No results found.",
                    allow_single_deselect: true,
                    width: "100%"
                }).addClass('yes-chosen');
            }
        });
    }, 5);
});
