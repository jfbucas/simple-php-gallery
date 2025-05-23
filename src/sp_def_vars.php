<?php
require('sp_config.php');

define("VERSION","1.1");

//Set default lang
if(!isset($lang))
    $lang = 'en';

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
    //@fclose($cache_ini);
    $cache_ini = @parse_ini_file($cachefolder . "/cache.ini",true);
}

//Create the resized_cache.ini file if it does not exist.
//Parse into the $resized_cache_ini variable.
if($cacheresized) {
    $resized_cache_ini = @fopen(
        $cacheresizedfolder . "/resized_cache.ini",
        "a"
    );
    //@fclose($resized_cache_ini);
    $resized_cache_ini = @parse_ini_file(
        $cacheresizedfolder . "/resized_cache.ini",
        true
    );
}

if(!$cachethumbs)
    clearThumbCache();

if(!$cacheresized)
    clearResizedCache();

$parts = pathinfo($_SERVER['PHP_SELF']);
$current_working_directory = $parts['dirname'];

$hide_folders[] = '.';
$hide_folders[] = '..';
$hide_folders[] = '.git';
$hide_folders[] = 'cgi-bin';
$hide_folders[] = 'css';
$hide_folders[] = 'fonts';
$hide_folders[] = 'js';

//If a directory list request was made, parse it into the dir variable.
$dir='.';
if(isset($_GET['dir']))
    $dir = stripslashes(rawurldecode($_GET['dir']));

//Prevent requests for parent directories
if(substr($dir, 0, 2) == '..')
    $dir = '.';
if(substr($dir, 0, 1) == '/')
    $dir = '.';
if(!(strpos($dir, '..') === false))
    $dir = '.';

//Get the name of the current folder.
$patharr = explode('/',$dir);
$current = $patharr[count($patharr)-1];

//If a file was requested for display, read the path into the $display_file variable
if(array_key_exists('file', $_GET))
{
    $display_file = preg_replace(
        '/\.\//',
        getCurrentWorkingDirectory() . '/',
        stripslashes($_GET['file'])
    );
    $resize_file = stripslashes($_GET['file']);
    $disk_file = $_GET['file'];
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
        $location = getCurrentWorkingDirectory();
        if(empty($location))
            $location = '/';
        header("Location: " . $location);
    }
    if(isImage(basename($_GET['file'])))
        $current = basename($_GET['file']);
    else
        exit;
}
?>
