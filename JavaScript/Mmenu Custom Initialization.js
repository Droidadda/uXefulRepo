function mmenus(){
    if ( 'mmenu' in jQuery ){
        var mobileNav = jQuery('#mobilenav');
        var mobileNavTriggerIcon = jQuery('a.mobilenavtrigger i');
 
        if ( mobileNav.is('*') ){
            mobileNav.mmenu({
                //Options
                offCanvas: {
                    position: "left", //"left" (default), "right", "top", "bottom"
                    zposition: "back", //"back" (default), "front", "next"
                },
                navbars: [{
                    position: "top",
                    content: ["searchfield"]
                }, {
                    position: "bottom",
                    content: ["<span>" + nebula.site.name + "</span>"]
                }],
                searchfield: { //This is for searching through the menu itself (NOT for site search, but Nebula enables site search capabilities for this input)
                    add: true,
                    search: true,
                    placeholder: 'Search',
                    noResults: "No navigation items found.",
                    showSubPanels: false,
                    showTextItems: false,
                },
                counters: true, //Display count of sub-menus
                iconPanels: false, //Layer panels on top of each other
                extensions: ["theme-light", "effect-slide-menu", "pageshadow"] //Theming, effects, and other extensions
            }, {
                //Configuration
                classNames: {
                    selected: "current-menu-item"
                }
            });
 
            if ( mobileNav.length ){
                mobileNav.data('mmenu').bind('opening', function(){
                    //When mmenu has started opening
                    mobileNavTriggerIcon.removeClass('fa-bars').addClass('fa-times').parents('.mobilenavtrigger').addClass('active');
                    nebulaTimer('mmenu', 'start');
                }).bind('opened', function(){
                    //After mmenu has finished opening
                    history.replaceState(null, document.title, location);
                    history.pushState(null, document.title, location);
                }).bind('closing', function(){
                    //When mmenu has started closing
                    mobileNavTriggerIcon.removeClass('fa-times').addClass('fa-bars').parents('.mobilenavtrigger').removeClass('active');
                    ga('send', 'timing', 'Mmenu', 'Closed', Math.round(nebulaTimer('mmenu', 'lap')), 'From opening mmenu until closing mmenu');
                }).bind('closed', function(){
                    //After mmenu has finished closing
                });
            }
 
            nebula.dom.document.on('click tap touch', '.mm-menu li a:not(.mm-next)', function(){
                ga('send', 'timing', 'Mmenu', 'Navigated', Math.round(nebulaTimer('mmenu', 'lap')), 'From opening mmenu until navigation');
            });
 
 
            var mmenuSearchInput = jQuery('.mm-search input');
            mmenuSearchInput.wrap('<form method="get" action="' + nebula.site.home_url + '"></form>').attr('name', 's');
            mmenuSearchInput.on('keyup', function(){
                if ( jQuery(this).val().length > 0 ){
                    jQuery('.clearsearch').removeClass('hidden');
                } else {
                    jQuery('.clearsearch').addClass('hidden');
                }
            });
            jQuery('.mm-panel').append('<div class="clearsearch hidden"><strong class="doasitesearch">Press enter to search the site!</strong><br /><a href="#"><i class="fa fa-times-circle"></i>Reset Search</a></div>');
            nebula.dom.document.on('click touch tap', '.clearsearch a', function(){
                mmenuSearchInput.val('').keyup();
                jQuery('.clearsearch').addClass('hidden');
                return false;
            });
 
            //Close mmenu on back button click
            if (window.history && window.history.pushState){
                window.addEventListener("popstate", function(e){
                    if ( jQuery('html.mm-opened').is('*') ){
                        mobileNav.data('mmenu').close();
                        e.stopPropagation();
                    }
                }, false);
            }
        }
    }
}
