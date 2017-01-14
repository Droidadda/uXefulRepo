//Search Keywords
function keywordSearch(container, parent, value, filteredClass){
    if ( !filteredClass ){
        var filteredClass = 'filtereditem';
    }
    jQuery(container).find("*:not(:Contains(" + value + "))").parents(parent).addClass(filteredClass);
    jQuery(container).find("*:Contains(" + value + ")").parents(parent).removeClass(filteredClass);
}
