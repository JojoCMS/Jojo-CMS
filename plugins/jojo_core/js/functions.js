function confirmdelete() {
    if (confirm("Are you sure you want to delete this?") == true) {
        return true;
    } else {
        return false;
    }
}

function xyz(c,a,b,s) {
    var s = (s == null) ? true : s;
    var o = '';
    var m = '';
    var m2 = ':otliam';
    for (i = 0; i <= b.length; i++) {o = b.charAt (i) + o;}
    b = o;
    for (i = 0; i <= m2.length; i++) {m = m2.charAt (i) + m;}
    if (!s) {m = '';}
    return m + a + unescape('%'+'4'+'0') + b + '.' + c;
}

function validate(val,type) {
    if (type=='email'){
        return val.match(/^(?:^[A-Z0-9._%-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|biz|info|name|aero|biz|info|jobs|museum|name)$)$/i);
        //return (val.indexOf(".") > 2) && (val.indexOf("@") > 0);
    }else if(type=='url'){
        return val.match(/^(?:\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])$/i);
    }else if(type=='integer'){
        //var regex = new RegExp ('[^0-9\.]','gi');
        return val.match(/^-?[0-9]+$/i);
        //var newval=parseFloat(val.replace(regex, ""));
        //if (newval != val) {alert('Only numeric input is allowed in this field');}
        //if (isNaN(newval)) {field.value = '';} else {field.value = newval;}
        //document.forms[theform].elements['fm_cost_'+i].value
    }
}

/*-- [validateEmail] --*/
/*-- Returns true or false based on whether an email address is a valid format --*/
function validateEmail(str) {
  var at="@"
  var dot="."
  var lat=str.indexOf(at)
  var lstr=str.length
  var ldot=str.indexOf(dot)
  if (str.indexOf(at)==-1){return false}
  if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){return false}
  if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){return false}
  if (str.indexOf(at,(lat+1))!=-1){return false}
  if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){return false}
  if (str.indexOf(dot,(lat+2))==-1){return false}
  if (str.indexOf(" ")!=-1){return false}
  return true
}

/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}

function isNull(a) {
  return typeof a == 'object' && !a;
}

function nl2br(myString){
  return myString.replace( /\n/g, '<br />\n' );
}

$(document).ready(function(){
  $('input.post_redirect_submit').mouseover(function(){window.status=this.title;return true;});
  $('input.post_redirect_submit').mouseout(function(){window.status='Done';return true;});
});