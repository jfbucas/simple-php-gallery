<?php

function isImage($file) {
    return preg_match("/.*(\.jpg|\.gif|\.png|\.jpeg)/", $file);
}

function getTitle($file) {
    global $descriptions;
    
    $my_title = $file;
    if(array_key_exists($file, $descriptions))
        if(array_key_exists('title', $descriptions[$file]))
            $my_title = $descriptions[$file]['title'];

    return $my_title;
}

function getDescription($file) {
    global $descriptions;
    
    $description = '';
    if(array_key_exists($file, $descriptions))
        if(array_key_exists('desc', $descriptions[$file]))
            $description = $descriptions[$file]['desc'];

    return $description;
}

//Return a sorted array of all files and folders in the current directory
function getFullDirList($input_dir="") {
    global $dir;
    if($input_dir == '')
        $input_dir = $dir;
    $dh  = opendir(stripslashes($input_dir));
    while (false !== ($filename = readdir($dh))) {
          $files[] = $filename;
    }
    @sort($files);
    return $files;
}

function getDirDescription() {
    global $dir;
    $path = explode('/',$dir);
    return getDescription($path[count($path)-1]);
}

function getPageTitle() {
    global $title, $current;
    
    $file_title = getTitle($current);
    if($file_title != '.' && $file_title != '')
        $page_title = $title . " : " . $file_title;
    else
        $page_title = $title;
        
    return $page_title;
}

function getCurrentWorkingDirectory() {
    $parts = pathinfo($_SERVER['PHP_SELF']);
    if($parts['dirname'] != '/')
        return $parts['dirname'];
    else
        return '';
}

function getDirList(){
    global $dir, $hide_folders, $modrewrite, $showfolderdetails;

    $cwd = getCurrentWorkingDirectory();
    $fullDirList = getFullDirList();
    uksort($fullDirList, "strnatcasecmp");
    $fullDirList = array_reverse($fullDirList);
    
    $dirList = array();
    foreach($fullDirList as $file) {
        $path = $dir . "/" . $file;
        $webpath = substr($path,2,strlen($path)-2);
        $filetitle = getTitle($file);

        //If the current item is a directory, add the link text to the array
        if(is_dir($path) && !in_array($file, $hide_folders)) {
            if($modrewrite) {
                $url = $cwd . "/folder/" . $webpath;
            }
            else {
                $url = $_SERVER['PHP_SELF'] . "?dir=" . $path;
            }
            if($showfolderdetails) {
                $num_images = getNumImages($path);
                $num_dir = getNumDir($path);
            }
            $dirList[] = array(
                'url' => $url,
                'filetitle' => $filetitle,
                'num_images' => $num_images,
                'num_dir' => $num_dir,
            );
        }
    }
    return $dirList;
}

function getImgList() {
    global $dir, $modrewrite, $cachethumbs, $maxthumbwidth, $maxthumbheight;
    global $cachefolder, $showimgtitles, $alignimages;

    $cwd = getCurrentWorkingDirectory();
    $imglink = array(); //An array to hold links to the images
    foreach(getFullDirList() as $file) {
        $path = $dir . "/" . $file;
        $webpath = substr($path, 2, strlen($path) - 2);
        $filetitle = getTitle($file);

        //If the current item is an image, add a the link text to the array
        if( isImage($file)) {
            $cached_img = $cachefolder . "/" . md5($webpath) . ".jpg";
            $divwidth = $maxthumbwidth + 4;
            $divheight = $maxthumbheight + 24;
            if($showimgtitles)
                $divheight += 16;
            if($modrewrite) {
                $url = $cwd . "/file/" . $webpath;
                if($cachethumbs
                    && file_exists($cached_img)
                    && sizeMatches($cached_img)
                    && cacheLinkMatch(md5($webpath))
                    && cacheFilesizeMatch(md5($webpath),filesize($webpath))
                ) {
                    $thumbnail_url = $cwd . '/' . $cached_img;
                }
                else {
                    $thumbnail_url = $cwd . "/thumb/" . $webpath;
                }
            }
            else {
                $url = $_SERVER['PHP_SELF'] . "?file=" . $path;
                if($cachethumbs
                    && file_exists($cached_img)
                    && sizeMatches($cached_img)
                    && cacheLinkMatch(md5($webpath))
                    && cacheFilesizeMatch(md5($webpath),filesize($webpath))
                ) {
                    $thumbnail_url = $cwd . '/' . $cached_img;
                }
                else {
                    $thumbnail_url = "sp_getthumb.php?source=" . $webpath;
                }
            }
            $imglink[] = array(
                'url' => $url,
                'thumbnail_url' => $thumbnail_url,
                'filetitle' => $filetitle,
                'divwidth' => $divwidth,
                'divheight' => $divheight,
            );
        }
    }
    return $imglink;
}

