<div class="container">
  <div class="page-header">
    <h3>Please login to access system</h3>
  </div>
  <?php $T->snippet('flash-message'); ?>
  <?php echo $form; ?>
</div>
<script type="text/javascript">
$(document).ready(function () {
  if (window.top.location != '<?php $url = $request->getAppUrl("Login/index"); echo $url; ?>') {
    window.top.location = '<?php echo $url ?>' ;
  }
});
</script>

