<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php echo $T->getResourceUrl('ico/favicon.ico') ?>">    
    <title><?php echo $config->getProjectTitle(); ?></title>
    <link href="<?php echo $T->getResourceUrl('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-theme.min.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/custom.css') ?>" rel="stylesheet">
    <script src="<?php echo $T->getResourceUrl('js/jquery-1.11.0.min.js') ?>"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header navbar-right">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?php echo $config->getProjectTitle() ?></a>
        </div>
        <div class="navbar-collapse collapse">
          <?php if ($session->getUser()) : ?>
          <ul class="nav navbar-nav">
            <?php $menu = array(
                'Home' => 'Home/index',
                'Admin' => 'Admin/index',
              );
              echo $UI->navMenu($menu);
            ?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="glyphicon glyphicon-user"></i> <?php if ($session->getUser()) echo $session->getUser()->getName() ?>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><?php echo $UI->link('Profile', 'User/profile') ?></li>
                <li class="divider"></li>
                <li><?php echo $UI->link('Logout', 'Logout/index', '', 'icon-off') ?></li>
              </ul>
            </li>
            <li>|||||</li>
          </ul>
          <?php else: ?>
          <ul class="nav navbar-nav">
            <?php echo $UI->navMenu(array('Home' => 'Home/index', 'About' => 'Home/about')) ?>
          </ul>
          <?php endif ?> 
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <?php echo $UI->subNavMenu() ?>
    <?php echo $T->block('content') ; ?>
    
    <div class="container">
      <hr/>
      <footer>
        Copyright &copy; <?php echo date('Y') ; ?>
      </footer>
    </div>
    <script src="<?php echo $T->getResourceUrl('js/jquery.form.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootbox.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-datetimepicker.min.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/jquery.jstree/jstree.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/jquery.select-chain.js') ?>"></script>
  </body>
</html>