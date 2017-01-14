//Custom CSS expression for a case-insensitive contains(). Source: https://css-tricks.com/snippets/jquery/make-jquery-contains-case-insensitive/
//Call it with :Contains() - Ex: ...find("*:Contains(" + jQuery('.something').val() + ")")... -or- use the nebula function: keywordSearch(container, parent, value);
jQuery.expr[":"].Contains=function(e,n,t){return(e.textContent||e.innerText||"").toUpperCase().indexOf(t[3].toUpperCase())>=0};
