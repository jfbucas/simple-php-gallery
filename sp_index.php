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

<p id="breadcrumb"><strong>Vous voyez :</strong>
<?php foreach( getBreadCrumbs() as $link): ?>
    <?php if( ! $link['first']): ?>&raquo;<?php endif; ?>
    <a href="<?= $link['url'] ?>" <?= $link['accesskey'] ?>><?= $link['title'] ?></a>
<?php endforeach; ?>
</p>

<?php if($display_file != ''): //Image requested ?>

    <div id="prevnext">
        <?php $prevNext = getPrevAndNext(); ?>
        <a accesskey="-" id="prev" href="<?= $prevNext['prev'] ?>">&laquo; Précédente [-]</a>
        <a accesskey="+" id="next" href="<?= $prevNext['next'] ?>">[+] Suivante &raquo;</a>
    </div>
    <div style="clear:both;"></div>
    <?php $fileInfo = getFile(); ?>
    <div id="image">
        <?php if( $fileInfo['linkType'] == 'img'): ?>
            <?php if( $fileInfo['target_url'] != ''): ?><a href="<?= $fileInfo['target_url'] ?>">
            <?php endif; ?>
            <img id="single" src="<?= $fileInfo['url'] ?>" alt="<?= $fileInfo['desc'] ?>"
             <?php if( $fileInfo['target_url'] != ''): ?>
             title="Cliquez pour voir en grand" />
            </a>
            <?php else: ?>
            />
            <?php endif; ?>
        <?php else: // video ?>
            <p><video controls="controls" poster="<?= $fileInfo['video_poster'] ?>"  preload="none" src="<?= $fileInfo['url'] ?>"></p>
            <p><a href="<?= $fileInfo['target_url'] ?>">Télécharger la vidéo d'origine</a></p>
        <?php endif; ?>
    </div>
    <?php if($fileInfo['desc'] != ''): ?>
        <p id="desc"><?= $fileInfo['desc'] ?></p>
    <?php endif; ?>

<?php else: // Directory listing requested ?>

    <div id="prevnext">
        <?php $prevNext = getPrevAndNextDir(); ?>
        <a accesskey="-" id="prev" href="<?= $prevNext['prev'] ?>">&laquo; Précédent [-]</a>
        <a accesskey="+" id="next" href="<?= $prevNext['next'] ?>">[+] Suivant &raquo;</a>
    </div>
    <div style="clear:both;"></div>
    <?php $dirDesc = getDirDescription(); ?>
    <?php if($dirDesc != ''): ?>
        <p id="dirdesc"><?= $dirDesc ?></p>
    <?php endif; ?>
    <?php $dirList = getDirList(); ?>
    <?php if(count($dirList)!=0): //If there are sub-directories, list them. ?>
        <div id="directories">
            <h2>Sous-répertoires</h2>
            <ul>
                <?php foreach($dirList as $oneDir): ?>
                <li>
                    <a href="<?= $oneDir['url'] ?>"><?= $oneDir['filetitle'] ?></a>
                    <?php if($oneDir['num_images'] != 0 || $oneDir['num_dir'] != 0): ?>
                    (
                        <?php if($oneDir['num_images'] != 0): ?>
                            <?= $oneDir['num_images'] ?> image<?= ($oneDir['num_images'] == 1) ? '':'s' ?>
                        <?php endif; ?>
                        <?php if($oneDir['num_images'] != 0 && $oneDir['num_dir'] != 0): ?>, <?php endif; ?>
                        <?php if($oneDir['num_dir'] != 0): ?>
                            <?= $oneDir['num_dir'] ?> sous-répertoire<?= ($oneDir['num_dir'] == 1) ? '':'s' ?>
                        <?php endif; ?>
                    )
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div id="gallery">
        <?php $imgList = getImgList(); ?>
        <?php foreach($imgList as $img1): //Output thumbnail links to all images in this directory ?>
            <div class="imgwrapper" style="height:<?= $img1['divheight'] ?>px;
                <?php if($alignimages): ?>width:<?= $img1['divwidth'] ?>px;
                text-align:center<?php endif; ?>
            ">
            <a href="<?= $img1['url'] ?>">
                <img src="<?= $img1['thumbnail_url'] ?>" 
                    <?php if($img1['filetitle'] != ''): ?>alt="<?= $img1['filetitle'] ?>"<?php endif; ?>/>
                <?php if($showimgtitles): ?>
                    <span><?= $img1['filetitle'] ?></span>
                <?php endif; ?>
            </a>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<p id="credit">
Powered by <a href="http://relativelyabsolute.com/spg/">Simple PHP Gallery</a> <?= VERSION ?>
</p>
</body>
</html>
