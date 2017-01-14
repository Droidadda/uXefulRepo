//Conditional JS Library Loading
//This could be done better I think (also, it runs too late in the stack).
function conditionalJSLoading(){
 
    //Only load bxslider library on a page that calls bxslider.
    if ( jQuery('.bxslider').is('*') ){
        jQuery.getScript('https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.5/jquery.bxslider.min.js').done(function(){
            bxSlider();
        }).fail(function(){
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
            ga('send', 'event', 'Error', 'JS Error', 'bxSlider could not be loaded.', {'nonInteraction': 1});
        });
        nebulaLoadCSS('https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.5/jquery.bxslider.min.css');
    }
 
    //Only load Chosen library if 'chosen-select' class exists.
    if ( jQuery('.chosen-select').is('*') ){
        jQuery.getScript('https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js').done(function(){
            chosenSelectOptions();
        }).fail(function(){
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
            ga('send', 'event', 'Error', 'JS Error', 'chosen.jquery.min.js could not be loaded.', {'nonInteraction': 1});
        });
        nebulaLoadCSS('https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css');
    }
 
    //Only load dataTables library if dataTables table exists.
    if ( jQuery('.dataTables_wrapper').is('*') ){
        jQuery.getScript('https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.10/js/jquery.dataTables.min.js').done(function(){
            nebulaLoadCSS('https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.10/css/jquery.dataTables.min.css');
            dataTablesActions();
        }).fail(function(){
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
            ga('send', 'event', 'Error', 'JS Error', 'jquery.dataTables.min.js could not be loaded', {'nonInteraction': 1});
        });
 
        //Only load Highlight if dataTables table exists.
        jQuery.getScript(nebula.site.template_directory + '/js/libs/jquery.highlight-5.closure.js').fail(function(){
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
            ga('send', 'event', 'Error', 'JS Error', 'jquery.highlight-5.closure.js could not be loaded.', {'nonInteraction': 1});
        });
    }
 
    if ( jQuery('pre.nebula-code').is('*') || jQuery('pre.nebula-pre').is('*') ){
        nebulaLoadCSS(nebula.site.template_directory + '/stylesheets/css/pre.css');
        nebula_pre();
    }
 
    if ( jQuery('.flag').is('*') ){
        nebulaLoadCSS(nebula.site.template_directory + '/stylesheets/css/flags.css');
    }
}
 
//Dynamically load CSS files using JS
function nebulaLoadCSS(url){
    if ( document.createStyleSheet ){
        try {
            document.createStyleSheet(url);
        } catch(e){
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('CSS Resource Load Error'));
            ga('send', 'event', 'Error', 'CSS Error', url + ' could not be loaded', {'nonInteraction': 1});
        }
    } else {
        var css;
        css = document.createElement('link');
        css.rel = 'stylesheet';
        css.type = 'text/css';
        css.media = "all";
        css.href = url;
        document.getElementsByTagName("head")[0].appendChild(css);
    }
}
