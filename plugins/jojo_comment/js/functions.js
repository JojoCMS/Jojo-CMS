/* Javascript validation for submitting comments  */
function checkComment(emailrequired) {
    if (emailrequired == undefined) {
        var emailrequired = true;
    }
    var i = 0;
    var errors = new Array();
    if (document.getElementById('name').value == '') {errors[i++]='Name is a required field';}
    if (emailrequired && (document.getElementById('email').value == '')) {errors[i++]='Email is a required field';}
    else if (emailrequired && !validateEmail(document.getElementById('email').value)) {errors[i++]='Email is not a valid email format';}
    if (document.getElementById('comment').value == '') {errors[i++]='Please enter a comment';}
    if (document.getElementById('captchacode') && (document.getElementById('captchacode').value == '')) {errors[i++]='Please enter the CAPTCHA code (required to prevent spam)';}

    if (errors.length==0) {
        return(true);
    } else {
        alert(errors.join("\n"));
        return(false);
    }
}

function redirectSubmit(url) {
        window.location.href = url;
}

function deleteComment(id) {
        $('#delete-comment-' + id ).html('Deleting comment...');
       data=new Array();
       data = {name: 'delete', value: id };
        $.get(this.href, {}, function() {commentUpdate(data, id); });
        /* Don't go to the update page */
        $('#comment-wrap-' + id ).html(data);
        return false;
}

function nofollowComment(id, yes) {
       data=new Array();
       if (yes==1) {
            data = {name: 'nofollow', value: id };
        } else {
            data = {name: 'follow', value: id };
        }
        $.get(this.href, {}, function() {commentUpdate(data, id); });
        /* Don't go to the update page */
        return false;
}

function anchorComment(id, yes) {
       data=new Array();
       if (yes==1) {
            data = {name: 'anchor', value: id };
        } else {
            data = {name: 'noanchor', value: id };
        }
        $.get(this.href, {}, function() {commentUpdate(data, id); });
        /* Don't go to the update page */
        return false;
}

function  commentUpdate(data, id) {
    $.post('json/jojo_comment_update.php', data,
            function(data) {
                $('#comment-wrap-' + id).html(data);
            }, "json");
         
}
