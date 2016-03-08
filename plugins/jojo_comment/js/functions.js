function redirectSubmit(url) {
        window.location.href = 'http://' + url;
}

function editComment(id) {
        $('#comment-wrap-' + id + ' .edit-comment').toggle();
        return false;
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

function saveComment(id) {
       data=new Array();
        var content = $('#comment-wrap-' + id + ' textarea').val();
        data = {name: 'update', value: id, content: content };
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
