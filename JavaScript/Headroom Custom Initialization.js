//Affix the logo/navigation when scrolling passed it
function initHeadroom(){
    var headerElement = jQuery('#header');
    var footerElement = jQuery('#footer');
    var fixedElement = jQuery('#logonavcon');
 
    if ( typeof fixedElement === 'undefined' || !fixedElement.is('*') ){
        return false;
    }
 
    if ( typeof headerElement === 'undefined' || !headerElement.is('*') ){
        headerElement = nebula.dom.body; //@TODO: If this fallback happens, the padding would need to move to the top.
    }
 
    if ( typeof headroom !== 'undefined' || !window.matchMedia("(min-width: 767px)").matches ){ //If headroom needs to be re-init or if tablet or mobile
        if ( !window.matchMedia("(min-width: 767px)").matches ){
            return false;
        }
 
        headroom.destroy();
    }
 
    var clonedFixedElement = fixedElement.clone().addClass('headroom--not-top').css({position: "absolute", left: "-10000px"}).appendTo('body'); //See the future: Get final height of fixedElement with unknown CSS properties
    var finalBufferSize = clonedFixedElement.outerHeight();
    clonedFixedElement.remove();
 
    window.headroom = new Headroom(fixedElement[0], {
        offset: fixedElement.offset().top, //Vertical offset in px before element is first unpinned
        tolerance: 3, //Scroll tolerance in px before state changes
        classes: {
            initial: "headroom", //When element is initialised
            pinned: "headroom--pinned", //When scrolling up
            unpinned: "headroom--unpinned", //When scrolling down
            top: "headroom--top", //When above offset
            notTop: "headroom--not-top" //When below offset
        },
        onPin: function(){ //Callback when pinned, 'this' is headroom object
            nebula.dom.document.removeClass('headroom--unpinned').addClass('headroom--pinned');
        },
        onUnpin: function(){ //Callback when unpinned, 'this' is headroom object
            nebula.dom.document.removeClass('headroom--pinned').addClass('headroom--unpinned');
        },
        onTop: function(){ //Callback when above offset, 'this' is headroom object
            nebula.dom.document.removeClass('headroom--not-top').addClass('headroom--top');
            headerElement.css('padding-bottom', '0');
        },
        onNotTop: function(){ //Callback when below offset, 'this' is headroom object
            nebula.dom.document.removeClass('headroom--top').addClass('headroom--not-top');
            headerElement.css('padding-bottom', fixedElement.outerHeight()).stop().animate({paddingBottom: finalBufferSize}, 400, "linear"); //Add padding buffer to header and animate (slightly faster than CSS) to finalBufferSize
        },
    });
    headroom.init();
 
    //Custom Nebula Headroom extensions
    nebula.dom.window.on('scroll', function(){
        var viewportBottom = nebula.dom.window.height()+nebula.dom.window.scrollTop();
        var documentHeight = nebula.dom.document.height();
        var scrollDistance = nebula.dom.document.scrollTop();
 
        //Add .headroom--below //@TODO "Nebula" 0: Could this be moved into onNotTop?
        if ( nebula.dom.document.scrollTop() > headerElement.offset().top+headerElement.outerHeight() ){
            fixedElement.addClass('headroom--below');
        } else if ( fixedElement.hasClass('headroom--below') ){
            fixedElement.removeClass('headroom--below');
        }
 
        //Add .headroom-bottom
        if ( viewportBottom >= documentHeight-(footerElement.outerHeight()/2) ){
            fixedElement.addClass('headroom--bottom');
        } else if ( fixedElement.hasClass('headroom--bottom') ){
            fixedElement.removeClass('headroom--bottom');
        }
    });
}
