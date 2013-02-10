<div class="span6" id="mainForm">
    <h3>Input Data <?php echo ucwords(str_replace('_', ' ', $tableName)) ?></h3>
  <?php $T->snippet('flash-message'); ?>
  <?php echo $form; ?>
</div>

<?php if (isset($tabs) && count($tabs) > 0) : ?>
<div class="span5">
  <ul class="nav nav-tabs" id="userTab">
  <?php $cnt=0; foreach ($tabs as $idx => $t) : $cnt++; ?>
    <li><a data-toggle="taba" id="<?php echo $idx.'Tab'; ?>" data-target="#<?php echo $idx ?>" href="<?php echo $tabHref . '&relkey=' . $t['relKey'] . '&relval=' . $id . '&tabid=' . $idx; ?>"><?php echo $t['name'] ?></a></li>
  <?php endforeach ?>
  </ul>
  <div id="tabContent" class="tab-content">
  <?php foreach ($tabs as $idx => $t) : ?>
    <div class="tab-pane" id="<?php echo $idx ?>"></div>
  <?php endforeach ?>
  </div>  
</div>

<script>
  $('[data-toggle="taba"]').click(function(e) {
    e.preventDefault();
    var loadurl = $(this).attr('href');
    var targ = $(this).attr('data-target')
    $.get(loadurl, function(data) {
        $(targ).html(data)

    });
    $(this).tab('show');
});
</script>
<?php endif ?>

<?php if ($inlineForm) : ?>
<script type="text/javascript">
    // wait for the DOM to be loaded 
  $(document).ready(function() { 
      // bind form' and provide a simple callback function 
      $('#<?php echo $form->getName(); ?>').ajaxForm({
        target: '#crud-form',
        success: function() {
          $('#pager').load('<?php echo $pagerAction; ?>');
        }
      }); 
  }); 
</script> 
<?php endif ?>