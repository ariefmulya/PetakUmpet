<div class="container">
  <div class="col-md-5">
    <h3>Find <?php echo ucwords(str_replace('_', ' ', $crudTitle)) ?></h3>
    <?php echo $filterForm; ?>
    <?php if ($readOnly === false) : ?>
      <?php if ($inlineForm) : ?>
        <a href="#" class="btn" 
              onclick="$('#crud-form').load('<?php echo $editAction; ?>');">
      <?php else : ?>
        <a class="btn" href="<?php echo $editAction; ?>"> 
      <?php endif ?>
        Add Data</a>
    <?php endif ?>
    <hr/>
    <div id="pager" class="row-fluid">
      <?php echo $pager; ?>
    </div>
  </div>
  <?php if ($inlineForm) : ?>
    <div class="col-md-7">
      <div id="crud-form">
      </div>
    </div>
  <?php endif ?>
</div>

<?php if($hasScript) : ?>
<script language="javascript">
<?php echo $hasScript ?>
</script>
<?php endif ?>