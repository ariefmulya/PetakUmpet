<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $ProjectTitle; ?></title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/bootstrap-docs.css" rel="stylesheet">
    <link href="css/bootstrap-datepicker.css" rel="stylesheet">
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
                <i class="icon-user"></i> <?php echo $session->getUser() ?>
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
          <a class="brand" href="#"><?php echo $ProjectTitle ?></a>
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
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
    <script type="text/javascript">$('input[datepicker|=datepicker]').datepicker({format: 'dd-mm-yyyy'});</script>
  </body>
</html>
