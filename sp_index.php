<?php
/*
Simple PHP Gallery by Paul Griffin.

This work is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike License. 
To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/1.0/ 
or send a letter to Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.

What this means:

You are free:
    * to copy, distribute, display, and perform the work
    * to make derivative works

Under the following conditions:
	
		* Attribution. You must give the original author credit.
	
		* Noncommercial. You may not use this work for commercial purposes.
	
		* Share Alike. If you alter, transform, or build upon this work, you may distribute the 
			resulting work only under a license identical to this one.

    * For any reuse or distribution, you must make clear to others the license terms of this work.
    
    * Any of these conditions can be waived if you get permission from the author.

*/
require('sp_helper_functions.php');
require('sp_def_vars.php');

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?php getPageTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="<?php getCurrentWorkingDirectory();?>/sp_styles.css" media="screen" />
</head>
<body>
<h1><?php getPageTitle(); ?></h1>

<p id="breadcrumb"><?php getBreadCrumbs();?></p>

<?php
//IF A FILE WAS REQUESTED FOR VIEWING, OUTPUT IT
if($display_file != '') 
{
?>	
	<div id="prevnext"><?php getPrevAndNext();?></div><div style="clear:both;"></div>
	<div id="image"><?php getFile(); ?></div>
<?php
	if(descriptionExists())
	{
?>
		<p id="desc"><?php getDescription();?></p>
<?php
	}
}

//OTHERWISE, A DIRECTORY LISTING REQUEST WAS MADE.  DISPLAY THE THUMBNAIL LINKS.
else 
{
?>
	<div id="prevnext"><?php getPrevAndNextDir();?></div><div style="clear:both;"></div>
<?php
	//IF THIS DIRECTORY HAS A DESCRIPTION, OUTPUT IT
	if(getDirDescription() != '')
	{
	?>
		<div id="dirdesc">
		<?php echo getDirDescription(); ?>
		
		</div>
	<?php	
	}
	//IF THERE ARE SUB-DIRECTORIES, LIST THEM.
	if(subDirExist())
	{
	?>
		<div id="directories"> <?php getDirLinks(); ?></div>
	<?php
	}
	?>
	<div id="gallery">
<?php
	//OUTPUT THUMBNAIL LINKS TO ALL IMAGES IN THIS DIRECTORY
	//getThumbnails();
	foreach(getImgLinks() as $link)
	{
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
