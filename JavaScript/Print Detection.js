//Capture Print Intent
    printed = 0;
    var afterPrint = function(){
        if ( printed === 0 ){
            printed = 1;
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['eventIntent'], 'Intent');
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Printed'));
            ga('send', 'event', 'Print', 'Print');
            nebulaConversion('print', true);
        }
    };
    if ( window.matchMedia ){
        var mediaQueryList = window.matchMedia('print');
        if ( mediaQueryList.addListener ){
            mediaQueryList.addListener(function(mql){
                if ( !mql.matches ){
                    afterPrint();
                }
            });
        }
    }
    window.onafterprint = afterPrint;
