//Check Nebula for updated version of this...

//Get query string parameters
function getQueryStrings(){
    queries = {};
    var q = document.URL.split('?')[1];
    if ( q ){
        q = q.split('&');
        for ( var i = 0; i < q.length; i++ ){
            hash = q[i].split('=');
            if ( hash[1] ){
                queries[hash[0]] = hash[1];
            } else {
                queries[hash[0]] = true;
            }
        }
    }
}
 
//Search query strings for the passed parameter
function get(query){
    if ( !query ){
        return queries;
    } else {
        return queries[query];
    }
    return false;
}
