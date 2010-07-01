<input type="text" id="tag-search" autocomplete="off" onchange="return false;" onkeydown="return (event.which != 13);" onkeyup="return tagSearchKeyPress(event);" />
<button id="tag-search-button" onclick="addTag($('#tag-search').val()); $('#tag-search').val('').focus(); return false;">Add</button>
<div class="clear"></div>

<div style="width:400px; margin-right: 20px; float: left;">
<textarea rows="8" cols="53" name="fm_{$fd_field}" id="fm_{$fd_field}" onchange="getRelatedTags()">{if $taglist}{$taglist}{/if}</textarea>
<p class="note">Separate each tag with a space: <em>cameraphone urban moblog</em>. Or to join 2 words together in one tag, use double quotes: <em>&quot;daily commute&quot;</em>.</p>
</div>

<div style="width: 200px; float: left;">
    <div id="suggestions">
        <strong>Suggestions</strong><br />
    </div>
    <div id="related-tags">
        <strong>Related tags</strong><br />
    </div>
</div>

<script type="text/javascript">/* <![CDATA[ */
{literal}

function suggestTag(tag) {
    return '<a href="" onclick="addTag(\''+tag+'\'); return false;">'+tag+'</a><br />'
}

function addTag(tag) {
    var v = $('#{/literal}fm_{$fd_field}{literal}').val();
    v = v.replace( /^\s*/, "" ).replace( /\s*$/, "" ); //trim WS
    $('#{/literal}fm_{$fd_field}{literal}').val(v+' "'+tag+'"');
    getRelatedTags();
    return false;
}

function getTagSuggestions() {
    $.getJSON('json/get-tag-ideas.php', { search: $('#tag-search').val() }, function(json) {
        $('#suggestions').html('<strong>Suggestions</strong><br />');
        for (var i = 0; i < json.length; i++) {
            $('#suggestions').append(suggestTag(json[i].tg_tag));
        }
    });
    return true;
}

function tagSearchKeyPress(e) {
    return (e.which == 13) ? addTag($('#tag-search').val()) : getTagSuggestions();
}

function getRelatedTags() {
    $.getJSON('json/get-related-tags.php', { related: $('#{/literal}fm_{$fd_field}{literal}').val() }, function(json){
        $('#related-tags').html('<strong>Related tags</strong><br />');
        for (var i=0; i < json.length; i++) {
            $('#related-tags').append(suggestTag(json[i].tg_tag));
        }
    });
}


{/literal}
/* ]]> */</script>