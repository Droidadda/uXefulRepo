//Select (and optionally copy) text
function selectText(element, copy, callback){
    if ( typeof element === 'string' ){
        element = jQuery(element)[0];
    } else if ( typeof element === 'object' && element.nodeType !== 1 ){
        element = element[0];
    }
 
    if ( typeof copy === 'function' ){
        callback = copy;
        copy = null;
    }
 
    try {
        if ( document.body.createTextRange ){
            var range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
            if ( !copy && callback ){
                callback(true);
                return false;
            }
        } else if ( window.getSelection ){
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
            if ( !copy && callback ){
                callback(true);
                return false;
            }
        }
    } catch(err){
        if ( callback ){
            callback(false);
            return false;
        }
    }
 
    if ( copy ){
        try {
            var success = document.execCommand('copy');
            if ( callback ){
                callback(success);
                return false;
            }
        } catch(err){
            if ( callback ){
                callback(false);
                return false;
            }
        }
    }
 
    if ( callback ){
        callback(false);
    }
    return false;
}
