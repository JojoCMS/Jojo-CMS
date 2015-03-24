
//Get all the elements of the given classname of the given tag.
function getElementsByClassName(classname,tag) {
  if(!tag) tag = "*";
  var anchs =  document.getElementsByTagName(tag);
  var total_anchs = anchs.length;
  var regexp = new RegExp('\\b' + classname + '\\b');
  var class_items = new Array()

  for(var i=0;i<total_anchs;i++) { //Go thru all the links seaching for the class name
    var this_item = anchs[i];
    if(regexp.test(this_item.className)) {
      class_items.push(this_item);
    }
  }
  return class_items;
}

// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function getCheckedValue(radioObj) {
    if(!radioObj)
        return "";
    var radioLength = radioObj.length;
    if(radioLength == undefined)
        if(radioObj.checked)
            return radioObj.value;
        else
            return "";
    for(var i = 0; i < radioLength; i++) {
        if(radioObj[i].checked) {
            return radioObj[i].value;
        }
    }
    return "";
}

// set the radio button with the given value as being checked
// do nothing if there are no radio buttons
// if the given value does not exist, all the radio buttons
// are reset to unchecked
function setCheckedValue(radioObj, newValue) {
    if(!radioObj)
        return;
    var radioLength = radioObj.length;
    if(radioLength == undefined) {
        radioObj.checked = (radioObj.value == newValue.toString());
        return;
    }
    for(var i = 0; i < radioLength; i++) {
        radioObj[i].checked = false;
        if(radioObj[i].value == newValue.toString()) {
            radioObj[i].checked = true;
        }
    }
}

function formatAsMoney(mnt) {
    mnt -= 0;
    mnt = (Math.round(mnt*100))/100;
    return (mnt == Math.floor(mnt)) ? mnt + '.00' : ( (mnt*10 == Math.floor(mnt*10)) ? mnt + '0' : mnt);
}

function removeclass(id,c) {
if (document.getElementById(id)) {
  var rep=document.getElementById(id).className.match(' '+c)?' '+c:c;
  document.getElementById(id).className= document.getElementById(id).className.replace(rep,'');
  }
}

function addclass(id,c) {
  if (document.getElementById(id)) {
  removeclass(id,c);
  document.getElementById(id).className += ' '+c;
  }
}

/* same as above, but based on the object, not ID */
function removeClass(object,c) {
if (object) {
  var rep=object.className.match(' '+c)?' '+c:c;
  object.className= object.className.replace(rep,'');
  }
}

function addClass(object,c) {
  if (object) {
  removeClass(object,c);
  object.className += ' '+c;
  }
}

/* For attaching code to events without overwriting existing ones */
function addEvent(obj, evType, fn){
 if (obj.addEventListener){
   obj.addEventListener(evType, fn, false);
   return true;
 } else if (obj.attachEvent){
   var r = obj.attachEvent("on"+evType, fn);
   return r;
 } else {
   return false;
 }
}

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
    }else if(type=='url'){
        return val.match(/^(?:\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])$/i);
    }else if(type=='integer'){
        return val.match(/^-?[0-9]+$/i);
    }
}

/*-- [validateEmail] --*/
/*-- Returns true or false based on whether an email address is a valid format --*/
function validateEmail(str) {
  var at="@";
  var dot=".";
  var lat=str.indexOf(at);
  var lstr=str.length;
  var ldot=str.indexOf(dot);
  if (str.indexOf(at)==-1){return false}
  if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){return false}
  if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){return false}
  if (str.indexOf(at,(lat+1))!=-1){return false}
  if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){return false}
  if (str.indexOf(dot,(lat+2))==-1){return false}
  if (str.indexOf(" ")!=-1){return false}
  return true;
}



$(document).ready(function(){
  $('input.post_redirect_submit').mouseover(function(){window.status=this.title;return true;});
  $('input.post_redirect_submit').mouseout(function(){window.status='Done';return true;});
});