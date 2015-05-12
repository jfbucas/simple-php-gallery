<?php
require('sp_config.php');

$source = stripslashes($_GET['source']);
$path = pathinfo($source);

//echo $source;

$hash = md5($source);

switch(strtolower($path["extension"])){
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
		$thumb = imagecreatetruecolor($maxthumbwidth,floor(imagesy($original)*$xratio));
	else
		$thumb = imagecreate($maxthumbwidth,floor(imagesy($original)*$xratio));
}
else {
	if($gd_version >= 2)
		$thumb = imagecreatetruecolor(floor(imagesx($original)*$yratio), $maxthumbheight);
	else
		$thumb = imagecreate(floor(imagesx($original)*$yratio), $maxthumbheight);
}

imagecopyresampled($thumb, $original, 0, 0, 0, 0, imagesx($thumb)+1,imagesy($thumb)+1,imagesx($original),imagesy($original));
imagedestroy($original);

//CACHE THE IMAGE IF CACHING HAS BEEN ENABLED
if($cachethumbs)
{
	imagejpeg($thumb, $cachefolder . "/" . $hash . ".jpg");
	$descriptions = @parse_ini_file('sp_descriptions.ini',true);
	$cache_ini = @parse_ini_file($cachefolder . "/cache.ini",true);
	$thisfolder = str_replace('sp_getthumb.php','',$_SERVER['PHP_SELF']);
	if($modrewrite)
		$url = $thisfolder . 'file/' . $source;
	else
		$url = $thisfolder . 'sp_index.php?file=' . $source;
		
	$cache_ini[$hash]['src'] = $thisfolder . $cachefolder . '/' . $hash . '.jpg';
	$cache_ini[$hash]['alt'] = $descriptions[$path['basename']]['desc'];
	$cache_ini[$hash]['url'] = $url;
	$cache_ini[$hash]['title'] = $descriptions[$path['basename']]['title'];
	$cache_ini[$hash]['size'] = filesize($source);
	write_ini_file($cachefolder . "/cache.ini", $cache_ini);
}


//RETURN A JPG TO THE BROWSER 
imagejpeg($thumb);
imagedestroy($thumb);


function write_ini_file($path, $assoc_array) {

   foreach ($assoc_array as $key => $item) {
       if (is_array($item)) {
           $content .= "\n[$key]\n";
           foreach ($item as $key2 => $item2) {
               $content .= "$key2 = \"$item2\"\n";
           }       
       } else {
           $content .= "$key = \"$item\"\n";
       }
   }       
  
   if (!$handle = fopen($path, 'w')) {
       return false;
   }
   if (!fwrite($handle, $content)) {
       return false;
   }
   fclose($handle);
   return true;
}
?>