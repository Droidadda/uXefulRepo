jQuery('div.textlines').delay(800).stop().animate(
    { top: 0 },
    { width: 300 },
    { queue: false, duration: 2000, easing: "easeOutBack" }
);
    
    
jQuery('div.textlines').delay(800).animate({
    top: [ "0", "easeOutBack" ],
    opacity: "1",
}, 2000, "swing" );
   
   
//Animation can add distances to pre-defined properties
jQuery('p').animate({
    left: '+=90px',
    top: '+=150px',
    opacity: 0.25
    }, 900, 'linear', function() {
    // function code on animation complete
});
   
   
//Animation as a function with a passed object (oThis could just as easily be a jQuery selector)
function navOut(oThis) {
    oThis.css('display', 'none').css('top', '-100px').removeClass('hidden'); //Prep the element for animation
    oThis.delay(800).stop().animate(
        { top: -100, opacity: 0 },
        { queue: false, duration: 500, easing: "easeOutBack", complete: function(){
            oThis.css('display', 'block').addClass('hidden').css('top', '0');
        }
    });
}
   
   
//Sample animation for fading something in on hover and out on mouse-out using opacity and 'hidden' class
jQuery('.person').hover(function(){
    jQuery(this).find('img.before').css('opacity', '0').removeClass('hidden');
    jQuery(this).find('img.before').stop().animate({
        opacity: "1",
    }, 250, "linear" );
}, function(){
    jQuery(this).find('img.before').stop().animate({
        opacity: "0",
    }, 250, "linear", function() {
        jQuery(this).find('img.before').addClass('hidden');
    });
});
 
//Simultaneous Animations
jQuery('.bgblur').animate(
    { backgroundPositionY: -200 },
    { queue: false, duration: 5000 }
);
jQuery('.nebulalogo .nebula1').delay(500).fadeIn(1500).animate(
    { top: 45 },
    { queue: false, duration: 3000 }
);
 
 
//Easings: https://jqueryui.com/easing/
