<?php
require('sp_config.php');

define("VERSION","1.1");


//DETECT AND STORE GD VERSION IF THIS IS THE FIRST TIME THE SCRIPT IS RUN.
if(!isset($gd_version))
{
	$config_file = @fopen('sp_config.php','r');
	$contents = fread($config_file, filesize('sp_config.php'));
	@fclose($config_file);
	$gd_version = gd_version();
	$contents = str_replace('?>','$gd_version = "' . $gd_version . "\";\n?>",$contents);
	$config_file = @fopen('sp_config.php','w');
	@fwrite($config_file, $contents);
	//header("Location: $_SERVER[PHP_SELF]");
	//echo 'GD Detected!  Version: '.$gd_version.'.  Please reload this page to view your gallery.  You will not see this message again.';
}

//CREATE THE CACHE FOLDER IF CACHING IS ENABLED AND IT DOES NOT ALREADY EXIST.
//IF THE FOLDER CANNOT BE CREATED, DISABLE CACHING

if($cachethumbs && !file_exists($cachefolder))
{
	if(!mkdir($cachefolder, 0755))
	{
			$cachethumbs = false;
	}
}
if($cacheresized && !file_exists($cacheresizedfolder))
{
	if(!mkdir($cacheresizedfolder, 0755))
		$cacheresized = false;	
}

//CREATE THE cache.ini FILE IF IT DOES NOT EXIST.  PARSE INTO THE $cache_ini VARIABLE.
if($cachethumbs)
{
	$cache_ini = @fopen($cachefolder . "/cache.ini","a");
	@fclose($cache_ini);
	$cache_ini = @parse_ini_file($cachefolder . "/cache.ini",true);
}

//CREATE THE resized_cache.ini FILE IF IT DOES NOT EXIST.  PARSE INTO THE $resized_cache_ini VARIABLE.

if($cacheresized)
{
	$resized_cache_ini = @fopen($cacheresizedfolder . "/resized_cache.ini","a");
	@fclose($resized_cache_ini);
	$resized_cache_ini = @parse_ini_file($cacheresizedfolder . "/resized_cache.ini",true);
}

if(!$cachethumbs)
	clearThumbCache();
	
if(!$cacheresized)
	clearResizedCache();

//PARSE THE DESCRIPTIONS FILE
$descriptions = @parse_ini_file('sp_descriptions.ini',true);
    if (! $descriptions) {
      die('Unable to read sp_descriptions.ini file.');
}

$parts = pathinfo($_SERVER['PHP_SELF']);
$current_working_directory = $parts['dirinfo'];

$hide_folders[] = '.';
$hide_folders[] = '..';
$hide_folders[] = $cachefolder;
$hide_folders[] = $cacheresizedfolder;

//IF A DIRECTORY LIST REQUEST WAS MADE, PARSE IT INTO THE $dir VARIABLE.

$dir='.';
if(isset($_GET['dir']))
	$dir = stripslashes($_GET['dir']);
	
//PREVENT REQUESTS FOR PARENT DIRECTORIES
if(eregi('^(\.\.)',$dir))
	$dir='.';
if(eregi('^/',$dir))
	$dir='.';
if(!(strpos($dir,'..') === false))
	$dir='.';
	
//GET THE NAME OF THE CURRENT FOLDER.
$patharr = explode('/',$dir);
$current = $patharr[count($patharr)-1];
$alias = '';

//GET THE FOLDER ALIAS, IF IT EXISTS
if($descriptions[$current]['title'] != '')
	$alias = $descriptions[$current]['title'];
else
	$alias = $current;

//IF A FILE WAS REQUESTED FOR DISPLAY, READ THE PATH INTO THE $display_file VARIABLE
$display_file = eregi_replace('\./',returnCurrentWorkingDirectory() . '/',stripslashes($_GET['file']));
$resize_file = stripslashes($_GET['file']);

if($display_file != '')
{
	if(!file_exists($resize_file))
	{
		$hash = md5(substr($resize_file,2,strlen($resize_file)-2));
		if($cachethumbs)
		{
			@unlink($cachefolder . "/" . $hash . ".jpg");
			unset($cache_ini[$hash]);
			write_ini_file($cachefolder . "/cache.ini",$cache_ini);
		}
		if($cacheresized)
		{
			@unlink($cacheresizedfolder . "/" . $hash . ".jpg");
		}
		$location = returnCurrentWorkingDirectory();
		if(empty($location))
			$location = '/';
		header("Location: " . $location);
	}
	if(eregi(".*(\.jpg|\.gif|\.png|\.jpeg)",basename($_GET['file'])))
		$current = basename($_GET['file']);
	else
	{
		exit;
	}
	if($descriptions[$current]['title'] != '')
	{
		$alias = $descriptions[$current]['title'];
	}
	else
		$alias = $current;
}

