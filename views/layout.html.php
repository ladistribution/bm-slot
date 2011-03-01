<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo $application->getName() ?></title>
  <meta charset="utf-8"/>
<?php if (defined('LD_COMPRESS_CSS') && constant('LD_COMPRESS_CSS')) : ?>
  <link href="<?php echo Ld_Ui::getCssUrl('/h6e-minimal/h6e-minimal.compressed.css', 'h6e-minimal') ?>" rel="stylesheet" type="text/css"/>
  <link href="<?php echo Ld_Ui::getCssUrl('/ld-ui/ld-ui.compressed.css', 'ld-ui') ?>" rel="stylesheet" type="text/css"/>
<?php else : ?>
  <link href="<?php echo Ld_Ui::getCssUrl('/h6e-minimal/h6e-minimal.css', 'h6e-minimal') ?>" rel="stylesheet" type="text/css"/>
  <link href="<?php echo Ld_Ui::getCssUrl('/ld-ui/ld-ui.css', 'ld-ui') ?>" rel="stylesheet" type="text/css"/>
<?php endif ?>
<?php if (defined('LD_APPEARANCE') && constant('LD_APPEARANCE')) : ?>
  <link href="<?php echo Ld_Ui::getApplicationStyleUrl() ?>" rel="stylesheet" type="text/css"/>
<?php endif ?>
  <style type="text/css">
  .h6e-block { padding:1em; }
  .ld-feed .hentry .entry-inner { width:500px; }
  .h6e-page-content { width:575px; float:left; margin-bottom:20px; }
  .h6e-sidebar { width:300px; float:left; margin-left:25px; }
  .h6e-page-content h2 { font-size:20px; }
  .ld-feed .hentry a.h6e-tag { color:#666; }
  </style>
  <script type="text/javascript" src="<?php echo Ld_Ui::getJsUrl('/jquery/jquery.js', 'js-jquery') ?>"></script>
</head>
<body class="ld-layout h6e-layout">

    <?php Ld_Ui::topBar(); ?>

    <div class="ld-main-content h6e-main-content">

        <h1 class="h6e-page-title"><?php echo $application->getName() ?></h1>

        <?php Ld_Ui::topNav(); ?>

        <?php if ($hasMenu) : ?>
        <ul class="h6e-tabs">
            <li <?php if (isset($isFriends)) echo 'class="active"' ?>><a href="<?php echo url_for('friends/marks') ?>">Friends Marks</a></li>
            <li <?php if (isset($isMarks)) echo 'class="active"' ?>><a href="<?php echo url_for('marks') ?>"><?php echo $configuration['bmUsername'] ?> Marks</a></li>
        </ul>
        <?php endif ?>

        <?php echo $content ?>

        <div class="h6e-simple-footer">
            Powered by <strong><?php echo $application->getPackage()->getName() ?></strong>
            with the help of <a href="http://www.limonade-php.net/">Limonade</a>
            via <a href="http://ladistribution.net/">La Distribution</a>
        </div>

    </div>

    <?php Ld_Ui::superBar(); ?>

</body>
</html>