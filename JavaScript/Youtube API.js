//Nebula definitely has an updated version of this.

//Check for Youtube Videos
function checkForYoutubeVideos(){
    if ( jQuery('.youtubeplayer').length ){
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }
}
function onYouTubeIframeAPIReady(e){
    if ( typeof players === 'undefined' ){
        players = {
            youtube: {},
            vimeo: {},
        };
        videoData = {};
    }
    jQuery('iframe.youtubeplayer').each(function(i){
        var youtubeiframeID = jQuery(this).attr('id');
        players.youtube[youtubeiframeID] = new YT.Player(youtubeiframeID, {
            events: {
                onReady: onPlayerReady,
                onStateChange: onPlayerStateChange,
                onError: onPlayerError
            }
        });
    });
    pauseFlag = false;
}
function onPlayerError(e){
    var videoTitle = e['target']['B']['videoData']['title'];
    ga('set', gaCustomDimensions['timestamp'], localTimestamp());
    ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Youtube Error'));
    ga('send', 'event', 'Error', 'Youtube API', videoTitle + ' (Code: ' + e.data + ')', {'nonInteraction': 1});
}
function onPlayerReady(e){
    if ( typeof videoProgress === 'undefined' ){
        videoProgress = {};
    }
 
    var id = e['target']['f']['id'];
    videoData[id] = {
        platform: 'youtube', //The platform the video is hosted using.
        player: players.youtube[id], //The player ID of this video. Can access the API here.
        duration: e['target']['B']['duration'], //The total duration of the video. Unit: Seconds
        current: e['target']['B']['currentTime'], //The current position of the video. Units: Seconds
        percent: e['target']['B']['currentTime']/e['target']['B']['duration'], //The percent of the current position. Multiply by 100 for actual percent.
        engaged: false, //Whether the viewer has watched enough of the video to be considered engaged.
        watched: 0, //Amount of time watching the video (regardless of seeking). Accurate to half a second. Units: Seconds
        watchedPercent: 0, //The decimal percentage of the video watched. Multiply by 100 for actual percent.
    };
}
function onPlayerStateChange(e){
    var videoTitle = e['target']['B']['videoData']['title'];
    var id = e['target']['f']['id'];
 
    videoData[id].current = e['target']['B']['currentTime'];
    videoData[id].percent = e['target']['B']['currentTime']/e['target']['B']['duration'];
 
    if ( e.data === YT.PlayerState.PLAYING ){
        ga('set', gaCustomMetrics['videoStarts'], 1);
        ga('set', gaCustomDimensions['videoWatcher'], 'Started');
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        ga('send', 'event', 'Videos', 'Play', videoTitle);
        nebulaConversion('videos', 'Youtube Played: ' + videoTitle);
        pauseFlag = true;
 
        youtubePlayProgress = setInterval(function(){
            videoData[id].current = e['target']['B']['currentTime'];
            videoData[id].percent = e['target']['B']['currentTime']/e['target']['B']['duration'];
            videoData[id].watched = videoData[id].watched+.5; //Must match the interval in seconds!
            videoData[id].watchedPercent = (videoData[id].watched)/e['target']['B']['duration'];
 
            if ( videoData[id].watchedPercent > 0.25 && !videoData[id].engaged ){
                ga('set', gaCustomDimensions['videoWatcher'], 'Engaged');
                ga('send', 'event', 'Videos', 'Engaged', videoTitle, {'nonInteraction': 1});
                nebulaConversion('videos', 'Youtube Engaged: ' + videoTitle);
                videoData[id].engaged = true;
            }
        }, 500);
    }
    if ( e.data === YT.PlayerState.ENDED ){
        clearTimeout(youtubePlayProgress);
        ga('set', gaCustomMetrics['videoCompletions'], 1);
        ga('set', gaCustomMetrics['videoPlaytime'], Math.round(videoData[id].watched/1000));
        ga('set', gaCustomDimensions['videoWatcher'], 'Finished');
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        ga('send', 'event', 'Videos', 'Finished', videoTitle, {'nonInteraction': 1});
        ga('send', 'timing', 'Videos', 'Finished', videoData[id].watched*1000, videoTitle); //Amount of time watched (can exceed video duration).
        nebulaConversion('videos', 'Youtube Finished: ' + videoTitle);
    } else if ( e.data === YT.PlayerState.PAUSED && pauseFlag ){
        clearTimeout(youtubePlayProgress);
        ga('set', gaCustomMetrics['videoPlaytime'], Math.round(videoData[id].watched));
        ga('set', gaCustomDimensions['videoPercentage'], Math.round(videoData[id].percent*100));
        ga('set', gaCustomDimensions['videoWatcher'], 'Paused');
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        ga('send', 'event', 'Videos', 'Pause', videoTitle);
        ga('send', 'timing', 'Videos', 'Paused (Watched)', videoData[id].watched*1000, videoTitle); //Amount of time watched, not the timestamp of when paused!
        nebulaConversion('videos', 'Youtube Paused: ' + videoTitle);
        pauseFlag = false;
    }
}
