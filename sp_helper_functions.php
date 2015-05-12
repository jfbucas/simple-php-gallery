<?php
//This function will return a sorted array of all files and folders in the current directory
function getDirList($input_dir="")
{
	global $dir;
	if($input_dir == '')
		$input_dir = $dir;
	$dh  = opendir(stripslashes($input_dir));
	while (false !== ($filename = readdir($dh)))
	{
	  	$files[] = $filename;
	}
	@sort($files);
	return $files;
}

function subDirExist()
{
	global $dirlink;
	$exist = false;
	if(count($dirlink)!=0)
		$exist = true;
	return $exist;
}

function getImgLinks()
{
	global $imglink;
	return $imglink;
}

function getDirDescription()
{
	global $dir;
	global $descriptions;

	$path = explode('/',$dir);
	$description = $descriptions[$path[count($path)-1]]['desc'];
	return $description;
}

//This function generates the sub-directory links for the current directory.  it returns void.
function getDirLinks()
{
	global $dirlink;
    echo "<h2>Sous-répertoires</h2><ul>";
	foreach($dirlink as $link)
	{
		echo "<li>$link</li>";
	}
    echo "</ul>";
}

function getPageTitle() {
	global $page_title;
	echo $page_title;
}

function getCurrentWorkingDirectory() {
	$parts = pathinfo($_SERVER['PHP_SELF']);
	if($parts['dirname'] != '/')
		echo $parts['dirname'];
	else
		echo '';
}

function returnCurrentWorkingDirectory() {
	$parts = pathinfo($_SERVER['PHP_SELF']);
	if($parts['dirname'] != '/')
		return $parts['dirname'];
	else
		return '';
}

function gd_version() {
   static $gd_version_number = null;
   if ($gd_version_number === null) {
       // Use output buffering to get results from phpinfo()
       // without disturbing the page we're in.  Output
       // buffering is "stackable" so we don't even have to
       // worry about previous or encompassing buffering.
       ob_start();
       phpinfo(8);
       $module_info = ob_get_contents();
       ob_end_clean();
       if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i",
               $module_info,$matches)) {
           $gd_version_number = $matches[1];
       } else {
           $gd_version_number = 0;
       }
   }
   return $gd_version_number;
}

function getFile() {
	global $display_file, $resize_file, $maxwidth, $maxheight;
    global  $resize, $cacheresizedfolder, $cacheresized;
	$cached_img = $cacheresizedfolder
        . "/"
        . md5(substr($resize_file,2,strlen($resize_file)-2))
        . ".jpg";
	$path = pathinfo($_SERVER[PHP_SELF]);
	$path = $path['dirname'];
	if($path == '/')
		$path = '';
	if ( strpos($display_file, ".webm-00001.png") === false ) {
		if(!function_exists('imagecreate') || $maxwidth == 0 || $maxheight ==0 || !$resize)
			echo '<img id="single" src="' . $display_file;
		else
		{
            echo '<a href="' . $display_file . '" target="top"><img id="single" src="';
            if(
                $cacheresized
                && file_exists($cached_img)
                && sizeMatches($cached_img, 'full')
                && resizedCacheFilesizeMatch(
                    md5(substr($resize_file,2,strlen($resize_file)-2)),
                    filesize($resize_file)
                )
            )
                echo $path . '/' . $cached_img;
            else
                echo $path . "/sp_resize.php?source=" . $resize_file;
		}
		echo '" alt="';
		getDescription();
		if(function_exists('imagecreate') && $maxwidth > 0 && $maxheight > 0 && $resize)
			echo '" title="Cliquez pour voir en grand" /></a>';
		else
			echo '" />';
	} else {
		$video_file=substr( $display_file, 0, strpos($display_file, "-00001.png") );
		if ( strpos($display_file, "270.webm") )
			$video_orig=substr( $display_file, 0, strpos($display_file, "270.webm") ) . ".mp4";
		if ( strpos($display_file, "180.webm") )
			$video_orig=substr( $display_file, 0, strpos($display_file, "180.webm") ) . ".mp4";
		if ( strpos($display_file, "90.webm") )
			$video_orig=substr( $display_file, 0, strpos($display_file, "90.webm") ) . ".mp4";
		else
			$video_orig=substr( $display_file, 0, strpos($display_file, ".webm") ) . ".mp4";

		echo '<p><video controls="controls" poster="'
            . $display_file
            .'"  preload="none" src="'
            . $video_file
            . '"></p>';
		echo '<p><a href="'. $video_orig . '">Download Original Video</a></p>';

	}
}

