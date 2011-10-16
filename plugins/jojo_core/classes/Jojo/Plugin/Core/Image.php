<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Plugin_Core_Image extends Jojo_Plugin_Core {

    /**
     * Output the Image file
     *
     */
    function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);

        /* Get requested filename */
        $file = urldecode(Jojo::getFormData('file', 'default.jpg'));
        $timestamp = strtotime('+1 day');

        /* Check file name has correct extension */
        $validExtensions = array('jpg', 'gif', 'jpeg', 'png');
        if (!in_array(Jojo::getFileExtension($file), $validExtensions)) {
            /* Not valid, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }
        
        $pad = false;

        if (preg_match('/^([0-9]+|default)\/(.+)/', $file, $matches)) {
            /* Max size */
            $_GET['sz'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^fit([0-9]+)x([0-9]+)\/(.+)/', $file, $matches)) {
            /* Fit to Max width + max height (no crop)*/
            $_GET['fitmaxw'] = $matches[1];
            $_GET['fitmaxh'] = $matches[2];
            $filename = $matches[3];
        } elseif (preg_match('/^pad([0-9]+)x([0-9]+)\/(.+)/', $file, $matches)) {
            /* same as "fit" but will pad out the image with whitespace */
            $_GET['fitmaxw'] = $matches[1];
            $_GET['fitmaxh'] = $matches[2];
            $filename = $matches[3];
            $pad = true;
        } elseif (preg_match('/^([0-9]+)x([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max width + max height*/
            $_GET['maxw'] = $matches[1];
            $_GET['maxh'] = $matches[2];
            $filename = $matches[3];
        } elseif (preg_match('/^w([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max width */
            $_GET['maxw'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^h([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max height */
            $_GET['maxh'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^v([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max volume */
            $_GET['maxv'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^s([0-9]+)\/(.+)/', $file, $matches)) {
            /* Square */
            $_GET['sq'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^mh([0-9]+)\/(.+)/', $file, $matches)) {
            /* ?? */
            $_GET['sz'] = $matches[1];
            $filename = $matches[2];
        }

        if (isset($filename) && file_exists(_DOWNLOADDIR . '/' . $filename)) {
            /* Uploaded image file */
            $filename = _DOWNLOADDIR . '/' . $filename;
        } elseif (isset($filename) && $res = Jojo::listThemes('images/' . $filename)) {
            /* Found in a theme images folder */
            $filename = $res[0];
        } elseif (isset($filename) && $res = Jojo::listPlugins('images/' . $filename)) {
            /* Found in a plugin images folder */
            $filename = $res[0];
        } elseif ($res = Jojo::listPlugins($file)) {
            /* Found in a plugin somewhere */
            $filename = $res[0];
        } elseif (!isset($filename) || !self::isRemoteFile($filename)) {
            /* File from somewhere */
            $filename = $file;
        }

        /* filetype + mimetype */
        $filetype = Jojo::getFileExtension($filename);
        $mimetype = ($filetype == 'jpg') ? 'image/jpeg': 'image/' . $filetype;

        /* Quality */
        $quality = (isset($_GET['ql'])) ? $_GET['ql'] : Jojo::getOption('jpeg_quality', 85);

        /* size */
        if (isset($_GET['sz'])) {
            $size = $_GET['sz'];
            $s = $size;
        } elseif (isset($_GET['fitmaxw']) && isset($_GET['fitmaxh'])) {
            $fitmaxw = $_GET['fitmaxw'];
            $fitmaxh = $_GET['fitmaxh'];
            $s = 'fit' . $fitmaxw.'x'.$fitmaxh;
        } elseif (isset($_GET['maxw']) && isset($_GET['maxh'])) {
            $maxw = $_GET['maxw'];
            $maxh = $_GET['maxh'];
            $s = $maxw.'x'.$maxh;
        } elseif (isset($_GET['maxw'])) {
            $maxw = $_GET['maxw'];
            $s = 'w'.$maxw;
        } elseif (isset($_GET['maxh'])) {
            $maxh = $_GET['maxh'];
            $s = 'h'.$maxh;
        } elseif (isset($_GET['sq'])) {
            $sq = $_GET['sq'];
            $size = str_replace('s','',$_GET['sq']);
            $s = 's'.$sq;
        } elseif (isset($_GET['maxv'])) {
            $maxv = $_GET['maxv'];
            $s = 'v' . $_GET['maxv'];
        } else {
            $size = 'default';
            $s = '';
        }

        if ($s && self::isRemoteFile($filename)) {
            $cachefile = _CACHEDIR . '/images/remote/' . $s . '/' . md5($filename) . '.' . Jojo::getFileExtension($filename);
        } elseif ($s) {
            $cachefile = _CACHEDIR . '/images/' . $s . '/' . str_replace(_DOWNLOADDIR . '/', '', $filename);
        } elseif (self::isRemoteFile($filename)) {
            $cachefile = _CACHEDIR . '/images/remote/' . md5($filename) . '.' . $filetype;
        } else {
            $cachefile = _CACHEDIR . '/images/' . str_replace(_DOWNLOADDIR . '/', '', $filename);
        }



        Jojo::runHook('jojo_core:imageCheckAccess', array('filename' => $filename));

        /* Check for existance of server-cached copy if user has not pressed CTRL-F5 */
        if (is_file($cachefile) && !Jojo::ctrlF5()) {
            Jojo::runHook('jojo_core:imageCachedFile', array('filename' => $cachefile));

            parent::sendCacheHeaders(filemtime($cachefile));

            /* output image data */
            $data = file_get_contents($cachefile);
            header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T', filemtime($cachefile)));
            header('Cache-Control: public, max-age=28800');
            header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
            header('Pragma: ');
            header('Content-type: ' . $mimetype);
            header('Content-Length: ' . strlen($data));
            header('Content-Disposition: inline; filename=' . basename($filename) . ';');
            header('Content-Description: PHP Generated Image (cached)');
            header('Content-Transfer-Encoding: binary');
            echo $data;
            exit();
        }

        /* for default sized images, read image data directly to save reprocessing */
        if (($s == 'default') || ($s == '')) {
            if (self::isRemoteFile($filename) || Jojo::fileExists($filename)) {
                Jojo::runHook('jojo_core:imageDefaultFile', array('filename' => $filename));

                parent::sendCacheHeaders(filemtime($filename));

                /* output image data */
                $data = file_get_contents($filename);

                //header('Cache-Control: public');
                if (!self::isRemoteFile($filename)) {
                    header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T', filemtime($filename)));
                }
                header('Cache-Control: public, max-age=28800');
                header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
                header('Pragma: ');
                header('Content-type: ' . $mimetype);
                header('Content-Length: ' . strlen($data));
                header('Content-Disposition: inline; filename=' . basename($filename) . ';');
                header('Content-Description: PHP Generated Image (cached)');
                header('Content-Transfer-Encoding: binary');
                echo $data;

                /* Cache for quicker response next time */
                Jojo::RecursiveMkdir(dirname($cachefile));
                file_put_contents($cachefile, $data);
                Jojo::publicCache($file, $data);
                exit();
            }

            foreach (Jojo::listThemes('images/' . $file) as $pluginfile) {
                Jojo::runHook('jojo_core:imageDefaultFile', array('filename' => $pluginfile));
                parent::sendCacheHeaders(filemtime($pluginfile));

                /* output image data */
                $data = file_get_contents($pluginfile);
                header('Last-Modified: '.date('D, d M Y H:i:s \G\M\T', filemtime($pluginfile)));
                header('Cache-Control: public, max-age=28800');
                header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
                header('Pragma: ');
                header('Content-type: ' . $mimetype);
                header('Content-Length: ' . strlen($data));
                header('Content-Disposition: inline; filename=' . basename($filename) . ';');
                header('Content-Description: PHP Generated Image (cached)');
                header('Content-Transfer-Encoding: binary');
                echo $data;

                /* Cache for quicker response next time */
                Jojo::RecursiveMkdir(dirname($cachefile));
                file_put_contents($cachefile, $data);
                Jojo::publicCache($file, $data);
                exit();
            }

            foreach (Jojo::listPluginsReverse('images/' . $file) as $pluginfile) {
                Jojo::runHook('jojo_core:imageDefaultFile', array('filename' => $pluginfile));
                parent::sendCacheHeaders(filemtime($pluginfile));

                /* output image data */
                $data = file_get_contents($pluginfile);
                header('Last-Modified: '.date('D, d M Y H:i:s \G\M\T', filemtime($pluginfile)));
                header('Cache-Control: public, max-age=28800');
                header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
                header('Pragma: ');
                header('Content-type: ' . $mimetype);
                header('Content-Length: ' . strlen($data));
                header('Content-Disposition: inline; filename=' . basename($filename) . ';');
                header('Content-Description: PHP Generated Image (cached)');
                header('Content-Transfer-Encoding: binary');
                echo $data;

                /* Cache for quicker response next time */
                Jojo::RecursiveMkdir(dirname($cachefile));
                file_put_contents($cachefile, $data);
                Jojo::publicCache($file, $data);
                exit();
            }
        }

        if (self::isRemoteFile($filename) || Jojo::fileExists($filename)) {
            /* the file exists - open it & create image handle*/
            if ($filetype == 'gif') {
                $im = imagecreatefromgif($filename);
            } elseif ($filetype == 'png') {
                $im = imagecreatefrompng($filename);
            } else {
                $im = imagecreatefromjpeg($filename);
            }
        } else {
            /* Search for matching files in the themes */
            $im = false;
            foreach (Jojo::listThemes('images/' . $filename) as $pluginfile) {
                $size = 'default';
                if ($filetype == 'gif') {
                    $im = imagecreatefromgif($pluginfile);
                    break;
                } elseif ($filetype == 'png') {
                    $im = imagecreatefrompng($pluginfile);
                    break;
                } else {
                    $im = imagecreatefromjpeg($pluginfile);
                    break;
                }
            }

            if (!$im) {
                /* Search for matching files in the plugins */
                foreach (Jojo::listPlugins('images/' . $filename, true) as $pluginfile) {
                    $size = 'default';
                    if ($filetype == 'gif') {
                        $im = imagecreatefromgif($pluginfile);
                        break;
                    } elseif ($filetype == 'png') {
                        $im = imagecreatefrompng($pluginfile);
                        break;
                    } else {
                        $im = imagecreatefromjpeg($pluginfile);
                        break;
                    }
                }
            }

            if ((!$im) && (preg_match('%.*/themes/([a-z0-9_-]+)\\.jpg$%i', $file, $result))) {
                /* if format is images/500/themes/theme_name.jpg search for theme screenshot */
                if (Jojo::fileexists(_THEMEDIR.'/'.$result[1].'/screenshot.jpg')) {
                    $im = imagecreatefromjpeg(_THEMEDIR.'/'.$result[1].'/screenshot.jpg');
                } elseif (Jojo::fileexists(_BASETHEMEDIR.'/'.$result[1].'/screenshot.jpg')) {
                    $im = imagecreatefromjpeg(_BASETHEMEDIR.'/'.$result[1].'/screenshot.jpg');
                } else {
                    $im = imagecreatefromjpeg(_BASEPLUGINDIR.'/jojo_core/images/cms/no-screenshot.jpg');
                }
            }
        }

        if (!$im) {
            /* Could not open image, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        $im_width = imageSX($im);
        $im_height = imageSY($im);

        $startx = $starty = 0; //This is used as the start co-ordinates. Normally zero, but for cropped images this will differ

        if (!empty($sq)) {
            /* Cut the img square */
            $new_height = $new_width = $size;
            $shortest = min($im_height, $im_width);
            $radius = ($shortest / 2); // Not radius, but you get the point (half the width/height)
            
            //find the offset for cropping
            $cropdata = self::getCropData($filename);
            if (is_array($cropdata)) {
                /* no crop data available, do a center crop */
                $crop_center_x = round($cropdata[0] * $im_width / 100);
                $crop_center_y = round($cropdata[1] * $im_height / 100);
                $startx = min(max($crop_center_x - $radius, 0), $im_width - $shortest);
                $starty = min(max($crop_center_y - $radius, 0), $im_height - $shortest);
            } else {
                /* no crop data available, do a center crop */
                $startx = ($im_width / 2) - $radius;
                $starty = ($im_height / 2) - $radius;
            }
            
            //resize
            $im_height = $im_width = min($im_height, $im_width);
        } elseif (!empty($fitmaxw) && !empty($fitmaxh)) {
            /* Scale to maximum dimensions - no clipping */
            $startx = 0;
            $starty = 0;
            $wfactor = $fitmaxw/$im_width;
            $hfactor = $fitmaxh/$im_height;
            if ($hfactor < $wfactor) {
               $new_height = $fitmaxh;
               $new_width = $im_width * $hfactor;
            } else {
               $new_width = $fitmaxw;
               $new_height = $im_height * $wfactor;
            }
        } elseif (isset($maxv) && !empty($maxv)) {
            /* Image of a maximum total area */
            $currentv = $im_width * $im_height;
            $factor = max(sqrt($currentv/$maxv), 1);
            $new_height = $im_height / $factor;
            $new_width = $im_width / $factor;
         } elseif (!empty($fitmaxw) && !empty($fitmaxh)) {
            /* Scale to maximum dimensions - no clipping */
            $startx = 0;
            $starty = 0;
            $wfactor = $fitmaxw/$im_width;
            $hfactor = $fitmaxh/$im_height;
            if ($hfactor < $wfactor) {
               $new_height = $fitmaxh;
               $new_width = $im_width * $hfactor;
            } else {
               $new_width = $fitmaxw;
               $new_height = $im_height * $wfactor;
            }
       } elseif (!empty($maxw) && !empty($maxh)) {
            /* Scale to maximum dimensions, clipping to fit */
            $new_width = $maxw;
            $new_height = $maxh;
            $startx = 0;
            $starty = 0;
            $factor1 = $im_width/$maxw;
            $factor2 = $im_height/$maxh;
            
            
            $cropdata = self::getCropData($filename);
            if (is_array($cropdata)) {
                /* we have crop data, so crop around the crop point */
                $crop_center_x = round($cropdata[0] * $im_width / 100);
                $crop_center_y = round($cropdata[1] * $im_height / 100);
                if ($factor1 > $factor2) {
                    $startx = $crop_center_x;
                    $scale_width = $maxw * $factor2;
                    $startx -= ($scale_width / 2);
                    $minx = $im_width - $scale_width;
                    $startx = min(max($startx, $minx), 0);
                    $im_width = $scale_width;
                } else {
                    $starty = $crop_center_y;
                    $scale_height = $maxh * $factor1;
                    $starty -= ($scale_height / 2);
                    $miny = $im_height - $scale_height;
                    $starty = max(min($starty, $miny), 0);
                    $im_height = $scale_height;
                }
            } else {
                /* we have  no crop data, so crop around the centre of the image */
                if ($factor1 > $factor2) {
                   $startx = ($im_width / 2);
                   $im_width = $maxw * $factor2;
                   $startx -= ($im_width / 2);
                } else {
                    $starty = ($im_height / 2);
                    $im_height = $maxh * $factor1;
                    $starty -= ($im_height / 2);
                }
            }
            
        } elseif (!empty($maxh)) {
            /* Resize tp maximum height */
            $factor = $maxh / $im_height;
            $new_height = $maxh;
            $new_width = $im_width * $factor;
        } elseif (!empty($maxw)) {
            /* Resize tp maximum width */
            $factor = $maxw/$im_width;
            $new_width = $maxw;
            $new_height = $im_height * $factor;
        } else {
            if ($size == 'default') {
                $size = max($im_width,$im_height);
            }
            if ($im_width >= $im_height) {
                $factor = $size/$im_width;
                $new_width = $size;
                $new_height = $im_height * $factor;
            } else {
                $factor = $size/$im_height;
                $new_height = $size;
                $new_width = $im_width * $factor;
            }
        }

        if ($new_width != imageSX($im) || $new_height != imageSY($im) || ($pad && (($fitmaxw != imageSX($im)) || ($fitmaxh != imageSY($im))))) {
            /* Resize */
            if ($pad) {
                $new_im = ImageCreateTrueColor($fitmaxw, $fitmaxh);
            } else {
                $new_im = ImageCreateTrueColor($new_width, $new_height);
            }
            if ($filetype == 'png') { //prevent the black background from appearing when resizing transparent png
                imagecolortransparent($new_im, imagecolorallocatealpha($new_im, 0, 0, 0,0));
                imagealphablending($new_im, false);
            } elseif ($pad) {
                $background = imagecolorallocate($new_im, 0xFF, 0xFF, 0xFF);//todo: allow this to be something other than white
                imagefill($new_im, 0, 0, $background);
            }
            $dst_x = ($pad) ? round(($fitmaxw / 2) - ($new_width / 2)) : 0;
            $dst_y = ($pad) ? round(($fitmaxh / 2) - ($new_height / 2)) : 0;
            ImageCopyResampled($new_im, $im, $dst_x, $dst_y, $startx, $starty, $new_width, $new_height, $im_width, $im_height);
            //ImageCopy($new_im, $im, 0, 0, $startx, $starty, $new_width, $new_height, $im_width, $im_height);
            $nochange = false;
        } else {
            /* No change */
            $new_im = $im;
            $nochange = true;
        }

        /* create folders in cache */
        Jojo::RecursiveMkdir(dirname($cachefile));

        /* Allow custom watermark code to be inserted depending on the site */
        foreach(Jojo::listPlugins('config/watermark.inc.php') as $wmfile) {
            require_once($wmfile);
        }

        $new_im = Jojo::applyFilter('image_watermark', $new_im);

        /* output image data */
        header('Content-type: ' . $mimetype);
        header('Content-Disposition: inline; filename=' . basename($filename) . ';');
        header('Content-Description: PHP Generated Image');
        header('Content-Transfer-Encoding: binary');

        header('Cache-Control: public, max-age=28800');
        header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
        header('Pragma: ');

        // output
        if ($filetype == "gif") {
            Imagegif($new_im);
            Imagegif($new_im, $cachefile);
            Imagegif($new_im, Jojo::publicCache($file));
        } else if ($filetype == "png") {
            imagesavealpha($new_im, true);
            Imagepng($new_im);
            Imagepng($new_im, $cachefile);
            Imagepng($new_im, Jojo::publicCache($file));
        } else {
            Imagejpeg($new_im, $cachefile, $quality);
            Imagejpeg($new_im, Jojo::publicCache($file), $quality);
            Imagejpeg($new_im,'',$quality);
        }

        // cleanup
        if ($new_im && !empty($new_im)) ImageDestroy($new_im);
        if ($im && !$nochange) ImageDestroy($im);

        exit();
    }


    //added by tim
    // TODO needs some more love
    static function isRemoteFile($filename) {
        return (preg_match('|^https?\://|i', $filename));
    }
    
    /* retrieves crop data, returning false or array(x,y) with the center point of an image. Todo, cache to a file to avoid DB query */
    static function getCropData($filepath)
    {
        if (!Jojo::fileExists($filepath)) return false;
        $imagedata = file_get_contents($filepath);
        $cropdata = Jojo::selectRow("SELECT * FROM {cropdata} WHERE hash=?", sha1($imagedata));
        if (!$cropdata) return false;
        $crop_x = (isset($cropdata['x'])) ? $cropdata['x'] : false;
        $crop_y = (isset($cropdata['y'])) ? $cropdata['y'] : false;
        return array($crop_x, $crop_y);
    }
}
