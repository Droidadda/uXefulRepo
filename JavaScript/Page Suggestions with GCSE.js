//Page Suggestions for 404 or no search results pages using Google Custom Search Engine
function pageSuggestion(){
    if ( nebula.dom.body.hasClass('search-no-results') || nebula.dom.body.hasClass('error404') ){
        if ( nebula.site.options.nebula_cse_id != '' && nebula.site.options.nebula_google_browser_api_key != '' ){
            if ( get().length ){
                var queryStrings = get();
            } else {
                var queryStrings = [''];
            }
            var path = window.location.pathname;
            var phrase = decodeURIComponent(path.replace(/\/+/g, ' ').trim()) + ' ' + decodeURIComponent(queryStrings[0].replace(/\+/g, ' ').trim());
            trySearch(phrase);
 
            nebula.dom.document.on('mousedown touch tap', 'a.suggestion', function(e){
                eventIntent = ( e.which >= 2 )? 'Intent' : 'Explicit';
                var suggestedPage = jQuery(this).text();
 
                ga('set', gaCustomDimensions['eventIntent'], eventIntent);
                ga('set', gaCustomMetrics['pageSuggestionsAccepted'], 1);
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Page Suggestion Accepted'));
                ga('send', 'event', 'Page Suggestion', 'Click', 'Suggested Page: ' + suggestedPage);
            });
        }
    }
}
 
function trySearch(phrase){
    var queryParams = {
        cx: nebula.site.options.nebula_cse_id,
        key: nebula.site.options.nebula_google_browser_api_key,
        num: 10,
        q: phrase,
        alt: 'JSON'
    }
    var API_URL = 'https://www.googleapis.com/customsearch/v1?';
 
    // Send the request to the custom search API
    jQuery.getJSON(API_URL, queryParams, function(response){
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        if ( response.items && response.items.length ){
            ga('set', gaCustomMetrics['pageSuggestions'], 1);
            ga('send', 'event', 'Page Suggestion', 'Suggested Page: ' + response.items[0].title, 'Requested URL: ' + window.location, {'nonInteraction': 1});
            showSuggestedPage(response.items[0].title, response.items[0].link);
        } else {
            ga('send', 'event', 'Page Suggestion', 'No Suggestions Found', 'Requested URL: ' + window.location, {'nonInteraction': 1});
        }
    });
}
 
function showSuggestedPage(title, url){
    var hostname = new RegExp(location.host);
    if ( hostname.test(url) ){
        jQuery('.suggestion').attr('href', url).text(title);
        jQuery('#suggestedpage').slideDown();
    }
}
