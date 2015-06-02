<?php
require('sp_config.php');

define("VERSION","1.1");

//Detect and store GD version if this is the first time the script is run.
if(!isset($gd_version)) {
    $config_file = @fopen('sp_config.php','r');
    $contents = fread($config_file, filesize('sp_config.php'));
    @fclose($config_file);
    $gd_version = gd_version();
    $contents = str_replace(
        '?>',
        '$gd_version = "' . $gd_version . "\";\n?>",
        $contents
    );
    $config_file = @fopen('sp_config.php','w');
    @fwrite($config_file, $contents);
//~     header("Location: $_SERVER[PHP_SELF]");
//~     echo 'GD Detected!  Version: '
//~         .$gd_version
//~         .'. Please reload this page to view your gallery. '
//~         .'You will not see this message again.';
}

//Create the cache folder if caching is enabled and it does not already exist
//If the folder cannot be created, disable caching
if($cachethumbs && !file_exists($cachefolder)) {
    if(!mkdir($cachefolder, 0755))
        $cachethumbs = false;
}
if($cacheresized && !file_exists($cacheresizedfolder)) {
    if(!mkdir($cacheresizedfolder, 0755))
        $cacheresized = false;
}

//Create the cache.ini file if it does not exist.
//Parse into the $cache_ini variable.
if($cachethumbs) {
    $cache_ini = @fopen($cachefolder . "/cache.ini","a");
    @fclose($cache_ini);
    $cache_ini = @parse_ini_file($cachefolder . "/cache.ini",true);
}

//Create the resized_cache.ini file if it does not exist.
//Parse into the $resized_cache_ini variable.
if($cacheresized) {
    $resized_cache_ini = @fopen(
        $cacheresizedfolder . "/resized_cache.ini",
        "a"
    );
    @fclose($resized_cache_ini);
    $resized_cache_ini = @parse_ini_file(
        $cacheresizedfolder . "/resized_cache.ini",
        true
    );
}

if(!$cachethumbs)
    clearThumbCache();

if(!$cacheresized)
    clearResizedCache();

//Parse the descriptions file
$descriptions = @parse_ini_file('sp_descriptions.ini',true);
if (! $descriptions) {
    die('Unable to read sp_descriptions.ini file.');
}

$parts = pathinfo($_SERVER['PHP_SELF']);
$current_working_directory = $parts['dirname'];

$hide_folders[] = '.';
$hide_folders[] = '..';
$hide_folders[] = '.git';
$hide_folders[] = 'cgi-bin';

//If a directory list request was made, parse it into the dir variable.
$dir='.';
if(isset($_GET['dir']))
    $dir = stripslashes($_GET['dir']);

//Prevent requests for parent directories
if(substr($dir, 0, 2) == '..')
    $dir='.';
if(substr($dir, 0, 1) == '/')
    $dir='.';
if(!(strpos($dir,'..') === false))
    $dir='.';

//Get the name of the current folder.
$patharr = explode('/',$dir);
$current = $patharr[count($patharr)-1];
$alias = '';

//Get the folder alias, if it exists
$alias = getTitle($current);

//If a file was requested for display, read the path into the $display_file variable
if(array_key_exists('file', $_GET))
{
    $display_file = preg_replace(
        '/\.\//',
        returnCurrentWorkingDirectory() . '/',
        stripslashes($_GET['file'])
    );
    $resize_file = stripslashes($_GET['file']);
}
else
    $display_file = '';

if($display_file != '') {
    if(!file_exists($resize_file)) {
        $hash = md5(substr($resize_file,2,strlen($resize_file)-2));
        if($cachethumbs) {
            @unlink($cachefolder . "/" . $hash . ".jpg");
            unset($cache_ini[$hash]);
            write_ini_file($cachefolder . "/cache.ini",$cache_ini);
        }
        if($cacheresized) {
            @unlink($cacheresizedfolder . "/" . $hash . ".jpg");
        }
        $location = returnCurrentWorkingDirectory();
        if(empty($location))
            $location = '/';
        header("Location: " . $location);
    }
    if(isImage(basename($_GET['file'])))
        $current = basename($_GET['file']);
    else
        exit;
}

