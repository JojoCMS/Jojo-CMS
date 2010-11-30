    <link rel="stylesheet" type="text/css" href="external/jsTree/themes/default/style.css" />
    <link rel="stylesheet" type="text/css" href="external/jsTree/themes/classic/style.css" />

    <script type="text/javascript" src="external/jsTree/_lib/jquery.cookie.js"></script>
    <script type="text/javascript" src="external/jsTree/_lib/jquery.hotkeys.js"></script>
    <script type="text/javascript" src="external/jsTree/jquery.jstree.js"></script>

    <div id="treediv" class="treediv" style="min-height:400px; overflow-x:auto; border-right:1px solid #eeeeee;"></div>
    <script type="text/javascript">{literal}
        var canLoad = true;
        $(function() {
            $("#treediv").bind('select_node.jstree', function (e, data) {
                if (canLoad && !data.inst.get_selected().hasClass('locked') && data.inst.get_selected().attr('id')) {
                    frajax('load', '{/literal}{$table}{literal}', data.inst.get_selected().attr('id'));
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

            $("#treediv").jstree({
                "json_data" : {
                    "ajax" : {
                        "url" : siteurl + "/json/admin-edit-nodes.php?table={/literal}{$table}{literal}",
                        "data": function (n) {
                            return { id : n.attr ? n.attr("id") : 0 };
                        }
                    }
                },

                "ui" : {
                    "select_limit" : 1
                },

                "themes" : {
                    "theme" : "classic",
                    "dots" : true,
                    "icons" : true
                },
{/literal}{if $draggable}{literal}
                "crrm" : {
                    "move" : {
                        "check_move" : function (m) {
                            var p = this._get_parent(m.o);
                            if(!p) return false;
                            p = p == -1 ? this.get_container() : p;
                            if(p === m.np) return true;
                            if(p[0] && m.np[0] && p[0] === m.np[0]) return true;
                            return false;
                        }
                    }
                },
                "dnd" : {
                    "drop_target" : false,
                    "drag_target" : false
                },
{/literal}{/if}{literal}
                "plugins" : [ "themes", "json_data", "cookies", "ui"{/literal}{if $draggable}{literal}, "dnd", "crrm"{/literal}{/if}{literal} ]
            });
        });
    {/literal}</script>
{if $draggable}<em style="color: #aaa; font-style: italic">Drag-drop to reorder</em>{/if}