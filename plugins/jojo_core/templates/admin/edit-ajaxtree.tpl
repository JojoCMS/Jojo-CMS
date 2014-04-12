    {if $searchable}<input id="treediv_q" class="input" type="text" value="" />
    {/if}
    <div id="treediv" class="treediv"></div>
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
                        'dataType' : 'json',
                    },
                    "check_callback" : true
               },
                "types" : {
                     "default" : {
                    },
                     "#" : {
                    },
                   "folder" : {
                      "icon" : "glyphicon glyphicon-folder-open",
                    },
                   "file" : {
                      "icon" : "glyphicon glyphicon-file",
                    }
                },{/literal}{if $searchable} 
                'search' : {ldelim} 'fuzzy' : false, 'show_only_matches' : false {rdelim}, {/if}
                'plugins' : [{if $draggable} "dnd",{/if}{if $searchable} "search",{/if} "state", "wholerow", "types" ]{literal}
            });
             $("#treediv").bind('select_node.jstree', function (e, data) {
                node = data.instance.get_node(data.selected[0], true);
                if (canLoad && !node.hasClass('locked') && node.attr('id')) {
                    frajax('load', '{/literal}{$table}{literal}', node.attr('id'));
                }
                return false;
            });

            $("#treediv").bind('move_node.jstree', function (e, data) {
                newparent = data.parent=='#' ? 0 : data.parent;
                $.ajax({
                    type: "POST",
                    url : siteurl + '/json/admin-edit-move.php',
                    data : {table: '{/literal}{$table}{literal}', id: data.node.id, newParent: data.parent, order: data.position},
                    success : function () {
                        frajax('load', '{/literal}{$table}{literal}', data.node.id);
                    },
                    dataType: 'text'
                });

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