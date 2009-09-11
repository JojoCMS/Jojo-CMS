
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