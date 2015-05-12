<?php
$title = 'Nos Photos';

//Thumbnail Settings
$maxthumbwidth = 240;
$maxthumbheight = 240;

//Main Image Resize Settings
$resize = true;
$maxwidth = 1024;
$maxheight = 768;

//$modrewrite Settings
$modrewrite = true;

//Cache Settings
$cachethumbs = true;
$cachefolder = 'cache';

$cacheresized = true;
$cacheresizedfolder = 'rcache';

$precache = true;

//Folder Hiding
$hide_folders[] = $cachefolder;
$hide_folders[] = $cacheresizedfolder;

//Miscellaneous Settings
$showfolderdetails = true;
$showimgtitles = true;
$alignimages = true;
$gd_version = "2.0.28";
?>
