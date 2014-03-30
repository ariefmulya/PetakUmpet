<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Form</h3>
    </div>
    <div class="modal-body" id="relationForm">
      <?php echo $T->snippet('flash-message') ?>
      <?php echo $form ?>
    <script> 
        // wait for the DOM to be loaded 
        $(document).ready(function() { 
            // bind form' and provide a simple callback function 
            $('#<?php echo $form->getFormName(); ?>').ajaxForm({
              target: '#<?php echo $targetId ?>myModal',
            });
        }); 
    </script> 
    </div>
  </div>
</div>