$(document).ready(function() {
    $('.contact-form.multipage').each(function(index) {
        if ($(this).attr('id').length >0 && $("fieldset", this).length>1) {
            setFormTabs($(this).attr('id'));
        }
    });
});

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
            showFormTab(formid,nexttabid);
            $('#' + formid + nexttabid + 'link').addClass('current');
            return false;
        });
    } else {
        $('#' + formid + ' a.next').hide();
    }
    if (tabid==$(fieldsets).last().attr('id')) {
        $('#' + formid + ' .form-submit').show();
    }
}
