//Highlight search terms
function searchTermHighlighter(){
    var theSearchTerm = document.URL.split('?s=')[1];
    if ( typeof theSearchTerm !== 'undefined' ){
        theSearchTerm = theSearchTerm.replace(/\+/g, ' ').replace(/\%20/g, ' ').replace(/\%22/g, '');
        jQuery('article .entry-title a, article .entry-summary').each(function(i){
            var searchFinder = jQuery(this).text().replace(new RegExp( '(' + preg_quote(theSearchTerm) + ')' , 'gi' ), '<span class="searchresultword">$1</span>');
            jQuery(this).html(searchFinder);
        });
    }
    function preg_quote(str){
        return (str + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
    }
}
 
//Emphasize the search Terms
function emphasizeSearchTerms(){
    var theSearchTerm = get('s');
    if ( typeof theSearchTerm !== 'undefined' ){
        var origBGColor = jQuery('.searchresultword').css('background-color');
        jQuery('.searchresultword').each(function(i){
            var stallFor = 150 * parseInt(i);
            jQuery(this).delay(stallFor).animate({
                backgroundColor: 'rgba(255, 255, 0, 0.5)',
                borderColor: 'rgba(255, 255, 0, 1)',
            }, 500, 'swing', function(){
                jQuery(this).delay(1000).animate({
                    backgroundColor: origBGColor,
                }, 1000, 'swing', function(){
                    jQuery(this).addClass('transitionable');
                });
            });
        });
    }
}
