<?php
require('sp_config.php');
require('sp_helper_functions.php');

$source = stripslashes($_GET['source']);
$path = pathinfo($source);

$hash = md5($source);

$original = imagecreatefrom_ext($source);
$thumb = imagecreatetruecolor($maxthumbwidth,$maxthumbheight);

if(imagesx($original) < imagesy($original)) {
    $cropped = imagecrop(
        $original,
        [
            'x' => 0,
            'y' => (imagesy($original) - imagesx($original)) / 2,
            'width' => imagesx($original),
            'height' => imagesx($original)
        ]
    );
} else {
    $cropped = imagecrop(
        $original,
        [
            'x' => (imagesx($original) - imagesy($original)) / 2,
            'y' => 0,
            'width' => imagesy($original),
            'height' => imagesy($original)
        ]
    );
}

imagecopyresampled(
    $thumb,
    $cropped,
    0, 0,
    0, 0,
    imagesx($thumb) + 1, imagesy($thumb) + 1,
    imagesx($cropped), imagesy($cropped)
);
imagedestroy($original);
imagedestroy($cropped);

//Cache the image if caching has been enabled
if($cachethumbs) {
    imagejpeg($thumb, $cachefolder . "/" . $hash . ".jpg");
    $cache_ini = @parse_ini_file($cachefolder . "/cache.ini",true);
    $thisfolder = str_replace('sp_getthumb.php','',$_SERVER['PHP_SELF']);
    if($modrewrite)
        $url = $thisfolder . 'file/' . $source;
    else
        $url = $thisfolder . 'sp_index.php?file=' . $source;

    $cache_ini[$hash]['src'] = $thisfolder . $cachefolder . '/' . $hash . '.jpg';
    $cache_ini[$hash]['alt'] = getDescOrName($path['basename']);
    $cache_ini[$hash]['url'] = $url;
    $cache_ini[$hash]['title'] = getDescOrName($path['basename']);
    $cache_ini[$hash]['size'] = filesize($source);
    write_ini_file($cachefolder . "/cache.ini", $cache_ini);
}

//Return a JPG to the browser
imagejpeg($thumb);
imagedestroy($thumb);

?>