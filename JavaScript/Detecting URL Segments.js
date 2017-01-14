oLocation = jQuery(location);
thisDomain = oLocation.attr('hostname').match(/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/);
 
if ( thisDomain ){ //NULL if local file
    thisURL = { //https://www.gearside.com:8082/index.php#tab2?foo=789
        host: oLocation.attr('host'), //www.gearside.com:8082
        hostname: oLocation.attr('hostname'), //www.gearside.com
        domain: thisDomain[0], //gearside.com
        sld: thisDomain[0].substr(0, thisDomain[0].indexOf('.')), //gearside
        tld: thisDomain[0].substr(thisDomain[0].indexOf('.')), //.com
        port: oLocation.attr('port'), //8082
        protocol: oLocation.attr('protocol'), //https:
        pathname: oLocation.attr('pathname'), //index.php
        href: oLocation.attr('href'), //https://www.gearside.com:8082/index.php#tab2
        hash: oLocation.attr('hash'), //#tab2
        search: oLocation.attr('search'), //?foo=789
    }
}
