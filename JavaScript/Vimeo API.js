//Nebula certainly has an updated version of this.

function vimeoControls(){
    //Load the Vimeo API script (froogaloop) remotely (with local backup)
    if ( jQuery('.vimeoplayer').is('*') ){
        jQuery.getScript('https://f.vimeocdn.com/js/froogaloop2.min.js').done(function(){
            createVimeoPlayers();
        }).fail(function(){
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
            ga('send', 'event', 'Error', 'JS Error', 'froogaloop (remote) could not be loaded.', {'nonInteraction': 1});
            jQuery.getScript(nebula.site.template_directory + '/js/libs/froogaloop.min.js').done(function(){
                createVimeoPlayers();
            }).fail(function(){
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
                ga('send', 'event', 'Error', 'JS Error', 'froogaloop (local) could not be loaded.', {'nonInteraction': 1});
            });
        });
    }
 
    //To trigger events on these videos, use the syntax: players['PHG-Overview-Video'].api("play");
    function createVimeoPlayers(){
        if ( typeof players === 'undefined' ){
            players = {
                youtube: {},
                vimeo: {},
            };
            videoData = {};
        }
        jQuery('iframe.vimeoplayer').each(function(i){
            var vimeoiframeID = jQuery(this).attr('id');
            players.vimeo[vimeoiframeID] = $f(vimeoiframeID);
            players.vimeo[vimeoiframeID].addEvent('ready', function(id){
                players.vimeo[id].addEvent('play', vimeoPlay);
                players.vimeo[id].addEvent('pause', vimeoPause);
                players.vimeo[id].addEvent('seek', vimeoSeek);
                players.vimeo[id].addEvent('finish', vimeoFinish);
                players.vimeo[id].addEvent('playProgress', vimeoPlayProgress);
            });
        });
 
        if ( typeof videoProgress === 'undefined' ){
            videoProgress = {};
        }
    }
 
    function vimeoPlay(id){
        var videoTitle = id.replace(/-/g, ' ');
        ga('set', gaCustomMetrics['videoStarts'], 1);
        ga('set', gaCustomDimensions['videoWatcher'], 'Started');
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        ga('send', 'event', 'Videos', 'Play', videoTitle);
        nebulaConversion('videos', 'Vimeo Played: ' + videoTitle);
    }
 
    function vimeoPlayProgress(data, id){
        var videoTitle = id.replace(/-/g, ' ');
 
        if ( typeof videoData[id] === 'undefined' ){
            videoData[id] = {
                platform: 'vimeo', //The platform the video is hosted using.
                player: players.vimeo[id], //The player ID of this video. Can access the API here. Units: Seconds
                duration: data.duration, //The total duration of the video. Units: Seconds
                current: data.seconds, //The current position of the video. Units: Seconds
                percent: data.percent, //The percent of the current position. Multiply by 100 for actual percent.
                engaged: false, //Whether the viewer has watched enough of the video to be considered engaged.
                seeker: false, //Whether the viewer has seeked through the video at least once.
                seen: [], //An array of percentages seen by the viewer. This is to roughly estimate how much was watched.
                watched: 0, //Amount of time watching the video (regardless of seeking). Accurate to 1% of video duration. Units: Seconds
                watchedPercent: 0, //The decimal percentage of the video watched. Multiply by 100 for actual percent.
            };
        } else {
            videoData[id].duration = data.duration;
            videoData[id].current = data.seconds;
            videoData[id].percent = data.percent;
 
            //Determine watched percent by adding current percents to an array, then count the array!
            nowSeen = Math.ceil(data.percent*100);
            if ( videoData[id].seen.indexOf(nowSeen) < 0 ){
                videoData[id].seen.push(nowSeen);
            }
            videoData[id].watchedPercent = videoData[id].seen.length;
            videoData[id].watched = (videoData[id].seen.length/100)*videoData[id].duration; //Roughly calculate time watched based on percent seen
        }
 
        if ( videoData[id].watchedPercent > 25 && !videoData[id].engaged ){
            ga('set', gaCustomDimensions['videoWatcher'], 'Engaged');
            ga('send', 'event', 'Videos', 'Engaged', videoTitle, {'nonInteraction': 1});
            nebulaConversion('videos', 'Vimeo Engaged: ' + videoTitle);
            videoData[id].engaged = true;
        }
    }
 
    function vimeoPause(id){
        var videoTitle = id.replace(/-/g, ' ');
        ga('set', gaCustomDimensions['videoWatcher'], 'Paused');
        ga('set', gaCustomMetrics['videoPlaytime'], Math.round(videoData[id].watched));
        ga('set', gaCustomDimensions['videoPercentage'], Math.round(videoData[id].percent*100));
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        ga('send', 'event', 'Videos', 'Pause', videoTitle);
        ga('send', 'timing', 'Videos', 'Paused (Watched)', Math.round(videoData[id].watched*1000), videoTitle); //Roughly amount of time watched, not the timestamp of when paused!
        nebulaConversion('videos', 'Vimeo Paused: ' + videoTitle);
    }
 
    function vimeoSeek(data, id){
        var videoTitle = id.replace(/-/g, ' ');
        ga('set', gaCustomDimensions['videoWatcher'], 'Seeker');
        ga('send', 'event', 'Videos', 'Seek', videoTitle + ' [to: ' + data.seconds + ']');
        nebulaConversion('videos', 'Vimeo Seeked: ' + videoTitle);
        videoData[id].seeker = true;
    }
 
    function vimeoFinish(id){
        var videoTitle = id.replace(/-/g, ' ');
        ga('set', gaCustomMetrics['videoCompletions'], 1);
        ga('set', gaCustomMetrics['videoPlaytime'], Math.round(videoData[id].watched));
        ga('set', gaCustomDimensions['videoWatcher'], 'Finished');
        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
        ga('send', 'event', 'Videos', 'Finished', videoTitle, {'nonInteraction': 1});
        ga('send', 'timing', 'Videos', 'Finished', Math.round(videoData[id].watched*1000), videoTitle); //Roughly amount of time watched (Can not be over 100% for Vimeo)
        nebulaConversion('videos', 'Vimeo Finished: ' + videoTitle);
    }
}
