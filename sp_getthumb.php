<?php
require('sp_config.php');
require('sp_helper_functions.php');

$source = stripslashes($_GET['source']);
$path = pathinfo($source);

$hash = md5($source);

switch(strtolower($path["extension"])) {
    case "jpeg":
    case "jpg":
        $original=imagecreatefromjpeg($source);
        break;
    case "gif":
        $original=imagecreatefromgif($source);
        break;
    case "png":
        $original=imagecreatefrompng($source);
        break;
    default:
        break;
}
$xratio = $maxthumbwidth/(imagesx($original));
$yratio = $maxthumbheight/(imagesy($original));

if($xratio < $yratio) {
    if($gd_version >= 2)
        $thumb = imagecreatetruecolor(
            $maxthumbwidth,
            floor(imagesy($original)*$xratio)
        );
    else
        $thumb = imagecreate(
            $maxthumbwidth,
            floor(imagesy($original)*$xratio)
        );
}
else {
    if($gd_version >= 2)
        $thumb = imagecreatetruecolor(
            floor(imagesx($original)*$yratio),
            $maxthumbheight
        );
    else
        $thumb = imagecreate(
            floor(imagesx($original)*$yratio),
            $maxthumbheight
        );
}

imagecopyresampled(
    $thumb,
    $original,
    0, 0,
    0, 0,
    imagesx($thumb)+1,imagesy($thumb)+1,
    imagesx($original),imagesy($original)
);
imagedestroy($original);

//Cache the image if caching has been enabled
if($cachethumbs) {
    imagejpeg($thumb, $cachefolder . "/" . $hash . ".jpg");
    $descriptions = @parse_ini_file('sp_descriptions.ini',true);
    $cache_ini = @parse_ini_file($cachefolder . "/cache.ini",true);
    $thisfolder = str_replace('sp_getthumb.php','',$_SERVER['PHP_SELF']);
    if($modrewrite)
        $url = $thisfolder . 'file/' . $source;
    else
        $url = $thisfolder . 'sp_index.php?file=' . $source;

    $cache_ini[$hash]['src'] = $thisfolder . $cachefolder . '/' . $hash . '.jpg';
    $cache_ini[$hash]['alt'] = getDescription($path['basename']);
    $cache_ini[$hash]['url'] = $url;
    $cache_ini[$hash]['title'] = getTitle($path['basename']);
    $cache_ini[$hash]['size'] = filesize($source);
    write_ini_file($cachefolder . "/cache.ini", $cache_ini);
}

//Return a JPG to the browser
imagejpeg($thumb);
imagedestroy($thumb);

?>