function getDescription() {
	global $descriptions, $current;
	echo $descriptions[$current]["desc"];
}

function descriptionExists() {
	global $descriptions;
	global $current;
	$exists = false;
	if($descriptions[$current]["desc"] != '')
		$exists = true;
	return $exists;
}

function sizeMatches($image, $size='thumb') {
	global $maxthumbwidth, $maxthumbheight, $maxwidth, $maxheight;
	$match = false;
	if(file_exists($image))
	{
		$path = pathinfo($image);
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				$image=imagecreatefromjpeg($image);
				break;
			case "gif":
				$image=imagecreatefromgif($image);
				break;
			case "png":
				$image=imagecreatefrompng($image);
				break;
			default:
				break;
		}
		$x = imagesx($image);
		$y = imagesy($image);
		imagedestroy($image);
		if($size == 'thumb')
		{
			if(
                (($x == $maxthumbwidth) && ($y <= $maxthumbheight))
                || (($x <= $maxthumbwidth) && ($y == $maxthumbheight))
            )
				$match = true;
		}
		if($size == 'full')
		{
			if(
                (($x == $maxwidth) && ($y <= $maxheight))
                || (($x <= $maxwidth) && ($y == $maxheight))
            )
				$match = true;
		}
	}
	return $match;
}

function clearCache() {
	clearThumbCache();
	clearResizedCache();
}

function clearThumbCache() {
	global $cachefolder;
	rmdirr($cachefolder);
	@mkdir($cachefolder, 0755);
	$cache_ini = @fopen($cachefolder . "/cache.ini","a");
	@fclose($cache_ini);
}

function clearResizedCache() {
	global $cacheresizedfolder;
	rmdirr($cacheresizedfolder);
	@mkdir($cacheresizedfolder, 0755);
	$cache_ini = @fopen($cacheresizedfolder . "/resized_cache.ini","a");
	@fclose($cache_ini);
}

function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }
    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }
    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep delete directories
        if (is_dir("$dirname/$entry")) {
            rmdirr("$dirname/$entry");
        } else {
            unlink("$dirname/$entry");
        }
    }
    // Clean up
    $dir->close();
    return rmdir($dirname);
}

function cacheLinkMatch($hash)
{
	global $cache_ini, $cachethumbs, $modrewrite;
	$match = false;
	if($modrewrite && !eregi('sp_',$cache_ini[$hash]['url']))
		$match = true;
	if(!$modrewrite && eregi('sp_',$cache_ini[$hash]['url']))
		$match = true;
	return $match;
}

function cacheFilesizeMatch($hash, $filesize)
{
	global $cache_ini;
	$match = false;
	if($filesize == $cache_ini[$hash]['size'])
		$match = true;
	return $match;
}

function resizedCacheFilesizeMatch($hash, $filesize)
{
	global $resized_cache_ini;
	$match = false;
	if($filesize == $resized_cache_ini[$hash]['size'])
		$match = true;
	return $match;
}

