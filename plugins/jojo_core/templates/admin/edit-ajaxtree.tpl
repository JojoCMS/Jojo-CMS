    <link rel="stylesheet" type="text/css" href="external/jstree/dist/themes/default/style.min.css" />
    <script type="text/javascript" src="external/jstree/dist/jstree.min.js"></script>

    {if $searchable}<input id="treediv_q" class="input" type="text" value="" />
    {/if}
    <div id="treediv" class="treediv" style="min-height:400px; overflow-x:auto; border-right:1px solid #eeeeee;"></div>
    <script type="text/javascript">{literal}
        var canLoad = true;
        $(function() {
            $("#treediv").jstree({
                'core' : {
                    'data' : {
                        'url' : siteurl + "/json/admin-edit-nodes.php?table={/literal}{$table}{literal}",
                        'data' : function (node) {
                          return { 'id' : (node.id ? node.id : 0) };
                        },
                        'dataType' : 'json'
                    }
                },
                "types" : {
                    "file" : {
                      "icon" : "glyphicon glyphicon-file",
                      "valid_children" : []
                    }
                },
               'search' : { 'fuzzy' : false, 'show_only_matches' : false },
                {/literal}'plugins' : [{if $draggable} "dnd",{/if}{if $searchable} "search",{/if} "state", "wholerow", "types" ]{literal}
            });
             $("#treediv").bind('select_node.jstree', function (e, data) {
                node = data.instance.get_node(data.selected[0], true);
                if (canLoad && !node.hasClass('locked') && node.attr('id')) {
                    frajax('load', '{/literal}{$table}{literal}', node.attr('id'));
                }
                return false;
            });

            $("#treediv").bind('move_node.jstree', function (e, data) {
                if (data.args[0].p == "before") {
                    /* Before an existing node */
                    $.post('json/admin-edit-move.php',
                           {table: '{/literal}{$table}{literal}', id: $(data.args[0].p).attr('id'), newParent: $(data.args[0].r).attr('parentid'), order: data.args[0].cp}
                           );
                } else if (data.args[0].p == "after") {
                    /* After an existing node */
                    $.post('json/admin-edit-move.php',
                           {table: '{/literal}{$table}{literal}', id: $(data.args[0].p).attr('id'), newParent: $(data.args[0].r).attr('parentid'), order: data.args[0].cp}
                           );
                }
                return false;
            });
{/literal}{if $searchable}{literal}
            var to = false;
            $('#treediv_q').keyup(function () {
                if(to) { clearTimeout(to); }
                to = setTimeout(function () {
                  var v = $('#treediv_q').val();
                  $('#treediv').jstree(true).search(v);
                }, 250);
            });
{/literal}{/if}{literal}
       });
    {/literal}</script>
{if $draggable}<em style="color: #aaa; font-style: italic">Drag-drop to reorder</em>{/if}