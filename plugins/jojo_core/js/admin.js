$(document).ready(function() {

  $('.empty-cache').click(function(){
    var buttontext = $(this).html();
    $(this).html('Emptying ...');
    var button = $(this);
    var clearscope = button.data('scope');
    $.get( 'json/admin-empty-cache.php', {scope: clearscope}, function(){
        button.html(buttontext);
        if (clearscope == 'full' || clearscope == 'css') {
            $.post( 'css/styles.css');
            $.post( 'css/admin.css');
        }
        if (clearscope == 'full' || clearscope == 'js') {
            $.post( 'js/common.js');
            $.post( 'js/commonadmin.js');
        }
     });
     return false;
  });

});

// insertcode is used for bold, italic, underline and quote and just
// wraps the tags around a selection or prompts the user for some
// text to apply the tag to
function insertcode(tag, desc, textAreaId) {
  var textarea = document.getElementById(textAreaId); //our textfield
  var open = "[" + tag + "]"; //our open tag
  var close = "[/" + tag + "]"; //our close tag

  if(!textarea.setSelectionRange) {
    var selected = document.selection.createRange().text;
    if(selected.length <= 0) {
      textarea.value += open + prompt("Please enter the text you'd like to " + desc, "") + close; //no text was selected so prompt the user for some text
    } else {


      if (tag == 'url') {
        //is the selected text a URL?
        var myregexp = /\b(https?|ftp):\/\/([-A-Z0-9.]+)(\/[-A-Z0-9+&@#\/%=~_|!:,.;]*)?(\?[-A-Z0-9+&@#\/%=~_|!:,.;]*)?/i;
        var match = myregexp.exec(selected);
        if (match != null) {
          result = match[0];
        } else {
          result = "http://";
        }
        var url = prompt("Please enter the address to link to (include http://)", result);
        if ( (url != '') && (url != 'http://') ) {var open = "[" + tag + "="+url+"]";}
      }

      if (tag == 'email') {
        var url = prompt("Please enter the email address to link to", "");
        if (url != '') {var open = "[" + tag + "="+url+"]";}
      }
      // put the code around the selected text
      document.selection.createRange().text = open + selected + close;
    }

  } else {

    if (tag == 'url') {
        //is the selected text a URL?
        var myregexp = /\b(https?|ftp):\/\/([-A-Z0-9.]+)(\/[-A-Z0-9+&@#\/%=~_|!:,.;]*)?(\?[-A-Z0-9+&@#\/%=~_|!:,.;]*)?/i;
        var match = myregexp.exec(textarea.value.substring(textarea.selectionStart, textarea.selectionEnd));
        if (match != null) {
          result = match[0];
        } else {
          result = "http://";
        }
        var url = prompt("Please enter the address to link to (include http://)", result);
        if ( (url != '') && (url != 'http://') ) {var open = "[" + tag + "="+url+"]";}
      }

      if (tag == 'email') {
        var url = prompt("Please enter the email address to link to", "");
        if (url != '') {var open = "[" + tag + "="+url+"]";}
      }

    var pretext = textarea.value.substring(0, textarea.selectionStart); //the text before the selection
    var codetext = open + textarea.value.substring(textarea.selectionStart, textarea.selectionEnd) + close; //the selected text with tags before and after
    var posttext = textarea.value.substring(textarea.selectionEnd, textarea.value.length) //the text after the selection
    // check if there was a selection
    if(codetext == open + close) {
      codetext = open + prompt("Please enter the text you'd like to " + desc, "") + close; //prompt the user
    }
    // update the text field
    textarea.value = pretext + codetext + posttext;
  }
  // set the focus on the text field
  textarea.focus();
}

// function parameters are: field - the string field, count - the field for remaining characters number and max - the maximum number of characters
function LimitTextSize(fieldid, countid, max) {
  if (enforce == '') {
    enforce = true;
  }
  field = document.getElementById(fieldid);
  count = document.getElementById(countid);
  // if the length of the string in the input field is greater than the max value, trim it
  if ( (field.value.length > max) && (enforce == true) )
    field.value = field.value.substring(0, max);
  else
  // calculate the remaining characters
  count.value = max - field.value.length;
}


function setTextEditorContent(id) {
    $('.jTagEditor').val('');
    var f = id.replace(/fm_(.*)/ig, "$1");
    if ($("input[name='editor_"+f+"']:checked").val() == 'bb') {
      //$('#'+id+'_bb').val($('#'+id).val());
      $('textarea[name='+id+'_bb]').val($('#'+id).val());
    } else {
      $('textarea[name='+id+'_html]').val($('#'+id).val());
      if (xinha_editors[id+'_xinha']) {
        xinha_editors[id+'_xinha'].setEditorContent($('#'+id).val());
      }
    }
}


    function center(element){
        try{
            element = $(element);
        }catch(e){
            return;
        }

        var my_width  = 0;
        var my_height = 0;

        if ( typeof( window.innerWidth ) == 'number' ){
            my_width  = window.innerWidth;
            my_height = window.innerHeight;
        }else if ( document.documentElement &&
                 ( document.documentElement.clientWidth ||
                   document.documentElement.clientHeight ) ){
            my_width  = document.documentElement.clientWidth;
            my_height = document.documentElement.clientHeight;
        }
        else if ( document.body &&
                ( document.body.clientWidth || document.body.clientHeight ) ){
            my_width  = document.body.clientWidth;
            my_height = document.body.clientHeight;
        }

        element.style.position = 'absolute';
        element.style.zIndex   = 99;

        var scrollY = 0;

        if ( document.documentElement && document.documentElement.scrollTop ){
            scrollY = document.documentElement.scrollTop;
        }else if ( document.body && document.body.scrollTop ){
            scrollY = document.body.scrollTop;
        }else if ( window.pageYOffset ){
            scrollY = window.pageYOffset;
        }else if ( window.scrollY ){
            scrollY = window.scrollY;
        }

        var elementDimensions = Element.getDimensions(element);

        var setX = ( my_width  - elementDimensions.width  ) / 2;
        var setY = ( my_height - elementDimensions.height ) / 2 + scrollY;

        setX = ( setX < 0 ) ? 0 : setX;
        setY = ( setY < 0 ) ? 0 : setY;

        element.style.left = setX + "px";
        element.style.top  = setY + "px";

        element.style.display  = 'block';
    }


// function parameters are: field - the string field, count - the field for remaining characters number and max - the maximum number of characters
function countDown(fieldid, countid, max) {
  var field = $('#' + fieldid);
  var count = $('#' + countid);
  count.html(max - field.val().length);
}

/* things to do after document has loaded */
$(document).ready(function(){
  $(".sortabletable").tablesorter({
      widgets: ['zebra'] 
    }); 
  $('[data-toggle="tooltip"]').tooltip();
  $('#fields-wrap form').on('keyup change mousewheel', 'input, select, textarea', function(){
     $('#btn_save').addClass('btn-warning');
    });
});