function getPrevAndNextDir() {
	global $modrewrite, $precache, $resize;
	if ($_GET['dir'] == "")
		return;

	$files = getDirList('./' . dirname($_GET['dir']) . '/');

	foreach($files as $f)
	{
		if(is_dir('./' . dirname($_GET['dir']) . '/' . $f)) {
			if (( $f != '.' ) && ( $f != '..' ) && ($f != "cache") && ($f != "rcache"))
				$imgfiles[] = $f;
		}
	}
	$current_index = array_search(basename($_GET['dir']),$imgfiles);

	$prev_index = $current_index-1;
	if($prev_index == -1)
		$prev_index = count($imgfiles)-1;

	$next_index = $current_index+1;
	if($next_index == count($imgfiles))
		$next_index = 0;

	if($modrewrite)
	{
		$prev_link = "<a accesskey=\"-\" id=\"prev\" href=\""
            . returnCurrentWorkingDirectory()
            . "/folder/"
            . dirname($_GET['dir'])
            . '/'
            . $imgfiles[$prev_index]
            . "\">&laquo; Précédente [-]</a>";
		$next_link = "<a accesskey=\"=\" id=\"next\" href=\""
            . returnCurrentWorkingDirectory()
            . "/folder/"
            . dirname($_GET['dir'])
            . '/'
            . $imgfiles[$next_index]
            . "\">[+] Suivante &raquo;</a>"
            . "<a accesskey=\"+\" href=\""
            . returnCurrentWorkingDirectory()
            . "/folder/"
            . dirname($_GET['dir'])
            . '/'
            . $imgfiles[$next_index]
            . "\"></a>";
	}
	else
	{
		$prev_link = "<a accesskey=\"-\" id=\"prev\" href=\""
            . $_SERVER[PHP_SELF]
            . "?dir="
            . dirname($_GET['dir'])
            . '/'
            . $imgfiles[$prev_index]
            . "\">&laquo; Précédente [-]</a>";
		$next_link = "<a accesskey=\"=\" id=\"next\" href=\""
            . $_SERVER[PHP_SELF]
            . "?dir="
            . dirname($_GET['dir'])
            . '/'
            . $imgfiles[$next_index]
            . "\">[+] Suivante &raquo;</a>"
            . "<a accesskey=\"+\" href=\""
            . $_SERVER[PHP_SELF]
            . "?dir="
            . dirname($_GET['dir'])
            . '/'
            . $imgfiles[$next_index]
            . "\"></a>";
	}
	echo $prev_link . "\n" . $next_link;

	if($precache && !$resize)
	{
		$trueimagepath = dirname($_SERVER[PHP_SELF]) . "/" . dirname($_GET['dir']);
		echo "\n<img src=\""
            . $trueimagepath
            . "/"
            . $imgfiles[$prev_index]
            . "\" width=\"1\" height=\"1\" alt=\"\" border=\"0\" "
            . "style=\"position:absolute; top:0; left:0; visibility:hidden;\" />";
		echo "\n<img src=\""
            . $trueimagepath
            . "/"
            . $imgfiles[$next_index]
            . "\" width=\"1\" height=\"1\" alt=\"\" border=\"0\" "
            . "style=\"position:absolute; top:0; left:0; visibility:hidden;\" />\n";
	}
}

function getPrevAndNext() {
	global $modrewrite, $precache, $resize;

	$files = getDirList('./' . dirname($_GET['file']) . '/');

	foreach($files as $img)
	{
		if(eregi(".*(\.jpg|\.gif|\.png|\.jpeg)", $img))
			$imgfiles[] = $img;
	}

	$current_index = array_search(basename($_GET['file']),$imgfiles);

	$prev_index = $current_index-1;
	if($prev_index == -1)
		$prev_index = count($imgfiles)-1;

	$next_index = $current_index+1;
	if($next_index == count($imgfiles))
		$next_index = 0;

	if($modrewrite)
	{
		$prev_link = "<a accesskey=\"-\" id=\"prev\" href=\""
            . returnCurrentWorkingDirectory()
            . "/file/"
            . dirname($_GET['file'])
            . '/'
            . $imgfiles[$prev_index]
            . "\">&laquo; Précédente [-]</a>";
		$next_link = "<a accesskey=\"=\" id=\"next\" href=\""
            . returnCurrentWorkingDirectory()
            . "/file/"
            . dirname($_GET['file'])
            . '/'
            . $imgfiles[$next_index]
            . "\">[+] Suivante &raquo;</a>"
            . "<a accesskey=\"+\" href=\""
            . returnCurrentWorkingDirectory()
            . "/file/"
            . dirname($_GET['file'])
            . '/'
            . $imgfiles[$next_index]
            . "\"></a>";
	}
	else
	{
		$prev_link = "<a accesskey=\"-\" id=\"prev\" href=\""
            . $_SERVER[PHP_SELF]
            . "?file="
            . dirname($_GET['file'])
            . '/'
            . $imgfiles[$prev_index]
            . "\">&laquo; Précédente [-]</a>";
		$next_link = "<a accesskey=\"=\" id=\"next\" href=\""
            . $_SERVER[PHP_SELF]
            . "?file="
            . dirname($_GET['file'])
            . '/'
            . $imgfiles[$next_index]
            . "\">[+] Suivante &raquo;</a>"
            . "<a accesskey=\"+\" href=\""
            . $_SERVER[PHP_SELF]
            . "?file="
            . dirname($_GET['file'])
            . '/'
            . $imgfiles[$next_index]
            . "\"></a>";
	}
	echo $prev_link . "\n" . $next_link;

	if($precache && !$resize)
	{
		$trueimagepath = dirname($_SERVER[PHP_SELF]) . "/" . dirname($_GET['file']);
		echo "\n<img src=\""
            . $trueimagepath
            . "/"
            . $imgfiles[$prev_index]
            . "\" width=\"1\" height=\"1\" alt=\"\" border=\"0\" "
            . "style=\"position:absolute; top:0; left:0; visibility:hidden;\" />";
		echo "\n<img src=\""
            . $trueimagepath
            . "/"
            . $imgfiles[$next_index]
            . "\" width=\"1\" height=\"1\" alt=\"\" border=\"0\" "
            . "style=\"position:absolute; top:0; left:0; visibility:hidden;\" />\n";
	}
}