function getFile() {
    global $display_file, $resize_file, $maxwidth, $maxheight;
    global $resize, $cacheresizedfolder, $cacheresized;
    global $current;

    $fileLink = '';
    $cached_img = $cacheresizedfolder
        . "/"
        . md5(substr($resize_file,2,strlen($resize_file)-2))
        . ".jpg";
    $path = pathinfo($_SERVER['PHP_SELF']);
    $path = $path['dirname'];
    if($path == '/')
        $path = '';
    if ( strpos($display_file, ".webm-00001.png") === false ) {
    
        $linkType = 'img';
        $video_poster = '';

        if(!function_exists('imagecreate')
            || $maxwidth == 0
            || $maxheight == 0
            || !$resize
        ) {
            $url = $display_file;
            $target_url = "";
        }
        else {
            $target_url = $display_file;
            if(
                $cacheresized
                && file_exists($cached_img)
                && sizeMatches($cached_img, 'full')
                && resizedCacheFilesizeMatch(
                    md5(substr($resize_file,2,strlen($resize_file)-2)),
                    filesize($resize_file)
                )
            ) {
                $url = $path . '/' . $cached_img;
            }
            else {
                $url = $path . "/sp_resize.php?source=" . $resize_file;
            }
        }
        $desc = getDescription($current);
    }
    else {
        $linkType = 'video';
        $video_file = substr($display_file, 0, strpos($display_file, "-00001.png") );
        if ( strpos($display_file, "270.webm") )
            $video_orig = substr($display_file, 0, strpos($display_file, "270.webm") )
                . ".mp4";
        if ( strpos($display_file, "180.webm") )
            $video_orig = substr($display_file, 0, strpos($display_file, "180.webm") )
                . ".mp4";
        if ( strpos($display_file, "90.webm") )
            $video_orig = substr($display_file, 0, strpos($display_file, "90.webm") )
                . ".mp4";
        else
            $video_orig = substr($display_file, 0, strpos($display_file, ".webm") )
                . ".mp4";

        $url = $video_file;
        $target_url = $video_orig;
        $video_poster = $display_file;
    }
    return array(
        'linkType' => $linkType,
        'url' => $url,
        'target_url' => $target_url,
        'video_poster' => $video_poster,
        'desc' => $desc,
    );
}

