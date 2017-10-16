<?php
require('sp_helper_functions.php');
require('sp_def_vars.php');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?= getPageTitle(); ?></title>

    <!-- Bootstrap -->
    <link href="<?= getCurrentWorkingDirectory();?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= getCurrentWorkingDirectory();?>/css/sp_styles.css" rel="stylesheet">
		
		
    <!-- Icon -->
    <?php if(isset($icon)): ?>
    <link rel="shortcut icon" href="<?= getCurrentWorkingDirectory();?>/css/<?= $icon ?>" type="image/<?= $icontype ?>">
    <link rel="icon" href="<?= getCurrentWorkingDirectory();?>/css/<?= $icon ?>" type="image/<?= $icontype ?>">
    <?php endif; ?>
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

<?php if($display_file != ''): //Image requested ?>
    <?php $prevNext = getPrevAndNext(); ?>
<?php else: // Directory listing requested ?>
    <?php $prevNext = getPrevAndNextDir(); ?>
<?php endif; ?>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Voir navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?= getPageTitle(); ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a accesskey="-" href="<?= $prevNext['prev'] ?>">&laquo; Précédente</a></li>
            <li><a accesskey="+" href="<?= $prevNext['next'] ?>">Suivante &raquo;</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

        <div class="row breadcrumbs">
            <div class="col-lg-12">
            <strong>Vous voyez :</strong>
                <?php foreach( getBreadCrumbs() as $link): ?>
                    <?php if( ! $link['first']): ?>&raquo;<?php endif; ?>
                    <a href="<?= $link['url'] ?>" <?= $link['accesskey'] ?>><?= $link['title'] ?></a>
                <?php endforeach; ?>
            </div>
        </div>
            
<?php if($display_file != ''): //Image requested ?>

    <?php $fileInfo = getFile(); ?>
        <center>
        <div class="row">
            <div class="col-lg-12">
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
        </div>
        </center>
    <?php if($fileInfo['desc'] != ''): ?>
        <p id="desc"><?= $fileInfo['desc'] ?></p>
    <?php endif; ?>

<?php else: // Directory listing requested ?>

    <?php $dirDesc = getDirDescription(); ?>
    <?php if($dirDesc != ''): ?>
        <div class="row">
            <div class="col-lg-12">
                <?= $dirDesc ?>
            </div>
        </div>
    <?php endif; ?>
    <?php $dirList = getDirList(); ?>
    <?php if(count($dirList)!=0): //If there are sub-directories, list them. ?>
        <div class="row">
            <div class="col-lg-12">
            <h4>Sous-répertoires</h4>
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
        </div>
    <?php endif; ?>
    <center>
    <div class="row">
        <?php $imgList = getImgList(); ?>
        <?php foreach($imgList as $img1): //Output thumbnail links to all images in this directory ?>
            <div  class="col-lg-3 col-md-4 col-xs-6 thumb" style="height:<?= $img1['divheight'] ?>px;
                <?php if($alignimages): ?>width:<?= $img1['divwidth'] ?>px;
                text-align:center<?php endif; ?>
            ">
            <a href="<?= $img1['url'] ?>">
                <img class="img-thumbnail" src="<?= $img1['thumbnail_url'] ?>" 
                    <?php if($img1['filetitle'] != ''): ?>alt="<?= $img1['filetitle'] ?>"<?php endif; ?>/>
                <?php if($showimgtitles): ?>
                    <div><?= $img1['filetitle'] ?></div>
                <?php endif; ?>
            </a>
            </div>
        <?php endforeach; ?>
    </div>
    </center>

<?php endif; ?>

            </div>
        </div>
        <hr>

    </div>
    <!-- /.container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?= getCurrentWorkingDirectory();?>/js/bootstrap.min.js"></script>
  </body>
</html>
