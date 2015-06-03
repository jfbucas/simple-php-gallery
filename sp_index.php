<?php
require('sp_helper_functions.php');
require('sp_def_vars.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?= getPageTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="<?= getCurrentWorkingDirectory();?>/sp_styles.css" media="screen" />
</head>
<body>
<h1><?= getPageTitle(); ?></h1>

<p id="breadcrumb"><?php getBreadCrumbs();?></p>

<?php
//If a file was requested for viewing, output it
if($display_file != '') {
?>
    <div id="prevnext"><?php getPrevAndNext();?></div><div style="clear:both;"></div>
    <div id="image"><?php getFile(); ?></div>
<?php
    if(descriptionExists()) {
?>
        <p id="desc"><?php getDescription();?></p>
<?php
    }
}
//Otherwise, a directory listing request was made.  Display the thumbnail links.
else {
?>
    <div id="prevnext"><?php getPrevAndNextDir();?></div><div style="clear:both;"></div>
<?php
    //If this directory has a description, output it
    if(getDirDescription() != '') {
    ?>
        <div id="dirdesc">
        <?php echo getDirDescription(); ?>

        </div>
    <?php
    }
    //If there are sub-directories, list them.
    if(subDirExist()) {
    ?>
        <div id="directories"> <?php getDirLinks(); ?></div>
    <?php
    }
    ?>
    <div id="gallery">
<?php
    //Output thumbnail links to all images in this directory
    //getThumbnails();
    foreach(getImgLinks() as $link) {
        echo $link;
    }
?>
    </div>
<?php
}
?>
<p id="credit">
Powered by <a href="http://relativelyabsolute.com/spg/">Simple PHP Gallery</a> <?php echo VERSION ?>
</p>
</body>
</html>
