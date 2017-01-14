function nebulaScrollTo(element, milliseconds){
    var headerHtOffset = ( jQuery('.headroom').length )? jQuery('.headroom').outerHeight() : 0; //Note: This selector should be the height of the fixed header, or a hard-coded offset.
 
    //Call this function with a selector to trigger scroll to an element (note: not a selector).
    if ( element ){
        if ( !milliseconds ){
            var milliseconds = 1000;
        }
        jQuery('html, body').animate({
            scrollTop: element.offset().top-headerHtOffset
        }, milliseconds);
        return false;
    }
 
    nebula.dom.document.on('click touch tap', 'a[href^=#]:not([href=#])', function(){ //Using an ID as the href
        if ( jQuery(this).parents('.mm-menu').is('*') ){
            return false;
        }
 
        pOffset = ( jQuery(this).attr('offset') )? parseFloat(jQuery(this).attr('offset')) : 0;
        if ( location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname ){
            var target = jQuery(this.hash);
            target = ( target.length )? target : jQuery('[name=' + this.hash.slice(1) +']');
            if ( target.length ){ //If target exists
                var nOffset = Math.floor(target.offset().top-headerHtOffset+pOffset);
                jQuery('html, body').animate({
                    scrollTop: nOffset
                }, 500);
                return false;
            }
        }
    });
 
    nebula.dom.document.on('click tap touch', '.nebula-scrollto', function(){ //Using the nebula-scrollto class with scrollto attribute.
        pOffset = ( jQuery(this).attr('offset') )? parseFloat(jQuery(this).attr('offset')) : 0;
        if ( jQuery(this).attr('scrollto') ){
            var scrollElement = jQuery(this).attr('scrollto');
            if ( scrollElement != '' ){
                jQuery('html, body').animate({
                    scrollTop: Math.floor(jQuery(scrollElement).offset().top-headerHtOffset+pOffset)
                }, 500);
            }
        }
        return false;
    });
}
