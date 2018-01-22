//Check Nebula for updated version of this...

//Get query string parameters
function getQueryStrings(url){
	if ( !url ){
		url = document.URL;
	}

	var queries = {};
	var queryString = url.split('?')[1];

	if ( queryString ){
		queryStrings = queryString.split('&');
		for ( var i = 0; i < queryStrings.length; i++ ){
			hash = queryStrings[i].split('=');
			if ( hash[1] ){
				 queries[hash[0]] = hash[1];
			} else {
				 queries[hash[0]] = true;
			}
		}
	}

	return queries;
}
 
//Search query strings for the passed parameter
function get(parameter, url){
	var queries = getQueryStrings(url);

	if ( !parameter ){
		return queries;
	}

	return queries[parameter] || false;
}
