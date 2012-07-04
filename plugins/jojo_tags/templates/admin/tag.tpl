<input type="text" id="tag-searchfm_{$fd_field}" autocomplete="off" onchange="return false;" onkeydown="return (event.which != 13);" onkeyup="return tagSearchKeyPressfm_{$fd_field}(event);" />
<button id="tag-search-button" onclick="addTagfm_{$fd_field}($('#tag-searchfm_{$fd_field}').val()); $('#tag-search').val('').focus(); return false;">Add</button>
<div class="clear"></div>

<div style="width:400px; margin-right: 20px; float: left;">
<textarea rows="8" cols="53" name="fm_{$fd_field}" id="fm_{$fd_field}" onchange="getRelatedTagsfm_{$fd_field}()">{if $taglist}{$taglist}{/if}</textarea>
<p class="note">Separate each tag with a space: <em>cameraphone urban moblog</em>. Or to join 2 words together in one tag, use double quotes: <em>&quot;daily commute&quot;</em>.</p>
</div>

<div style="width: 200px; float: left;">
    <div id="suggestionsfm_{$fd_field}">
        <strong>Suggestions</strong><br />
    </div>
    <div id="related-tagsfm_{$fd_field}">
        <strong>Related tags</strong><br />
    </div>
</div>

<script type="text/javascript">/* <![CDATA[ */
{literal}

function suggestTag{/literal}fm_{$fd_field}{literal}(tag) {
    return '<a href="" onclick="addTag{/literal}fm_{$fd_field}{literal}(\''+tag+'\'); return false;">'+tag+'</a><br />'
}

function addTag{/literal}fm_{$fd_field}{literal}(tag) {
    var v = $('#{/literal}fm_{$fd_field}{literal}').val();
    v = v.replace( /^\s*/, "" ).replace( /\s*$/, "" ); //trim WS
    $('#{/literal}fm_{$fd_field}{literal}').val(v+' "'+tag+'"');
    getRelatedTags{/literal}fm_{$fd_field}{literal}();
    return false;
}

function getTagSuggestions{/literal}fm_{$fd_field}{literal}() {
    $.getJSON('json/get-tag-ideas.php', { search: $('#tag-search{/literal}fm_{$fd_field}{literal}').val() }, function(json) {
        $('#suggestions{/literal}fm_{$fd_field}{literal}').html('<strong>Suggestions</strong><br />');
        for (var i = 0; i < json.length; i++) {
            $('#suggestions{/literal}fm_{$fd_field}{literal}').append(suggestTag{/literal}fm_{$fd_field}{literal}(json[i].tg_tag));
        }
    });
    return true;
}

function tagSearchKeyPress{/literal}fm_{$fd_field}{literal}(e) {
    return (e.which == 13) ? addTag($('#tag-search').val()) : getTagSuggestions{/literal}fm_{$fd_field}{literal}();
}

function getRelatedTags{/literal}fm_{$fd_field}{literal}() {
    $.getJSON('json/get-related-tags.php', { related: $('#{/literal}fm_{$fd_field}{literal}').val() }, function(json){
        $('#related-tags{/literal}fm_{$fd_field}{literal}').html('<strong>Related tags</strong><br />');
        for (var i=0; i < json.length; i++) {
            $('#related-tags{/literal}fm_{$fd_field}{literal}').append(suggestTag{/literal}fm_{$fd_field}{literal}(json[i].tg_tag));
        }
    });
}


{/literal}
/* ]]> */</script>