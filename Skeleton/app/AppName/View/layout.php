<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo $config->getProjectTitle(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="<?php echo $T->getResourceUrl('css/bootstrap.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-responsive.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-docs.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/custom.css') ?>" rel="stylesheet">
    <script src="<?php echo $T->getResourceUrl('js/jquery.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/jquery.form.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootbox.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/jquery.cookie.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-datetimepicker.min.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/jquery.jstree/jquery.jstree.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/jquery.select-chain.js') ?>"></script>
  </head>
  <body data-offset="50" data-target=".subnav">

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar" type="button">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="nav-collapse collapse">
            <?php if ($session->getUser()) : ?>
            <ul class="nav">
              <?php $menu = array(
                  'Home' => 'Home/index',
                  'Admin' => 'Admin/index',
                );
                echo $T->navMenu($menu);
              ?>
            </ul>

            <div class="btn-group pull-right">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-user"></i> <?php if ($session->getUser()) echo $session->getUser()->getName() ?>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><?php echo $T->link('Profile', 'User/profile') ?></li>
                <li class="divider"></li>
                <li><?php echo $T->link('Logout', 'Logout/index', '', 'icon-off') ?></li>
              </ul>
            </div>
            <?php else: ?>
            <ul class="nav">
              <?php echo $T->navMenu(array('Home' => 'Home/index', 'About' => 'Home/about')) ?>
            </ul>
            <?php endif ?>
          </div><!--/.nav-collapse -->
          <a class="brand" href="#"><?php echo $config->getProjectTitle() ?></a>
          
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="span12">
          <?php echo $T->subNavMenu() ?>
          <?php echo $__mainContents; ?>
        </div>
      </div>
    </div>
    
    <footer>
      <div class="container">
        <hr/>
        Copyright &copy; 2012
      </div>
    </footer>
  </body>
</html>

