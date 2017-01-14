//Nebula definitely has a more recent version of this...

//Detect scroll depth for engagement and more accurate bounce rate
function scrollDepth(){
    var headerHeight = ( jQuery('#header').is('*') )? jQuery('#header').height() : 250;
    var entryContent = jQuery('.entry-content');
 
    //Flags
    var timer = 0;
    var maxScroll = -1;
    var isScroller = false;
    var isReader = false;
    var endContent = false;
    var endPage = false;
 
    //Reading time calculations
    var startTime = new Date();
    var beginning = startTime.getTime();
    var totalTime = 0;
 
    nebula.dom.window.on('scroll', function(){
        if ( !isScroller ){
            currentTime = new Date();
            initialScroll = currentTime.getTime();
            delayBeforeInitial = (initialScroll-beginning)/1000;
 
            ga('send', 'timing', 'Scroll Depth', 'Initial scroll', Math.round(delayBeforeInitial*1000), 'Delay after pageload until initial scroll');
            isScroller = true;
        }
 
        //Calculate max scroll percent
        scrollPercent = Math.round((nebula.dom.window.scrollTop()/(nebula.dom.document.height()-nebula.dom.window.height()))*100);
        if ( scrollPercent > maxScroll ){
            maxScroll = scrollPercent;
            ga('set', gaCustomDimensions['maxScroll'], maxScroll + '%'); //Don't send an event here- this is only needed when another event is triggered.
        }
 
        if ( timer ){
            clearTimeout(timer);
        }
        timer = setTimeout(scrollLocation, 100); //Use a buffer so we don't call scrollLocation too often.
    });
 
    //Check the scroll location
    function scrollLocation(){
        viewportBottom = nebula.dom.window.height()+nebula.dom.window.scrollTop();
        documentHeight = nebula.dom.document.height();
 
        //When the user scrolls past the header
        var becomesReaderAt = ( entryContent.is('*') )? entryContent.offset().top : headerHeight;
        if ( viewportBottom >= becomesReaderAt && !isReader ){
            currentTime = new Date();
            readStartTime = currentTime.getTime();
            timeToScroll = (readStartTime-initialScroll)/1000;
 
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
 
            //This next line (event) is the line that alters Bounce Rate.
            //This line allows bounce rate to be calculated for individual page engagement.
            //To use the more traditional definition of bounce rate as a "Page Depth" engagement metric remove this line (or add a non-interaction object).
            ga('send', 'event', 'Scroll Depth', 'Began reading', Math.round(timeToScroll) + ' seconds (since initial scroll) [Signifies non-bounce visit]'); //This line alters bounce rate in Google Analytics.
            ga('send', 'timing', 'Scroll Depth', 'Began reading', Math.round(timeToScroll*1000), 'Scrolled from top of page to top of entry-content'); //Unless there is a giant header, this timing will likely be 0 on most sites.
            isReader = true;
        }
 
        //When the reader reaches the end of the entry-content
        if ( entryContent.is('*') ){
            if ( viewportBottom >= entryContent.offset().top+entryContent.innerHeight() && !endContent ){
                currentTime = new Date();
                readEndTime = currentTime.getTime();
                readTime = (readEndTime-readStartTime)/1000;
 
                //Set Custom Dimensions
                if ( gaCustomDimensions['scrollDepth'] ){
                    if ( readTime < 10 ){
                        ga('set', gaCustomDimensions['scrollDepth'], 'Previewer');
                    } else if ( readTime < 60 ){
                        ga('set', gaCustomDimensions['scrollDepth'], 'Scanner');
                    } else {
                        ga('set', gaCustomMetrics['engagedReaders'], 1);
                        ga('set', gaCustomDimensions['scrollDepth'], 'Reader');
                        nebulaConversion('engaged', nebula.post.title);
                    }
                }
 
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('send', 'event', 'Scroll Depth', 'Finished reading', Math.round(readTime) + ' seconds (since reading began)');
                ga('send', 'timing', 'Scroll Depth', 'Finished reading', Math.round(readTime*1000), 'Scrolled from top of entry-content to bottom');
                endContent = true;
            }
        }
 
        //If user has hit the bottom of the page
        if ( viewportBottom >= documentHeight && !endPage ){
            currentTime = new Date();
            endTime = currentTime.getTime();
            totalTime = (endTime-readStartTime)/1000;
 
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('send', 'event', 'Scroll Depth', 'Reached bottom of page', Math.round(totalTime) + ' seconds (since pageload)');
            ga('send', 'timing', 'Scroll Depth', 'Reached bottom of page', Math.round(totalTime*1000), 'Scrolled from top of page to bottom');
            endPage = true;
        }
    }
}