//This function generates the breadcrumb trail displayed at the top of the page.  it returns void.
function getBreadCrumbs()
{
	global $descriptions;
	global $dir;
	global $display_file;
	global $current_working_directory;
	global $title;
	global $modrewrite;

	$nodir = false;
	if($dir != '.')
		$patharr = explode('/', $dir);
	else
	{
		$cwd = returnCurrentWorkingDirectory() . '/';
		$path = @str_replace($cwd,'',$display_file);
		if(!$path)
			$path = $display_file;
		if($cwd == '/')
			$path = substr($display_file,1,strlen($display_file)-1);
		$patharr = explode('/', $path);
		$nodir = true;
	}
	$linkpath = '.';
	$counter = 0;
	echo "<strong>Vous voyez :</strong> <a href=\""
        . returnCurrentWorkingDirectory()
        . "/\"";
	if(
        (count($patharr) == 2 && isset($_GET['dir']))
        ||(count($patharr) == 1 && isset($_GET['file']))
    )
		echo " accesskey=\"u\"";
	echo ">" . $title . "</a> ";
	foreach($patharr as $folder)
	{
		if($descriptions[$folder]['title'] != '')
			$foldername = $descriptions[$folder]['title'];
		else
			$foldername = $folder;
		if(!eregi('^\.',$folder))
		{
			$linkpath .= "/$folder";
			if($patharr[count($patharr)-1] != $folder)
			{
				if($modrewrite)
				{
					echo '&raquo; <a href="'
                        . returnCurrentWorkingDirectory()
                        . '/folder/'
                        . $linkpath
                        . '"';
					if($counter == (count($patharr)-2))
						echo " accesskey=\"u\"";
					echo ">$foldername</a> ";
				}
				else
				{
					echo "&raquo; <a href=\"" . $_SERVER['PHP_SELF'] . "?dir=$linkpath\"";
					if($counter == count($patharr)-2)
						echo " accesskey=\"u\"";
					echo ">$foldername</a> ";
				}
			}
			else if($patharr[count($patharr)-1] == '')
				;
			else
			{
				if($nodir)
				{
					if($descriptions[$folder]['title'] != '')
						echo "&raquo; <strong>" . $descriptions[$folder]['title'] . "</strong>";
					else
						echo "&raquo; <strong>$folder</strong>";
				}
				else
					echo "&raquo; <strong>$foldername</strong>";
			}
		}
		$counter++;
	}
}

function getNumImages($dir)
{
	$num_images = 0;
	foreach(getDirList($dir) as $file)
	{
		if( eregi(".*(\.jpg|\.gif|\.png|\.jpeg)", $file))
		{
			$num_images++;
		}
	}
	return $num_images;
}

function getNumDir($directory)
{
	$num_dir = 0;
	foreach(getDirList($directory) as $item)
	{
		$path = $directory . '/' . $item;
		if(is_dir($path) && $item != '.' && $item != '..' && $item != $cachefolder)
			$num_dir++;
	}
	return $num_dir;
}

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
