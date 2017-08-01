<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Harvey Kane <code@ragepank.com>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

ini_set('max_execution_time', 900);

class frajax {
    var $title = '';
    var $combined;   //combines all actions into one block, saving space and allowing gzipping. Faster, but means responses aren't delivered gradually.
    var $usegzip;
    var $jsopen;
    var $jsclose;

    function __construct($combined=false, $usegzip=false) {
        $this->usegzip = $usegzip;
        $this->combined = $combined;
        $this->combined = false; //debug
        $this->jsopen  = $this->combined ? '' : '<script type="text/javascript">'."\n/* <![CDATA[ */\n";
        $this->jsclose = $this->combined ? '' : '/* ]]> */'."\n".'</script'.">\n";
    }

    /* Gets the correct name of scriptaculous effects, with correct capitalisation etc. Or sets a default effect */
    function _getEffect($effect) {
        /* disabling all scriptaculous effects to be replaced (eventually) with jQuery */
        return '';
        switch (strtolower($effect)) {
          case 'none':
            return 'none';
          case 'blindup':
            return 'BlindUp';
          case 'switchoff':
            return 'SwitchOff';
          case 'fadeout':
            return 'SwitchOff';
        }
        return $effect;
    }

    function sendHeader() {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
        echo "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
        echo "<html>\n";
        echo "<head>\n";
        echo "<title>".$this->title."</title>\n";
        echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
        echo '<base href="' . _SITEURL . '/" />';
        echo "</head>\n";
        echo "<body>\n";
        if ($this->combined) {
            echo '<script type="text/javascript">'."\n/* <![CDATA[ */\n";
        }
        if (!$this->usegzip) flush();
    }

    function sendFooter() {
        if ($this->combined) {
            echo '/* ]]> */'."\n".'</script'.">\n";
        }
        echo "</body>\n";
        echo "</html>\n";
    }

    /* Sets the value of an element in the parent */
    function assign($id, $property, $value, $effect='', $delay=1) {
        $effect = $this->_getEffect($effect);
        echo $this->jsopen;

        $value = json_encode($value);
        if ($property == 'innerHTML') {
            echo "parent.$('#$id').html($value);\n";
        } else {
            echo "parent.document.getElementById('".$id."').".$property." = $value;\n";
        }

        if ($effect != '') {
            echo "new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $delay});\n";
        }

        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Sets the focus to the element */
    function setFocus($id) {
        echo $this->jsopen;
        echo "parent.document.getElementById('".$id."').focus();\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* SCroll to the top of the screen */
    function scrollToTop() {
        echo $this->jsopen;
        echo "parent.window.scrollTo(0,0);\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Adds a class to an element */
    function addClass($id, $classname) {
        echo $this->jsopen;
        //echo "parent.document.getElementById('".$id."').className += ' ".$classname."';\n";
        echo "parent.$('#$id').addClass('$classname');\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Removes a class from an element */
    function removeClass($id, $classname) {
        echo $this->jsopen;
        //echo "var c = '".$classname."';\n";
        //echo "var rep=parent.document.getElementById('".$id."').className.match(' '+c)?' '+c:c;\n";
        //echo "parent.document.getElementById('".$id."').className= parent.document.getElementById('".$id."').className.replace(rep,'');";
        echo "parent.$('#$id').removeClass('$classname');\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Sends javascript code without modification */
    function script($js) {
        echo $this->jsopen;
        echo $js."\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Shows an alert box */
    function alert($message) {
        echo $this->jsopen;
        echo "alert('".$this->_escapeJS($message)."');\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Shows a page element */
    function show($id,$effect='',$delay=0) {
        $effect = $this->_getEffect($effect);
        echo $this->jsopen;
        if ($effect != '') {
          echo "new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $delay});\n";
        } else {
          //echo "parent.document.getElementById('$id').style.display = 'block';\n";
          echo "parent.$('#$id').show();\n";
        }
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Hides a page element */
    function hide($id, $effect='', $delay=0) {
        $effect = $this->_getEffect($effect);
        echo $this->jsopen;
        if ($effect != '') {
          echo "if (parent.document.getElementById('$id').visible()) {";
            echo "new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $delay});\n";
          echo "}";
        } else {
          //echo "parent.document.getElementById('$id').style.display = 'none';\n";
          echo "parent.$('#$id').hide();\n";
        }
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* runs a scriptaculous effect on an element */
    function effect($id, $effect='', $duration=1, $delay=0, $options='') {
      //$effect = $this->_getEffect($effect);
      //echo $this->jsopen;
      //if ($delay == 0) {
      //  echo "new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $duration,$options});\n";
      //} else {
      //  echo "setTimeout(\"new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $duration})\",".$delay."000);";
      //}
      //echo $this->jsclose;
      //if (!$this->usegzip) flush();
    }

    /* runs a scriptaculous morph effect on an element */
    function morph($id, $toclass='', $duration=1, $delay=0) {
        //$effect = $this->_getEffect($effect);
        echo $this->jsopen;
        if ($delay == 0) {
          //echo "new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $duration});\n";
          echo "parent.document.getElementById('$id').morph('$toclass');\n";
        } else {
          //echo "setTimeout(\"new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $duration})\",".$delay."000);";
        }
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Clears the innerHTML content of element */
    function clear($id, $effect='', $delay=0) {
        $effect = $this->_getEffect($effect);
        echo $this->jsopen;
        if ($effect != '') {
          echo "new parent.Effect.$effect(parent.document.getElementById('$id'),{duration: $delay});\n";
        } else {
          //echo "parent.document.getElementById('$id').innerHTML = '';\n";
          echo "parent.$('#$id').html('');\n";
        }
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Sets the title of the parent window */
    function setTitle($title) {
        echo $this->jsopen;
        echo "parent.document.title = '".$this->_escapeJS($title)."';\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }

    /* Redirects to the specified page */
    function redirect($url='') {
        if ($url == '') $url = 'parent.window.location';
        echo $this->jsopen;
        echo 'parent.window.location = "'.$this->_escapeJS($url).'";'."\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
        $this->sendFooter();
        exit();
    }

    /* Select menu */
    function setSelected($menuid, $title) {
        echo $this->jsopen;
        echo 'var li = parent.document.getElementById("nav").getElementsByTagName("LI");'."\n";
        echo 'for(var i=0;i<li.length;i++){'."\n";
        echo '  var a=li[i].getElementsByTagName("A");'."\n";
        echo '  var txt=a[0].innerHTML;'."\n";
        echo '  var rep=li[i].className.match(" selected")?" selected":"selected";'."\n";
        echo '  li[i].className=li[i].className.replace(rep,"");'."\n";
        echo '  if(txt=="'.$this->_escapeJS($title).'"){'."\n";
        echo '    li[i].className+=" selected";'."\n";
        echo '  }'."\n";
        echo '}'."\n";
        echo $this->jsclose;
        if (!$this->usegzip) flush();
    }


    function _escapeJS($data) {
        /* beta */
        $data = str_replace("'", "\'", $data);
        $data = str_replace('"', "'+String.fromCharCode(34)+'", $data);
        $data = str_replace ("\r\n", '\n', $data);
        $data = str_replace ("\r", '\n', $data);
        $data = str_replace ("\n", '\n', $data);
        return $data;
    }
}