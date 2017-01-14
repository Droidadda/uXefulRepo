/*==========================
 Using functions to animate objects in Flash.
 ===========================*/
 
 //With a function name
setTimeout(AAA001, 4500);
function AAA001(){
    ride_mc._alpha = 100;   
    new Tween(white_mc, "_alpha", Regular.easeInOut, 50, 85, 1, true);
    new Tween(ride_mc, "_xscale", Regular.easeInOut, 100, 65, 1, true);
    new Tween(ride_mc, "_yscale", Regular.easeInOut, 100, 65, 1, true);
    new Tween(ride_mc, "_y", Regular.easeInOut, 125, 90, 1, true);
}
 
 
//With an anonymous function
setTimeout(function(){
    logo_mc._alpha = 100;
    date_mc._alpha = 100;
    new Tween(logo_mc, "_y", Strong.easeOut, -64, 0.05, 1, true);
    new Tween(whiteOverlay_mc, "_y", Strong.easeOut, 350, 310, 1, true);
    new Tween(date_mc, "_y", Strong.easeOut, 350, 310, 1, true);
    new Tween(bgphoto, "_x", Regular.easeInOut, 23, 150, 1, true);
    new Tween(bgphoto, "_xScale", Regular.easeInOut, 100, 65, 1, true);
    new Tween(bgphoto, "_yScale", Regular.easeInOut, 100, 65, 1, true);
}, 8000);
 
 
 
//Easings
Strong
Back
Elastic
Regular
Bounce
 
easeIn
easeOut
easeInOut
 
None.easeNone
