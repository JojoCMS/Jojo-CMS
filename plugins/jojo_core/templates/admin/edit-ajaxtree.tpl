    {if $searchable}<div class="search form-group"><input id="treediv_q" class="input form-control" type="text" value="Search" onfocus="if ($(this).val()=='Search') $(this).val('');" /></div>
    {/if}
    <div id="treediv" class="treediv{if $menutype} {$menutype}{/if}"></div>
    <script type="text/javascript">{literal}
        var canLoad = true;
        $(function() {
            $("#treediv").jstree({
                'core' : {
                    'data' : {
                        'url' : siteurl + "/json/admin-edit-nodes.php?table={/literal}{$table}{literal}",
                        'data' : function (node) {
                          return { 'id' : (node.id ? node.id : '#') };
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
                'search' : {ldelim} 'fuzzy' : false, 'show_only_matches' : true, 'case_sensitive' : false {rdelim}, {/if}
                'plugins' : [{if $draggable} "dnd",{/if}{if $searchable} "search",{/if} "state", "wholerow", "types" ]{literal}
            });
             $("#treediv").bind('select_node.jstree', function (e, data) {
                node = data.instance.get_node(data.selected[0]);
                if (canLoad && node.type=='file' && node.id) {
                    frajax('load', '{/literal}{$table}{literal}', node.id);
                } else if (node.type=='folder') {
                    if (node.state.opened) {
                        $("#treediv").jstree("close_node", node.id);
                    } else {
                        $("#treediv").jstree("open_node", node.id);
                    }
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
{if $draggable}<p><em style="color: #aaa;">Drag-drop to reorder</em></p>{/if}