<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class hktree
{
    var $name;                    //Name us ised to uniquely identify this tree within the site. Will set cookie names etc

    var $nodes = array();         //All nodes listed here out of order, with full data
    var $urls = array();          //Array of URLs for links
    var $statuses = array();      //Array of node statuses - '' is normal, or expired, protected
    var $targets = array();       //Array of Targets for links
    var $classname = array();     //Array of additional classes to add to the item
    var $onclicks = array();      //Array of OnClick Events
    var $children = array();      //The children for each node listed here
    var $bulletlist;
    var $parentnode = array();
    var $liststyletype = '';      //Deprecated
    var $liststyle = 'circle';    //The type of list style to use on plain lists
    var $listclass = '';          //The class use on plain lists
    var $selected = '';           //Selected item in select lists
    var $disabled = '';           //item in select list that should be disabled (if item has children, they get disabled to)

    var $plus = '<b>+</b>';       //image or string to use for closed elements
    var $minus = '<b>-</b>';      //image or string to use for opened elements
    var $nokids = '';             //image or string to use for elements with no kids
    var $autoclose = true;        //When a node is opened, it automatically closes other open nodes to keep the tree compact
    var $treeformat = 'windows';  // "windows" means a standard looking treemenu. "custom" means apply your own styles.
    var $showicons = true;
    var $theme = 'light';         //light or dark - changes the graphics to suit the background

    var $indent0;                 //String to use for each indent level (desireable if you style out the bullets)
    var $indent1;
    var $indent2;
    var $indent3;
    var $indent4;
    var $indent5;

    //internal variables - don't use these
    var $path = array();         //path of parent IDs to current position
    var $pathcopy = array();     //used for temporary operations
    var $depth;
    var $showdepth;              //You may want to hide items below a certain depth - default = show all items

    function hktree($name = '')
    {
        $this->name = $name;
        $depth = 0;
    }

    function addNode($id=0, $parent=0, $name = '', $url = '', $target = '', $onclick = '', $classname = '', $rollover = '', $html = '', $status = '')
    {
        $parent = str_replace("'", '', $parent);
        $parent = str_replace('"', '', $parent);
        $id = str_replace(array("'", '"'), '',  $id);
        $this->nodes[$id]                = $name;
        $this->htmls[$id]                = $html;
        $this->statuses[$id]             = $status;
        $this->urls[$id]                 = $url;
        $this->targets[$id]              = $target;
        $this->classname[$id]            = $classname;
        $this->rollovers[$id]            = $rollover;
        $this->onclicks[$id]             = $onclick;
        $this->children[$parent][]       = $id;
        $this->parentnode[$id]['parent'] = $parent;
        if ( ($onclick != '') && ($url == '') ) {$url = '#';} //If onclick event is used, we must have at least # for the URL
    }

    function recursivePath($start = 0, $path = '')
    {
        $keys = array_keys($this->nodes);
        $results = array();
        $results["$start "] = $path . '/' . $start;
        if (isset($this->children[$start]) && is_array($this->children[$start])) {
            foreach ($this->children[$start] as $id) {
                $results = array_merge($results, $this->recursivePath($id, $path . '/' . $start));
            }
        }
        return $results;
    }

    /* used for bulk edit functionality */
    function displaynode_array($start=0)
    {

        if ( ($start!='0') && ($start!='') ) {
            //$this->bulletlist .= "<li>";


            $this->bulletlist[]= array('id'=>$start,'depth'=>($this->depth-1));//$this->nodes[$start];

        }
        if (isset($this->children[$start]) && is_array($this->children[$start])) {
            $this->depth = $this->depth + 1;
            for ($i=0;$i<count($this->children[$start]);$i++) {
                $this->displaynode_array($this->children[$start][$i]);
            }
            $this->depth = $this->depth - 1;
        }

    }


    function displaynode_plain($start=0)
    {

        if ( ($start!='0') && ($start!='') ) {
            $this->bulletlist .= "<li>";
            $class = $this->classname[$start] == '' ? '' : ' class="' . $this->classname[$start].'"';
            $url = $this->urls[$start];
            if ($this->onclicks[$start] != '') {$onclick = ' onclick="' . $this->onclicks[$start].'"';} else {$onclick = "";}
            if ($url != '') {$this->bulletlist .= "<a href=\"$url\"" . $onclick.$class . ">";} //start link
            $this->bulletlist .= $this->nodes[$start];
            if ($url != '') {$this->bulletlist .= "</a>";} //finish link
        }
        if (isset($this->children[$start]) && is_array($this->children[$start])) {
            $this->depth = $this->depth + 1;
            $this->bulletlist .= "<ul";
            $this->bulletlist .= ($this->listclass) ? ' class="' . $this->listclass . '"' : '';
            $this->bulletlist .= ($this->liststyle) ? ' style="list-style-type: ' . $this->liststyle . ';"' : '';
            $this->bulletlist .= ">\n";
            for ($i=0;$i<count($this->children[$start]);$i++) {
                $this->displaynode_plain($this->children[$start][$i]);
            }
            $this->bulletlist .= "</ul>\n";
            $this->depth = $this->depth - 1;
        }
        if ( ($start!='0') && ($start!='') ) {
            $this->bulletlist .= "</li>\n";
        }
    }

    function displaynode_h3list($start=0,$h=3)
    {
        if ( ($start!='0') && ($start!='') ) {
            $this->bulletlist .= ($this->depth == 1) ? "<h$h>" : "<li>";
            $class = $this->classname[$start] == '' ? '' : ' class="' . $this->classname[$start].'"';
            $url = $this->urls[$start];
            if ($this->onclicks[$start] != '') {$onclick = ' onclick="' . $this->onclicks[$start].'"';} else {$onclick = "";}
            if ($url != '') {$this->bulletlist .= "<a href=\"$url\"" . $onclick.$class . ">";} //start link
            $this->bulletlist .= $this->htmls[$start] ? $this->htmls[$start] : $this->nodes[$start];
            if ($url != '') {$this->bulletlist .= "</a>";} //finish link
            $this->bulletlist .= ($this->depth == 1) ? "</h$h>" : "";
        }
        if (isset($this->children[$start]) && is_array($this->children[$start])) {
            $this->depth = $this->depth + 1;
            if ($this->depth > 1) {
                $this->bulletlist .= "<ul";
                $this->bulletlist .= ($this->listclass) ? ' class="' . $this->listclass . '"' : '';
                $this->bulletlist .= ($this->liststyle) ? ' style="list-style-type: ' . $this->liststyle . ';"' : '';
                $this->bulletlist .= ">\n";
            }
            for ($i=0;$i<count($this->children[$start]);$i++) {
                $this->displaynode_h3list($this->children[$start][$i],$h);
            }
            if ($this->depth > 1) $this->bulletlist .= "</ul>\n";
            $this->depth = $this->depth - 1;
        }
        if ( ($start!='0') && ($start!='') ) {
            //$this->bulletlist .= "</li>\n";
            $this->bulletlist .= ($this->depth == 1) ? "" : "</li>";
        }
    }

    function displaynode_select($start=0,$disable=false)
    {
        $indent = '&nbsp;&nbsp;&nbsp;';

        $class = array();
        if (isset($this->statuses[$start]) && ($this->statuses[$start] == 'expired')) {
            $class[] = 'expired';
        }

        if (substr($start, 0, 1) == 'c') {
            $class[] = 'category';
        }

        if (($this->disabled == $start || $disable) && $start!=0 && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            // on IE: use optgroup to show disabled options in select box (disabled attr doesn't work with IE)
            $this->bulletlist .= "<optgroup label=\"";
            $this->bulletlist .= str_repeat($indent, max(0, $this->depth - 1));
            $this->bulletlist .= isset($this->nodes[$start]) ? $this->nodes[$start] : '';
            $this->bulletlist .= "\"";
            if (count($class)) {
                $this->bulletlist .= 'class="'.implode(' ',$class).'"';
            }
            $this->bulletlist .= "></optgroup>";
            $disable = true;
        } else {
            $this->bulletlist .= "<option value=\"" . htmlentities($start) . "\"";
            if ($this->selected == $start) {
                $this->bulletlist .= " selected=\"selected\"";
            }
            if (count($class)) {
                $this->bulletlist .= ' class="'.implode(' ',$class).'"';
            }
            //on mozilla, opera etc. use disable-attribute to set option disabled in select box
            if (($this->disabled == $start || $disable) && $start != 0) {
                $this->bulletlist .= ' disabled="disabled" ';
                $disable = true;
            }
            $this->bulletlist .= ">";

            $url = isset($this->urls[$start]) ? $this->urls[$start] : '';
            $this->bulletlist .= str_repeat($indent, max(0, $this->depth - 1));
            $this->bulletlist .= isset($this->nodes[$start]) ? $this->nodes[$start] : '';
            $this->bulletlist .= "</option>\n";
        }

        if (isset($this->children[$start]) && is_array($this->children[$start])) {
            $this->depth = $this->depth + 1;
            for ($i = 0; $i < count($this->children[$start]); $i++) {
                if ($disable) {
                    $this->displaynode_select($this->children[$start][$i],true); //disable all children
                } else {
                    $this->displaynode_select($this->children[$start][$i]);
                }
            }
            $this->depth = $this->depth - 1;
        }
    }

    function displaynode_moo($start=0)
    {

        if ( ($start!='0') && ($start!='') ) {

            $li_class = array();
            $a_class = array();

            if (dirname($_SERVER['PHP_SELF']) . '/' . $this->urls[$start] == $_SERVER['REQUEST_URI']) {
                $li_class[] = "selected";
                $a_class[] = "selected";
            } //the selected page should be highlighted one way or another

            $this->bulletlist .= "<li class=\"" . implode(' ',$a_class) . "\">";
            $url1 = $this->urls[$start];
            $url = $url1."#heading" . $this->parentnode[$start]['parent'];
            if ($this->onclicks[$start] != '') {$onclick = ' onclick="' . $this->onclicks[$start].'"';} else {$onclick = "";}


            if($this->children[$start]){ // checking whether this is a parent element or not
                if ($url != '') {
                    $this->bulletlist .= "<span class=\"display heading" . $start . "\" style=\"cursor: pointer;\">" . $this->plus;
                    $this->bulletlist .= "<a class=\"" . implode(' ',$a_class) . "\"$onclick>";
                } //start link
            }else{
                if ($url != '') {$this->bulletlist .= "<a class=\"" . implode(' ',$a_class) . "\" href=\"$url\"$onclick>";} //start link
            }
            $this->bulletlist .= $this->nodes[$start];
            if ($url != '') {$this->bulletlist .= "</a>";}
            if($this->children[$start]){ // checking whether this is a parent element or not
                if ($url != '') {$this->bulletlist .= "</span>";} //finish link
            }
        }
        if (is_array($this->children[$start])) {
            $this->depth = $this->depth + 1;


            if($this->depth == 2){
                $this->bulletlist .= "<ul class=\"stretcher\">\n";
            }else{
                $this->bulletlist .= "<ul>\n";
            }
            for ($i=0;$i<count($this->children[$start]);$i++) {
                $this->displaynode_moo($this->children[$start][$i]);
            }
            $this->bulletlist .= "</ul>\n";
            $this->depth = $this->depth - 1;

        }
        if ( ($start!='0') && ($start!='') ) {
            $this->bulletlist .= "</li>\n";
        }
    }


    function shownodes() //debug function
    {
        foreach ($this->nodes as $id => $name) {
            $ret .= "$id = $name\n<br />";
        }
        return $ret;
    }

    function printout_plain($showdepth=10)
    {
        $this->showdepth = $showdepth;
        $this->bulletlist = '';
        $this->displaynode_plain();
        return $this->bulletlist;
    }

    function printout_array($showdepth=10)
    {
        $this->showdepth = $showdepth;
        $this->bulletlist = array();
        $this->displaynode_array();
        return $this->bulletlist;
    }

    /* Returns an indented select list */
    function printout_select($showdepth=10,$selected = '', $disabled = '')
    {
    $this->disabled = $disabled;
        $this->selected = $selected;
        $this->showdepth = $showdepth;
        $this->bulletlist = '';
        $this->displaynode_select();
        return $this->bulletlist;
    }

    function printout_moo($showdepth=10)
    {
        $this->showdepth = $showdepth;
        $this->bulletlist = '';
        $this->displaynode_moo();
        return $this->bulletlist;
    }


    function printout_tree($showdepth=10)
    {
        $this->showdepth = $showdepth;
        $this->bulletlist = '';
        $this->displaynode_treemenu();
        return $this->bulletlist;
    }

    /* Uses H3 elements for top level, then unordered lists for sub-items */
    function printout_h3list($showdepth=10)
    {
        $this->showdepth = $showdepth;
        $this->bulletlist = '';
        $this->displaynode_h3list();
        return $this->bulletlist;
    }

    /* Uses H3 elements for top level, then unordered lists for sub-items */
    function printout_h2list($showdepth=10)
    {
        $this->showdepth = $showdepth;
        $this->bulletlist = '';
        $this->displaynode_h3list(0,2);
        return $this->bulletlist;
    }

    function moodiv($start = 0, $depth = 0)
    {
          if ($depth > 2 || !isset($this->children[$start])) {
            return false;
        }

        $menuItems = array();
        foreach($this->children[$start] as $k => $v) {
              $menuItems[$k] = array();
            $menuItems[$k]['name'] = $this->nodes[$v];
            $menuItems[$k]['url'] = $this->urls[$v];
            $menuItems[$k]['children'] = $this->moodiv($v, $depth + 1);
        }
        return $menuItems;
    }

    function debug()
    {
        print_r($this->nodes);
        echo "<br />";
        print_r($this->children);
        echo "<br />";
    }

    function asArray($start = 0, $depth = 0)
    {
        $res = array();

        /* At our recursion depth or no children */
        if ($depth > 4 || !isset($this->children[$start])) {
            return $res;
        }

        /* Add children */
        foreach($this->children[$start] as $k => $v) {
            $res[$k] = array(
                        'name' => $this->nodes[$v],
                        'url' => $this->urls[$v],
                        'children' => $this->asArray($v, $depth + 1),
                        );
        }

        /* Return result */
        return $res;
    }

} //end class
