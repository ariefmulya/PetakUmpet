<?php if (($flash = $session->getFlash()) !== null) : ?>
  <div class="alert">
    <button class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $flash; ?></strong> 
  </div>
<?php endif ?>
