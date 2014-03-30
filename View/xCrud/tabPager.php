<div id="<?php echo $targetId ?>Div">
  <?php echo $pager; ?>
<hr>
<a class="btn btn-default" id="<?php echo $targetId ?>AddModal" data-toggle="modala" data-target="#<?php echo $targetId ?>myModal" href="<?php echo $href ?>">Add Data</a>
<div><div class="modal fade" id="<?php echo $targetId ?>myModal"></div></div>

<script type="text/javascript">
$(document).ready( function () {
  $('[data-toggle="modala"]').click(function(e) {
    e.preventDefault();
    var parentId = $('#mainForm form input[name=id]').val();
    if (parentId == undefined || parentId == '') {
      alert("Please fill the main form first.");
      return false;
    }
    var loadurl = $(this).attr('href')
    var targ = $(this).attr('data-target')
    $.get(loadurl, function(data) {
        $(targ).html(data)
    });
    $('#<?php echo $targetId ?>myModal').modal('toggle')
  });
  $('#<?php echo $targetId ?>myModal').on('hidden', function () {
    $('#<?php echo $targetId ?>Tab').click();
  });
});
</script>
</div>
