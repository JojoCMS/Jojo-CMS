$(document).ready(function() {
    var d = new Date();
    $('.contact-form').each(function(index) {
        if ($(this).attr('id').length >0) {
            var formid = $(this).attr('id');
            var uploads = $(this).find('.fileupload');
            var options = {
                target:        '#' + formid + 'message',   // target element(s) to be updated with server response
                beforeSubmit:  preFlight,  // pre-submit callback
                uploadProgress: function(event, position, total, percentComplete) {
                    if (uploads.length>0) {
                        var percentVal = percentComplete + '%';
                        $('#' + formid + ' .progress').show();
                        $('#' + formid + ' .progress .bar').width(percentVal)
                        $('#' + formid + ' .progress .percent').html(percentVal);
                    }
                },
                success:       showResponse,  // post-submit callback
                url:       'json/jojo_contact_send.php?_='+d.getTime(),        // override for form's 'action' attribute
                //type:      type        // 'get' or 'post', override for form's 'method' attribute
                dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type)
                //clearForm: true,       // clear all form fields after successful submit
                //resetForm: true        // reset the form after successful submit

                // $.ajax options can be used here too, for example:
                error: function(){
                    
                    var submitEl = $('#' + formid + '.submit .button');
                    submitEl.removeAttr('disabled').val(submitEl.data('normalval'));
                    $('#' + formid + 'message').show().html('There has been a failure to communicate. Your request has been stored however and will be attended to shortly');
                }
            };
            $('#' + formid).validate({
             errorElement: 'span',
             submitHandler: function(form) {
               $(form).ajaxSubmit(options);
               return false;
             }
            });
        }
        // Store the button's default value for use when submitting
        var submitEl = $('.submit .button', this);
        submitEl.data('normalval', submitEl.val());
        if ($(this).attr('id').length >0 && $("fieldset", this).length>1 && $(this).hasClass('multipage')) {
            setFormTabs($(this).attr('id'));
        }
    });
});

// pre-submit callback
function preFlight(formData, jqForm, options) {
    // jqForm is a jQuery object encapsulating the form element.  To access the
    // DOM element for the form do this:
    // var formElement = jqForm[0];
    var formID = $(jqForm[0]['form_id']).val();

    // here we could return false to prevent the form from being submitted;
    // returning anything other than false will allow the form submit to continue
    if (!$('#form' + formID).valid()) {
        $('#form' + formID).validate( {
            errorElement: 'span'
        });
        return false;
    }
    // Disable and re-label the button to provide a subtle visual
    // indicator that the form is being processed.
    $('#form' + formID + ' .submit .button')
        .attr('disabled', 'disabled')
        .val('Loading...');
    return true;
}

// post-submit callback
function showResponse(response)  {
    var formid = response.id;
    $('#' + formid + 'message').show().html(response.responsemessage);
    if (response.sent==true) {
        $('#' + formid).clearForm();
        if (response.hideonsuccess==true) {
            $('#' + formid).hide();
        }
    }
    // Revert the button back to a usable state
    var submitEl = $('#' + formid + ' .submit .button');
    submitEl.removeAttr('disabled').val(submitEl.data('normalval'));
}

// Tab navigation functions

function showFormTab(formid, tabid) {
    $('#' + formid + ' fieldset').hide();
    $('#' + formid + ' #' + tabid).show();
    $('#' + formid + ' .tabswitch a').removeClass('current');
    setFormTabNav(formid, tabid);
}

function setFormTabs(formid) {
    var fieldsets=$('#' + formid + ' fieldset');
    var numtabs = $(fieldsets).length;
    var formlinks = '';
    var tabscript = '';
    var tabid = '';
    $(fieldsets).each(function(index) {
        tabscript = "showFormTab('" + formid + "','" + $(this).attr('id') + "');$('#" + formid + $(this).attr('id') + "link').addClass('current');";
        if (index==(numtabs -1)) {
            tabscript += "$('#" + formid + " div.submit').show();";
        }
        tabscript += "return false;"
        formlinks += '<a href="" id="' + formid + $(this).attr('id') + 'link" onclick="' + tabscript + '"';
        if (index==0) {
            formlinks += ' class="current"';
            tabid = $(this).attr('id');
        } else if (index==(numtabs -1)) {
            formlinks += ' class="last"';
        }
        if ($("legend", this).html()) {
            formlinks += ' title="' + $("legend", this).html() + '"><span>' + $("legend", this).html() + '</span></a>';
        } else {
            formlinks += '></a>';
        }
    });

    $(fieldsets).hide();
    $('#' + formid + ' fieldset:first-child').show().before('<div class="tabswitch">' + formlinks + '</div>');
    $('#' + formid + ' .form-submit.endonly').hide();
    $('#' + formid + ' .form-submit').before('<div class="tabnav"><a class="prev" href="" style="display:none;"><span>&lt; Back</span></a><a class="next" href=""><span>Next &gt;</span></a></div>');
    setFormTabNav(formid, tabid);
}

function setFormTabNav(formid, tabid) {
    var fieldsets=$('#' + formid + ' fieldset');
    var backscript = '';
    var nextscript = '';
    var prevtabid = '';
    var nexttabid = '';
    var next = false;
    var numtabs = $(fieldsets).length;
    var v = $('#' + formid).validate({onsubmit:false});
    $(fieldsets).each(function(index) {
        if ($(this).attr('id')==tabid) {
            next = true;
        } else if (next==true) {
            nexttabid = $(this).attr('id');
            return false;
        } else {
            prevtabid = $(this).attr('id');
        }
    });
    if (prevtabid.length>0) {
        $('#' + formid + ' a.prev').show().click(function(){
            showFormTab(formid,prevtabid);
            $('#' + formid + prevtabid + 'link').addClass('current');
            return false;
        });
    } else {
        $('#' + formid + ' a.prev').hide();
    }
    if (nexttabid.length>0) {
        $('#' + formid + ' a.next').show().click(function(){
            if (v.form()) {
                showFormTab(formid,nexttabid);
                $('#' + formid + nexttabid + 'link').addClass('current');
            }
            return false;
        });
    } else {
        $('#' + formid + ' a.next').hide();
    }
    if (tabid==$(fieldsets).last().attr('id')) {
        $('#' + formid + ' .form-submit').show();
    }
}
