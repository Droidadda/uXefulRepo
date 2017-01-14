//Social sharing buttons
function socialSharing(){
    var loc = window.location;
    var title = nebula.dom.document.attr('title');
    var encloc = encodeURI(loc);
    var enctitle = encodeURI(title);
    jQuery('.fbshare').attr('href', 'http://www.facebook.com/sharer.php?u=' + encloc + '&t=' + enctitle).attr('target', '_blank').on('click tap touch', function(){
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('send', 'event', 'Social', 'Share', 'Facebook');
    });
    jQuery('.twshare').attr('href', 'https://twitter.com/intent/tweet?text=' + enctitle + '&url=' + encloc).attr('target', '_blank').on('click tap touch', function(){
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('send', 'event', 'Social', 'Share', 'Twitter');
    });
    jQuery('.gshare').attr('href', 'https://plus.google.com/share?url=' + encloc).attr('target', '_blank').on('click tap touch', function(){
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('send', 'event', 'Social', 'Share', 'Google+');
    });
    jQuery('.lishare').attr('href', 'http://www.linkedin.com/shareArticle?mini=true&url=' + encloc + '&title=' + enctitle).attr('target', '_blank').on('click tap touch', function(){
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('send', 'event', 'Social', 'Share', 'LinkedIn');
    });
    jQuery('.emshare').attr('href', 'mailto:?subject=' + title + '&body=' + loc).attr('target', '_blank').on('click tap touch', function(){
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('send', 'event', 'Social', 'Share', 'Email');
    });
}
