<?php if (($flash = $session->getFlash()) !== null) : ?>
  <div class="alert alert-warning alert-dismissable">
    <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong><?php echo $flash; ?></strong> 
  </div>
<?php endif ?>
