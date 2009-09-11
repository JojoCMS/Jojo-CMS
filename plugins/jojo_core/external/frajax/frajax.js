function frajax(action,a,b,c,d,e,f,g,h,i,j) {
    var url  = siteurl;
    var r = /(http|https):\/\/.*/;
    var m = r.exec(window.location);
    if (m != null && m.length > 1) {
    	if (m[1] == 'https') {
    	    url  = secureurl;
    	}
    }
    url += '/actions/' + action + '.php?';
    if (a) {url += 'arg1=' + encodeURIComponent(a);}
    if (b) {url += '&arg2=' + encodeURIComponent(b);}
    if (c) {url += '&arg3=' + encodeURIComponent(c);}
    if (d) {url += '&arg4=' + encodeURIComponent(d);}
    if (e) {url += '&arg5=' + encodeURIComponent(e);}
    if (f) {url += '&arg6=' + encodeURIComponent(f);}
    if (g) {url += '&arg7=' + encodeURIComponent(g);}
    if (h) {url += '&arg8=' + encodeURIComponent(h);}
    if (i) {url += '&arg9=' + encodeURIComponent(i);}
    if (j) {url += '&arg10=' + encodeURIComponent(j);}
    document.getElementById('frajax-iframe').src = url;
    return false;
}