if($alias != '.' && $alias != '')
    $page_title = $title . " : " . $alias;
else
    $page_title = $title;

if(!empty($_GET['dir']) || (empty($_GET['dir']) && empty($_GET['file']))) {
    $imglink = array(); //An array to hold links to the images
    $dirlink = array(); //An array to hold links to sub-directories

    foreach(getDirList() as $file) {
        $path = $dir . "/" . $file;
        $webpath = substr($path,2,strlen($path)-2);
        $filetitle = getTitle($file);

        //If the current item is an image, add a the link text to the $imglink array
        if( isImage($file)) {
            $cached_img = $cachefolder . "/" . md5($webpath) . ".jpg";
            $divwidth = $maxthumbwidth+4;
            $divheight = $maxthumbheight+24;
            if($showimgtitles)
                $divheight += 16;
            if($alignimages)
                $link = '<div class="imgwrapper" style="height:'
                    . $divheight
                    . 'px;width:'
                    . $divwidth
                    .'px;text-align:center">';
            else
                $link = '<div class="imgwrapper" style="height:' . $divheight . 'px;">';
            if($modrewrite) {
                $link .= "<a href=\""
                    . returnCurrentWorkingDirectory()
                    . "/file/$webpath\">";
                if($cachethumbs
                    && file_exists($cached_img)
                    && sizeMatches($cached_img)
                    && cacheLinkMatch(md5($webpath))
                    && cacheFilesizeMatch(md5($webpath),filesize($webpath))
                )
                    $link .= "<img src=\""
                        . returnCurrentWorkingDirectory()
                        . '/'
                        . $cached_img
                        . "\" alt=\""
                        . $filetitle
                        . "\" />";
                else
                    $link .= "<img src=\""
                        . returnCurrentWorkingDirectory()
                        . "/thumb/$webpath\" alt=\""
                        . $filetitle
                        . "\" />";
            }
            else {
                $link .= "<a href=\"$_SERVER[PHP_SELF]?file=$path\">";
                if($cachethumbs
                    && file_exists($cached_img)
                    && sizeMatches($cached_img)
                    && cacheLinkMatch(md5($webpath))
                    && cacheFilesizeMatch(md5($webpath),filesize($webpath))
                )
                    $link .= "<img src=\""
                        . $current_working_directory
                        . $cached_img
                        . "\" alt=\""
                        . $filetitle
                        . "\" />";
                else
                    $link .= "<img src=\"sp_getthumb.php?source=$webpath\" alt=\""
                        . $filetitle
                        . "\" />";
            }
            if($showimgtitles)
                $link .= '<span>' . $filetitle . '</span>';
            $link .= "</a></div>\n\t";
            $imglink[] = $link;
        }
        //If the current item is a directory, add the link text to the $dirlink array
        else if(is_dir($path) && !in_array($file, $hide_folders)) {
            if($modrewrite)
                $dir_string = "<a href=\""
                    . returnCurrentWorkingDirectory()
                    . "/folder/$webpath\">"
                    . $filetitle
                    . "</a>";
            else
                $dir_string = "<a href=\""
                    . $_SERVER['PHP_SELF']
                    . "?dir=$path\">"
                    . $filetitle
                    . "</a>";
            if($showfolderdetails) {
                $num_images = getNumImages($path);
                $num_dir = getNumDir($path);
                $img_s = ($num_images == 1) ? '':'s';
                $dir_s = ($num_dir == 1) ? '':'s';
                if($num_images != 0 || $num_dir != 0)
                    $dir_string .= " (";
                if($num_images != 0) {
                    $dir_string .= $num_images . " image" . $img_s;
                    if($num_dir != 0)
                        $dir_string .= ', ';
                    else
                        $dir_string .= ')';
                }
                if($num_dir != 0) {
                    $dir_string .= $num_dir . " sous-répertoire" . $dir_s . ')';
                }
            }
            $dirlink[$file] = $dir_string;
        }
        //If the current item is not an image and is not a directory, ignore it
    }
    uksort($dirlink, "strnatcasecmp");
    $dirlink = array_reverse($dirlink);
}
?>
