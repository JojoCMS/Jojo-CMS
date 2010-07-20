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
