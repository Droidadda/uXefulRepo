//Word copy tracking
var copyCount = 0;
var copyOver = 0;
nebula.dom.document.on('cut copy', function(){
    copyCount++;
    var words = [];
    var selection = window.getSelection() + '';
    words = selection.split(' ');
    wordsLength = words.length;
    ga('set', gaCustomDimensions['timestamp'], localTimestamp());
 
    //Track Email or Phone copies as contact intent.
    emailPhone = jQuery.trim(words.join(' '));
    if ( regexPattern.email.test(emailPhone) ){
        ga('set', gaCustomDimensions['contactMethod'], 'Mailto');
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Mailto'));
        ga('send', 'event', 'Contact', 'Copied email: ' + emailPhone);
        nebulaConversion('contact', 'Email (copied): ' + emailPhone);
    } else if ( regexPattern.phone.test(emailPhone) ){
        ga('set', gaCustomDimensions['contactMethod'], 'Click-to-Call');
        ga('set', gaCustomDimensions['eventIntent'], 'Intent');
        ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Click-to-Call'));
        ga('send', 'event', 'Click-to-Call', 'Copied phone: ' + emailPhone);
        nebulaConversion('contact', 'Phone (copied): ' + emailPhone);
    }
 
    if ( copyCount < 13 ){
        if ( words.length > 8 ){
            words = words.slice(0, 8).join(' ');
            ga('send', 'event', 'Copied Text', words + '... [' + wordsLength + ' words]');
        } else {
            if ( selection === '' || selection === ' ' ){
                ga('send', 'event', 'Copied Text', '[0 words]');
            } else {
                ga('send', 'event', 'Copied Text', selection);
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Copied Text'));
            }
        }
    } else {
        if ( copyOver === 0 ){
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Many Copies'));
            ga('send', 'event', 'Copied Text', '[Copy limit reached]');
        }
        copyOver = 1;
    }
});
