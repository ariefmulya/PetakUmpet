<div class="span6" id="mainForm">
    <h3>Input Data <?php echo ucwords(str_replace('_', ' ', $tableName)) ?></h3>
  <?php $T->snippet('flash-message'); ?>
  <?php echo $form; ?>
</div>

<?php if (isset($relations) && count($relations) > 0) : ?>
<div class="span5">
  <ul class="nav nav-tabs" id="userTab">
  <?php $cnt=0; foreach ($relations as $r) : $cnt++; ?>
    <li><a data-toggle="taba" id="<?php echo $r['targetId'].'Tab'; ?>" data-target="#<?php echo $r['targetId'] ?>" href="<?php echo $r['href'] . $id ?>"><?php echo $r['name'] ?></a></li>
  <?php endforeach ?>
  </ul>
  <div id="tabContent" class="tab-content">
  <?php foreach ($relations as $r) : ?>
    <div class="tab-pane" id="<?php echo $r['targetId'] ?>"></div>
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
      $('#<?php echo $tableName; ?>').ajaxForm({
        target: '#crud-form',
        success: function() {
          $('#pager').load('<?php echo $pagerAction; ?>');
        }
      }); 
  }); 
</script> 
<?php endif ?>