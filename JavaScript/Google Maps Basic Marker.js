jQuery(function(){
  //Load the Google Maps script when needed via JS (or load it with a blocking <script> tag in the HTML)
  if ( jQuery('.googlemap').length ){
		if ( typeof google == "undefined" || !has(google, 'maps') ){ //If the API has not already been called
			jQuery.getScript('https://www.google.com/jsapi?key=' + nebula.site.options.nebula_google_browser_api_key, function(){
			    google.load('maps', '3', {
			        callback: function(){
			        	jQuery(document).trigger('nebula_google_maps_api_loaded');
                myMap(); //Could directly call the function here like this.
			        }
			    });
			}).fail(function(){
			    ga('send', 'event', 'Error', 'JS Error', 'Google Maps JS API script could not be loaded.', {'nonInteraction': true});
			});
		}
	}
});

function myMap(){
	var styledMapType = new google.maps.StyledMapType([{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#d3d3d3"}]},{"featureType":"transit","stylers":[{"color":"#808080"},{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#b3b3b3"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":1.8}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#d7d7d7"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ebebeb"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#a7a7a7"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#efefef"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#696969"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#737373"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#d6d6d6"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#dadada"}]}]);

	var myLocation = {
		lat: 43.1434035,
		lng: -76.1289288
	};

	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: 16,
		center: myLocation
	});

	var marker = new google.maps.Marker({
		position: myLocation,
		icon: 'http://gearside.com/whatever/assets/img/map-marker.png', //Optional custom icon
		map: map
	});
  
  //If stylizing the map
	map.mapTypes.set('styled_map', styledMapType);
	map.setMapTypeId('styled_map');
}
