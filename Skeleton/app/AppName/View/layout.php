<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $config->getProjectTitle(); ?></title>
    <link href="<?php echo $T->getResourceUrl('css/bootstrap.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-responsive.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-docs.css') ?>" rel="stylesheet">
    <link href="<?php echo $T->getResourceUrl('css/bootstrap-datepicker.css') ?>" rel="stylesheet">
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
                  'About' => 'Home/about',
                );
              ?>
              <?php echo $T->navMenu($menu, $request->getPage()) ?>
            </ul>
            <div class="btn-group pull-right">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-user"></i> <?php echo $session->getUser()->getName() ?>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><?php echo $T->link('Profile', 'User/profile') ?></li>
                <li class="divider"></li>
                <li><?php echo $T->link('Logout', 'Logout/index') ?></li>
              </ul>
            </div>
            <?php else: ?>
            <ul class="nav">
              <?php echo $T->navMenu(array('Home' => 'Home/index', 'About' => 'Home/about'), $request->getPage()) ?>
            </ul>
            <?php endif ?>
          </div><!--/.nav-collapse -->
          <a class="brand" href="#"><?php echo $config->getProjectTitle() ?></a>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row-fluid">
        <div class="span10">
          <?php echo $__mainContents; ?>
        </div>
      </div>
    </div>

    <hr/>
    <footer>
      Copyright &copy; 2012
    </footer>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo $T->getResourceUrl('js/jquery.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootbox.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-datepicker.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-transition.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-alert.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-modal.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-dropdown.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-scrollspy.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-tab.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-tooltip.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-popover.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-button.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-collapse.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-carousel.js') ?>"></script>
    <script src="<?php echo $T->getResourceUrl('js/bootstrap-typeahead.js') ?>"></script>
    <script type="text/javascript">$('input[datepicker|=datepicker]').datepicker({format: 'yyyy-mm-dd'});</script>
  </body>
</html>
