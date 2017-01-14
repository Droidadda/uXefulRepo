//Nebula has an updated version of this.

//Column height equalizer
function nebulaEqualize(){
    jQuery('.row.equalize').each(function(){
        var oThis = jQuery(this);
        tallestColumn = 0;
        oThis.children('.columns').css('min-height', '0').each(function(i){
            if ( !jQuery(this).hasClass('no-equalize') ){
                columnHeight = jQuery(this).outerHeight();
                if ( columnHeight > tallestColumn ){
                    tallestColumn = columnHeight;
                }
            }
        });
        oThis.find('.columns').css('min-height', tallestColumn);
    });
}
