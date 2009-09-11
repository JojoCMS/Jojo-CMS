<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class gallerylayout
{
    var $images = array();
    var $_numimages = 0;
    var $_fullwidth;
    var $_imagetemplate = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
    var $_padding = 3;
    var $code = 0;

    function gallerylayout($maxwidth=600,$padding=3,$imagetemplate = '')
    {
        $this->_fullwidth = $maxwidth;
        $this->_padding = $padding;
        if ($imagetemplate != '') $this->_imagetemplate = $imagetemplate;
        $this->code = mt_rand(1, 100);
    }

    /* Gets the file extension for a given filename */
    function _getFileExt($file)
    {
        $ext = explode(".", $file);
        if (count($ext) == 0) return '';
        return $ext[count($ext)-1];
    }

    /* Converts the format of a 2D array from $arr[a][b] to $arr[b][a] - used for sorting the array*/
    function _transpose($arr)
    {
        foreach($arr as $keyx => $valx) {
            foreach($valx as $keyy => $valy) {
                $newarr[$keyy][$keyx] = $valy;
            }
        }
        return $newarr;
    }

    function addImage($filename, $url = '')
    {
        if ($url == '') $url = $filename;
        /* Ensure the file is an image */
        if (
            (strtolower($this->_getFileExt($filename)) != "jpg") &&
            (strtolower($this->_getFileExt($filename)) != "jpeg") &&
            (strtolower($this->_getFileExt($filename)) != "gif") &&
            (strtolower($this->_getFileExt($filename)) != "png")
             ) {
            return false;
        }

        /* Read the dimensions of the image */
        $imagesize = getimagesize($filename);
        $w = $imagesize[0];
        $h = $imagesize[1];

        /* don't include zero sized images */
        if (($h == 0) || ($w == 0)) return false;

        /* Find the ration of width:height */
        $ratio = $w / $h;

        /* Set format based on the dimensions */
        $format = ($w > $h) ? 'landscape' : 'portrait';

        /* Keep a count on the total number of images */
        $this->_numimages++;

        /* Save all image details to an array */
        $i = $this->_numimages - 1;
        $this->images[$i] = array();
        $this->images[$i]['filename'] = $filename;
        $this->images[$i]['url'] = $url;
        $this->images[$i]['format'] = $format;
        $this->images[$i]['ratio'] = $ratio;
        $this->images[$i]['w'] = $w; //Not currently used
        $this->images[$i]['h'] = $h; //Not currently used

        return true;
    }

    /* Replaces variables into the supplied image template */
    function insertImage($size, $name)
    {
        return str_replace('[image]',$name,str_replace('[size]',$size, $this->_imagetemplate));
    }

    function getHtml()
    {
        $html = '';
        $html .= '<div id="gallery-thumbs" style="width: 360px; overflow: auto; border: 1px solid #444;">';
        $html .= "\n";

        for ($i=0;$i<count($this->images);$i++) {
            $html .= '    <div style="float: left; width: 110px;">';
            $html .= "\n";
                    $html .= '<a href="images/default/' . $this->images[$i]['url'].'" onclick="document.getElementById(\'gallery-image-' . $this->code . '\').src=\'images/360/loading.gif\';return false;" title="">';
                    $html .= "\n";
                        $html .= '<img class="boxed" src="images/s100/' . $this->images[$i]['url'].'" alt="" />';
                        $html .= "\n";
                    $html .= '</a>';
                    $html .= "\n";
                $html .= '</div>';
                $html .= "\n";
        }
        $html .= "<div style=\"clear: both;\"></div></div>\n";
        $html .= '<!-- [Pagination] -->';
        $html .= "\n";
        $html .= '<div id="gallery-pagination" style="text-align: center;">Pages...</div>';
        $html .= "\n";

        $html .= "\n<div>";
            $html .= '<!-- [Preview Image] -->';
            $html .= "\n";
                $html .= '<img class="boxed" id="gallery-image-' . $this->code . '" src="images/s360/' . $this->images[0]['url'].'" alt="" />';
                $html .= "\n</div>";

        /* Close the containing DIV */
        //$html .= "<div style=\"clear: both;\"></div>\n\n";

        return $html;
    }
}