function sizeMatches($image, $size='thumb') {
    global $maxthumbwidth, $maxthumbheight, $maxwidth, $maxheight;
    
    $match = false;
    if(file_exists($image)) {
        $path = pathinfo($image);
        switch(strtolower($path["extension"])) {
            case "jpeg":
            case "jpg":
                $image = imagecreatefromjpeg($image);
                break;
            case "gif":
                $image = imagecreatefromgif($image);
                break;
            case "png":
                $image = imagecreatefrompng($image);
                break;
            default:
                break;
        }
        $x = imagesx($image);
        $y = imagesy($image);
        imagedestroy($image);
        if($size == 'thumb') {
            if(
                (($x == $maxthumbwidth) && ($y <= $maxthumbheight))
                || (($x <= $maxthumbwidth) && ($y == $maxthumbheight))
            )
                $match = true;
        }
        if($size == 'full') {
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

function rmdirr($dirname) {
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
        }
        else {
            unlink("$dirname/$entry");
        }
    }
    // Clean up
    $dir->close();
    return rmdir($dirname);
}

function cacheLinkMatch($hash) {
    global $cache_ini, $cachethumbs, $modrewrite;
    $match = false;
    if(array_key_exists($hash, $cache_ini))
    {
        if($modrewrite && !strpos($cache_ini[$hash]['url'], 'sp_'))
            $match = true;
        if(!$modrewrite && strpos($cache_ini[$hash]['url'], 'sp_'))
            $match = true;
    }
    return $match;
}

function cacheFilesizeMatch($hash, $filesize) {
    global $cache_ini;
    $match = false;
    if(array_key_exists($hash, $cache_ini))
        if($filesize == $cache_ini[$hash]['size'])
            $match = true;
    return $match;
}

function resizedCacheFilesizeMatch($hash, $filesize) {
    global $resized_cache_ini;
    $match = false;
    if(array_key_exists($hash, $resized_cache_ini))
        if($filesize == $resized_cache_ini[$hash]['size'])
            $match = true;
    return $match;
}

function getPrevAndNextDir() {
    global $modrewrite, $precache, $resize;
    if(!array_key_exists('dir', $_GET))
        return;

    $cwd = getCurrentWorkingDirectory();
    $dirOfDir = dirname($_GET['dir']);
    $files = getFullDirList('./' . $dirOfDir . '/');

    foreach($files as $f) {
        if(is_dir('./' . $dirOfDir . '/' . $f)) {
            if (( $f != '.' ) && ( $f != '..' ) && ($f != "cache") && ($f != "rcache"))
                $imgfiles[] = $f;
        }
    }
    $current_index = array_search(basename($_GET['dir']), $imgfiles);

    $prev_index = $current_index-1;
    if($prev_index == -1)
        $prev_index = count($imgfiles)-1;

    $next_index = $current_index+1;
    if($next_index == count($imgfiles))
        $next_index = 0;

    if($modrewrite) {
        $url_prefix = $cwd . "/folder/" . $dirOfDir . '/';
    }
    else {
        $url_prefix = $_SERVER[PHP_SELF] . "?dir=" . $dirOfDir . '/';
    }
    
    return array(
        'prev' => $url_prefix . $imgfiles[$prev_index],
        'next' => $url_prefix . $imgfiles[$next_index],
    );
}

function getPrevAndNext() {
    global $modrewrite, $precache, $resize;

    $cwd = getCurrentWorkingDirectory();
    $dirOfFile = dirname($_GET['file']);
    $files = getFullDirList('./' . $dirOfFile . '/');

    foreach($files as $img) {
        if(isImage($img))
            $imgfiles[] = $img;
    }

    $current_index = array_search(basename($_GET['file']), $imgfiles);

    $prev_index = $current_index - 1;
    if($prev_index == -1)
        $prev_index = count($imgfiles) - 1;

    $next_index = $current_index + 1;
    if($next_index == count($imgfiles))
        $next_index = 0;

    if($modrewrite) {
        $url_prefix = $cwd . "/file/" . $dirOfFile . '/';
    }
    else {
        $url_prefix = $_SERVER[PHP_SELF] . "?file=" . $dirOfFile . '/';
    }
    
    return array(
        'prev' => $url_prefix . $imgfiles[$prev_index],
        'next' => $url_prefix . $imgfiles[$next_index],
    );
}

//Generates the breadcrumb trail displayed at the top of the page.
function getBreadCrumbs() {
    global $dir;
    global $display_file;
    global $current_working_directory;
    global $title;
    global $modrewrite;
    
    $cwd = getCurrentWorkingDirectory();
    $links = array();
    $nodir = false;
    if($dir != '.')
        $patharr = explode('/', $dir);
    else {
        $path = @str_replace($cwd . '/', '', $display_file);
        if(!$path)
            $path = $display_file;
        if($cwd == '')
            $path = substr($display_file,1,strlen($display_file)-1);
        $patharr = explode('/', $path);
        $nodir = true;
    }
    $linkpath = '.';
    $counter = 0;
    $accesskey = "";
    if(
        (count($patharr) == 2 && isset($_GET['dir']))
        ||(count($patharr) == 1 && isset($_GET['file']))
    ) {
        $accesskey = ' accesskey="u" ';
    }
    $links[] = array(
        'url' => $cwd . "/",
        'title' => $title,
        'accesskey' => $accesskey,
        'first' => true,
        );
    foreach($patharr as $folder) {
        $accesskey = "";
        $url = "";
        $foldername = getTitle($folder);
        if(!(substr($folder, 0, 1) == '.')) {
            $linkpath .= "/$folder";
            if($patharr[count($patharr)-1] != $folder) {
                if($modrewrite) {
                    $url = $cwd . '/folder/' . $linkpath;
                }
                else {
                    $url = $_SERVER['PHP_SELF'] . "?dir=" . $linkpath;
                }
                if($counter == (count($patharr)-2)) {
                    $accesskey = ' accesskey="u" ';
                }
                $links[] = array(
                    'url' => $url,
                    'title' => getTitle($folder),
                    'accesskey' => $accesskey,
                    'first' => false,
                    );
            }
            else 
                if( ! ($patharr[count($patharr)-1] == '')) {
                    $links[] = array(
                        'url' => "",
                        'title' => getTitle($folder),
                        'accesskey' => "",
                        'first' => false,
                        );
                }
        }
        $counter++;
    }
    return $links;
}

function getNumImages($dir) {
    $num_images = 0;
    foreach(getFullDirList($dir) as $file) {
        if( isImage($file)) {
            $num_images++;
        }
    }
    return $num_images;
}

function getNumDir($directory) {
    global $cachefolder;
    $num_dir = 0;
    foreach(getFullDirList($directory) as $item) {
        $path = $directory . '/' . $item;
        if(is_dir($path)
            && $item != '.'
            && $item != '..'
            && $item != $cachefolder
        )
            $num_dir++;
    }
    return $num_dir;
}

function write_ini_file($path, $assoc_array) {
    $content = '';
    foreach ($assoc_array as $key => $item) {
        if (is_array($item)) {
            $content .= "\n[$key]\n";
            foreach ($item as $key2 => $item2) {
                $content .= "$key2 = \"$item2\"\n";
            }
        }
        else {
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
