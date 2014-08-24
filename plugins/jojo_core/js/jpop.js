/* content (a jquery object), width (default 600px), height (default 500px), padding (default 10px) */
function jpop(c,w,h,p) {
  if (w==null) {var w=600;}
  if (h==null) {var h=500;}
  if (p==null) {var p=10;}
  $('#jpop_loading').show();
  $('#jpop_overlay').show();
  var pagesize = jpop_getPageSize();
  var x = (pagesize[0]/2)-(w/2)-p;
  var y = (pagesize[1]/2)-(h/2)-p;
  $('select').addClass('jpop_select');
  c.addClass("jpop_content").css("width",w).css("height",h).css("padding",p).css("left",x).css("top",y).show();
  $('#jpop_loading').hide();
  jpopOnClose(function(){});
  return false;
}

function jpopOnClose(fn) {
  $("#jpop_overlay").unbind('click');
  $("#jpop_overlay").click(function(){$(this,"#jpop_loading").hide();$(".jpop_content").hide();$('select.jpop_select').removeClass('jpop_select');});
  $("#jpop_overlay").click(fn);
}

function jpop_getPageSize(){
  var de = document.documentElement;
  var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
  var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
  arrayPageSize = [w,h];
  return arrayPageSize;
}

/* things to do after document has loaded */
var jPopAppended = false;
$(document).ready(function(){
    if(!jPopAppended) {
        $("body").append("<div id='jpop_loading'><img src='images/loading.gif' alt='loading' /></div><div id='jpop_overlay'></div>");
        jPopAppended = true;
    }
});