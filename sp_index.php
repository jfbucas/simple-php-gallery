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

<p id="breadcrumb"><?= getBreadCrumbs();?></p>

<?php
//If a file was requested for viewing, output it
if($display_file != '') {
?>
    <div id="prevnext"><?= getPrevAndNext();?><?= getPrevAndNextImgCache();?></div>
    <div style="clear:both;"></div>
    <div id="image"><?= getFile(); ?></div>
<?php
    if(getDescription($current) != '') {
?>
        <p id="desc"><?= getDescription($current);?></p>
<?php
    }
}
//Otherwise, a directory listing request was made.  Display the thumbnail links.
else {
?>
    <div id="prevnext"><?= getPrevAndNextDir();?></div>
    <div style="clear:both;"></div>
<?php
    //If this directory has a description, output it
    if(getDirDescription() != '') {
    ?>
        <div id="dirdesc"><?= getDirDescription(); ?></div>
    <?php
    }
    //If there are sub-directories, list them.
    if(count($dirlink)!=0) { ?>
        <div id="directories">
            <h2>Sous-répertoires</h2>
            <ul>
                <?php foreach($dirlink as $link) { ?>
                    <li><?= $link ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php
    } ?>
    <div id="gallery">
<?php
    //Output thumbnail links to all images in this directory
    foreach($imglink as $link) {
        echo $link;
    }
?>
    </div>
<?php
}
?>
<p id="credit">
Powered by <a href="http://relativelyabsolute.com/spg/">Simple PHP Gallery</a> <?= VERSION ?>
</p>
</body>
</html>
