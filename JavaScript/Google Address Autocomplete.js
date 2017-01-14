//Places - Address Autocomplete
function nebulaAddressAutocomplete(autocompleteInput){
    if ( jQuery(autocompleteInput).is('*') ){ //If the addressAutocomplete ID exists
        jQuery.getScript('https://www.google.com/jsapi', function(){
            google.load('maps', '3', {
                other_params: 'libraries=places',
                callback: function(){
                    addressAutocomplete = new google.maps.places.Autocomplete(
                        jQuery(autocompleteInput)[0],
                        {types: ['geocode']} //Restrict the search to geographical location types
                    );
 
                    google.maps.event.addListener(addressAutocomplete, 'place_changed', function(){ //When the user selects an address from the dropdown, populate the address fields in the form.
                        place = addressAutocomplete.getPlace(); //Get the place details from the addressAutocomplete object.
 
                        nebula.user.address = {
                            street: {
                                number: null,
                                name: null,
                                full: null,
                            },
                            city: null,
                            county: null,
                            state: {
                                name: null,
                                abbreviation: null,
                            },
                            country: {
                                name: null,
                                abbreviation: null,
                            },
                            zip: {
                                code: null,
                                suffix: null,
                                full: null,
                            },
                        };
 
                        for ( var i = 0; i < place.address_components.length; i++ ){
                            //Lots of different address types. This function uses only the common ones: https://developers.google.com/maps/documentation/geocoding/#Types
                            switch ( place.address_components[i].types[0] ){
                                case "street_number":
                                    nebula.user.address.street.number = place.address_components[i].short_name; //123
                                    break;
                                case "route":
                                    nebula.user.address.street.name = place.address_components[i].long_name; //Street Name Rd.
                                    break;
                                case "locality":
                                    nebula.user.address.city = place.address_components[i].long_name; //Liverpool
                                    break;
                                case "administrative_area_level_2":
                                    nebula.user.address.county = place.address_components[i].long_name; //Onondaga County
                                    break;
                                case "administrative_area_level_1":
                                    nebula.user.address.state.name = place.address_components[i].long_name; //New York
                                    nebula.user.address.state.abbreviation = place.address_components[i].short_name; //NY
                                    break;
                                case "country":
                                    nebula.user.address.country.name = place.address_components[i].long_name; //United States
                                    nebula.user.address.country.abbreviation = place.address_components[i].short_name; //US
                                    break;
                                case "postal_code":
                                    nebula.user.address.zip.code = place.address_components[i].short_name; //13088
                                    break;
                                case "postal_code_suffix":
                                    nebula.user.address.zip.suffix = place.address_components[i].short_name; //4725
                                    break;
                                default:
                                    //console.log('Address component ' + place.address_components[i].types[0] + ' not used.');
                            }
                        }
                        if ( nebula.user.address.street.number && nebula.user.address.street.name ){
                            nebula.user.address.street.full = nebula.user.address.street.number + ' ' + nebula.user.address.street.name;
                        }
                        if ( nebula.user.address.zip.code && nebula.user.address.zip.suffix ){
                            nebula.user.address.zip.full = nebula.user.address.zip.code + '-' + nebula.user.address.zip.suffix;
                        }
 
                        nebula.dom.document.trigger('nebula_address_selected');
                        ga('set', gaCustomDimensions['contactMethod'], 'Autocomplete Address');
                        ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                        ga('send', 'event', 'Contact', 'Autocomplete Address', nebula.user.address.city + ', ' + nebula.user.address.state.abbreviation + ' ' + nebula.user.address.zip.code);
                    });
 
                    jQuery(autocompleteInput).on('focus', function(){
                        if ( navigator.geolocation ){
                            navigator.geolocation.getCurrentPosition(function(position){ //Bias to the user's geographical location.
                                var geolocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                                var circle = new google.maps.Circle({
                                    center: geolocation,
                                    radius: position.coords.accuracy
                                });
                                addressAutocomplete.setBounds(circle.getBounds());
                            });
                        }
                    }).on('keydown', function(e){
                        if ( e.which === 13 && jQuery('.pac-container:visible').is('*') ){ //Prevent form submission when enter key is pressed while the "Places Autocomplete" container is visbile
                            return false;
                        }
                    });
 
                    if ( autocompleteInput === '#address-autocomplete' ){
                        nebula.dom.document.on('nebula_address_selected', function(){
                            //do any default stuff here.
                        });
                    }
                } //End Google Maps callback
            }); //End Google Maps load
        }).fail(function(){
            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('JS Resource Load Error'));
            ga('send', 'event', 'Error', 'JS Error', 'Google Maps Places script could not be loaded.', {'nonInteraction': 1});
        });
    }
}
