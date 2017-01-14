/*==========================
 Mouse Events
 ===========================*/
 
clickTag.addEventListener(MouseEvent.MOUSE_UP, onClick);
function onClick(e:MouseEvent):void {
    nebulaDetection('click');
    setDetectedValues();
    var click_url:String = root.loaderInfo.parameters.clickTAG;
    if ( click_url ) {
        navigateToURL(new URLRequest(click_url), '_blank');
    }
    drawer.test.testText.text = "CLICKED";
}
 
clickTag.addEventListener(MouseEvent.ROLL_OVER, _rollOver);
function _rollOver(e:Event):void {
    setDetectedValues();
    //nebulaDetection('over'); //@TODO: Uncomment this if mouseover tracking is desired. Warning: There could be a ton of events!
    drawer.test.testText.text = "OVER";
    drawer.test.testText.autoSize = TextFieldAutoSize.CENTER;
    new Tween(drawer.test, "scaleX", Back.easeOut, 1, 1.5, .5, true);
    new Tween(drawer.test, "scaleY", Back.easeOut, 1, 1.5, .5, true);
}
 
clickTag.addEventListener(MouseEvent.ROLL_OUT, _rollOut);
function _rollOut(e:Event):void {
    drawer.test.testText.text = "TEST";
    drawer.test.testText.autoSize = TextFieldAutoSize.CENTER;
    new Tween(drawer.test, "scaleX", Back.easeOut, 1.5, 1, .5, true);
    new Tween(drawer.test, "scaleY", Back.easeOut, 1.5, 1, .5, true);
}