if($alias != '.' && $alias != '')
	$page_title = $title . ": " . $alias;
else 
	$page_title = $title;
	
if(!empty($_GET['dir']) || (empty($_GET['dir']) && empty($_GET['file'])))
{
	$imglink = array(); //AN ARRAY TO HOLD LINKS TO THE IMAGES
	$dirlink = array(); //AN ARRAY TO HOLD LINKS TO SUB-DIRECTORIES

	foreach(getDirList() as $file)
	{
		$path = $dir . "/" . $file;
		$webpath = substr($path,2,strlen($path)-2);
		
		//IF THE CURRENT ITEM IS AN IMAGE, ADD A THE LINK TEXT TO THE $imglink ARRAY
		if( eregi(".*(\.jpg|\.gif|\.png|\.jpeg)", $file))
		{
			$cached_img = $cachefolder . "/" . md5($webpath) . ".jpg";
			$divwidth = $maxthumbwidth+4;
			$divheight = $maxthumbheight+24;
			if($showimgtitles)
				$divheight += 16;
			if($alignimages)
				$link = '<div class="imgwrapper" style="height:' . $divheight . 'px;width:' . $divwidth .'px;text-align:center">';
			else
				$link = '<div class="imgwrapper" style="height:' . $divheight . 'px;">';
			if($modrewrite)
			{
				$link .= "<a href=\"" . returnCurrentWorkingDirectory() . "/file/$webpath\">";
				if($cachethumbs && file_exists($cached_img) && sizeMatches($cached_img) && cacheLinkMatch(md5($webpath)) && cacheFilesizeMatch(md5($webpath),filesize($webpath)))
					$link .= "<img src=\"" . returnCurrentWorkingDirectory() . '/' . $cached_img . "\" alt=\"". $descriptions[$file]['title'] . "\" />";
				else
					$link .= "<img src=\"" . returnCurrentWorkingDirectory() . "/thumb/$webpath\" alt=\"". $descriptions[$file]['title'] . "\" />";		
			}
			else
			{
				$link .= "<a href=\"$_SERVER[PHP_SELF]?file=$path\">";
				if($cachethumbs && file_exists($cached_img) && sizeMatches($cached_img) && cacheLinkMatch(md5($webpath)) && cacheFilesizeMatch(md5($webpath),filesize($webpath)))
					$link .= "<img src=\"" . $current_working_directory . $cached_img . "\" alt=\"". $descriptions[$file]['title'] . "\" />";
				else
					$link .= "<img src=\"sp_getthumb.php?source=$webpath\" alt=\"". $descriptions[$file]['title'] . "\" />";
			}
			if($showimgtitles)
				if($descriptions[$file]['title'] != '')
					$link .= '<span>' . $descriptions[$file]['title'] . '</span>';
				else
					$link .= "<span>$file</span>";
			$link .= "</a></div>\n\t";
			$imglink[] = $link;
		}
		//IF THE CURRENT ITEM IS A DIRECTORY, ADD THE LINK TEXT TO THE $dirlink ARRAY
		else if(is_dir($path) && !in_array($file, $hide_folders) && ($file != ".git"))
		{
			if($descriptions[$file]['title'] != '')
				$file = $descriptions[$file]['title'];
			if($modrewrite)
				$dir_string = "<a href=\"" . returnCurrentWorkingDirectory() . "/folder/$webpath\">" . $file . "</a>";		
			else
				$dir_string = "<a href=\"" . $_SERVER['PHP_SELF'] . "?dir=$path\">" . $file . "</a>";
			if($showfolderdetails)
			{
				$num_images = getNumImages($path);
				$num_dir = getNumDir($path);
				$img_s = ($num_images == 1) ? '':'s';
				$dir_s = ($num_dir == 1) ? '':'s';
				if($num_images != 0 || $num_dir != 0)
					$dir_string .= " (";
				if($num_images != 0)
				{
					$dir_string .= $num_images . " image" . $img_s;
					if($num_dir != 0)
						$dir_string .= ', ';
					else
						$dir_string .= ')';
				}
				if($num_dir != 0)
				{
					$dir_string .= $num_dir . " sous-répertoire" . $dir_s . ')';
				}				
			}
			$dirlink[$file] = $dir_string;
		}
		//IF THE CURRENT ITEM IS NOT AN IMAGE AND IS NOT A DIRECTORY, IGNORE IT
	}
    uksort($dirlink, "strnatcasecmp");
    $dirlink = array_reverse($dirlink);
}
?>
