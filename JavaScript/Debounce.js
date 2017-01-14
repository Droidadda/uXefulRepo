//I think there is a better on in Nebula right now.

//Waits for events to finish before triggering
//Passing immediate triggers the function on the leading edge (instead of the trailing edge).
var debounceTimers = {};
function debounce(callback, wait, uniqueId, immediate){
    if ( !uniqueId ){
        uniqueId = "Don't call this twice without a uniqueId";
    }
 
    var context = this, args = arguments;
    var later = function(){
        debounceTimers[uniqueId] = null;
        if ( !immediate ){
            callback.apply(context, args);
        }
    };
    var callNow = immediate && !debounceTimers[uniqueId];
 
    clearTimeout(debounceTimers[uniqueId]);
    debounceTimers[uniqueId] = setTimeout(later, wait);
    if ( callNow ){
        callback.apply(context, args);
    }
};
