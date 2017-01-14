function nebulaVibrate(pattern){
    if ( typeof pattern === 'undefined' ){
        pattern = [100, 200, 100, 100, 75, 25, 100, 200, 100, 500, 100, 200, 100, 500];
    } else if ( typeof pattern !== 'object' ){
        pattern = [100, 200, 100, 100, 75, 25, 100, 200, 100, 500, 100, 200, 100, 500];
    }
    if ( checkVibration() ){
        navigator.vibrate(pattern);
    }
    return false;
}
 
function checkVibration(){
    Vibration = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
    if ( !(Vibration) ){
        return false;
    } else {
        return true;
    }
}
