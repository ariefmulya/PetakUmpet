<?php if (($flash = $session->getFlash()) !== null) : ?>
  <div class="row-fluid">
    <div class="span7">
      <div class="alert">
        <button class="close" data-dismiss="alert">&times;</button>
        <strong><?php echo $flash; ?></strong> 
      </div>
    </div>
  </div>
<?php endif ?>