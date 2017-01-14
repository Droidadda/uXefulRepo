//Embedded function
document.getElementById("outside").addEventListener("click", function(){
    var t2 = document.getElementById("t2");
    if ( t2.firstChild.nodeValue == "three" ){
      t2.firstChild.nodeValue = "two";
    } else {
      t2.firstChild.nodeValue = "three";
    }
});
 
//Detect Click and Touchend
document.getElementById("outside").addEventListener("click", swapText);
document.getElementById("outside").addEventListener("touchend", swapText);
function swapText(){
    var t2 = document.getElementById("t2");
    if ( t2.firstChild.nodeValue == "three" ){
      t2.firstChild.nodeValue = "two";
    } else {
      t2.firstChild.nodeValue = "three";
    }
}
