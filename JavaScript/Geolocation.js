//Request Geolocation
function requestPosition(){
    var nav = null;
    if (nav === null){
        nav = window.navigator;
    }
    var geoloc = nav.geolocation;
    if (geoloc != null){
        geoloc.getCurrentPosition(successCallback, errorCallback, {enableHighAccuracy: true}); //One-time location poll
        //geoloc.watchPosition(successCallback, errorCallback, {enableHighAccuracy: true}); //Continuous location poll (This will update the nebula.session.geolocation object regularly, but be careful sending events to GA- may result in TONS of events)
    }
}
 
//Geolocation Success
function successCallback(position){
    nebula.session.geolocation = {
        error: false,
        coordinates: { //A value in decimal degrees to an precision of 4 decimal places is precise to 11.132 meters at the equator. A value in decimal degrees to 5 decimal places is precise to 1.1132 meter at the equator.
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
        },
        accuracy: {
            meters: position.coords.accuracy,
            miles: (position.coords.accuracy*0.000621371).toFixed(2),
        },
        altitude: { //Above the mean sea level
            meters: position.coords.altitude,
            miles: (position.coords.altitude*0.000621371).toFixed(2),
            accuracy: position.coords.altitudeAccuracy,
        },
        speed: {
            mps: position.coords.speed,
            kph: (position.coords.speed*3.6).toFixed(2),
            mph: (position.coords.speed*2.23694).toFixed(2),
        },
        heading: position.coords.heading, //Degrees clockwise from North
    }
 
    if ( nebula.session.geolocation.accuracy.meters < 50 ){
        nebula.session.geolocation.accuracy.color = '#00bb00';
        ga('set', gaCustomDimensions['geoAccuracy'], 'Excellent (<50m)');
    } else if ( nebula.session.geolocation.accuracy.meters > 50 && nebula.session.geolocation.accuracy.meters < 300 ){
        nebula.session.geolocation.accuracy.color = '#a4ed00';
        ga('set', gaCustomDimensions['geoAccuracy'], 'Good (50m - 300m)');
    } else if ( nebula.session.geolocation.accuracy.meters > 300 && nebula.session.geolocation.accuracy.meters < 1500 ){
        nebula.session.geolocation.accuracy.color = '#ffc600';
        ga('set', gaCustomDimensions['geoAccuracy'], 'Poor (300m - 1500m)');
    } else {
        nebula.session.geolocation.accuracy.color = '#ff1900';
        ga('set', gaCustomDimensions['geoAccuracy'], 'Very Poor (>1500m)');
    }
 
    nebula.dom.document.trigger('geolocationSuccess');
    nebula.dom.body.addClass('geo-latlng-' + nebula.session.geolocation.coordinates.latitude.toFixed(4).replace('.', '_') + '_' + nebula.session.geolocation.coordinates.longitude.toFixed(4).replace('.', '_') + ' geo-acc-' + nebula.session.geolocation.accuracy.meters.toFixed(0).replace('.', ''));
    debugInfo();
    ga('set', gaCustomDimensions['geolocation'], nebula.session.geolocation.coordinates.latitude.toFixed(4) + ', ' + nebula.session.geolocation.coordinates.longitude.toFixed(4));
    ga('set', gaCustomDimensions['timestamp'], localTimestamp());
    ga('send', 'event', 'Geolocation', nebula.session.geolocation.coordinates.latitude.toFixed(4) + ', ' + nebula.session.geolocation.coordinates.longitude.toFixed(4), 'Accuracy: ' + nebula.session.geolocation.accuracy.meters.toFixed(2) + ' meters');
}
 
//Geolocation Error
function errorCallback(error){
    switch (error.code){
        case error.PERMISSION_DENIED:
            geolocationErrorMessage = 'Access to your location is turned off. Change your settings to report location data.';
            var geoErrorNote = 'Denied';
            break;
        case error.POSITION_UNAVAILABLE:
            geolocationErrorMessage = "Data from location services is currently unavailable.";
            var geoErrorNote = 'Unavailable';
            break;
        case error.TIMEOUT:
            geolocationErrorMessage = "Location could not be determined within a specified timeout period.";
            var geoErrorNote = 'Timeout';
            break;
        default:
            geolocationErrorMessage = "An unknown error has occurred.";
            var geoErrorNote = 'Error';
            break;
    }
 
    nebula.session.geolocation = {
        error: {
            code: error.code,
            description: geolocationErrorMessage
        }
    }
 
    nebula.dom.document.trigger('geolocationError');
    nebula.dom.body.addClass('geo-error');
    debugInfo();
    ga('set', gaCustomDimensions['geolocation'], geolocationErrorMessage);
    ga('set', gaCustomDimensions['timestamp'], localTimestamp());
    ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Geolocation Error (' + geoErrorNote + ')'));
    ga('send', 'event', 'Geolocation', 'Error', geolocationErrorMessage, {'nonInteraction': 1});
}
