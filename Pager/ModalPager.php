<?php

namespace PetakUmpet\Pager;

class ModalPager extends QueryPager {

  public function rowCallback($rowData)
  {
    $id = $rowData[$this->id];

    $editHref = $this->editAction . '&id=' . $id;
    $deleteHref = $this->deleteAction . '&id=' . $id;
    $pagerHref = $this->pagerAction;

    $link = '<td><div class="btn-group">' ;

    $link .=  "<a class=\"btn btn-mini btn-primary\" data-toggle=\"modala\" data-target=\"#".$this->targetId."myModal\"  href=\"$editHref\" >";

    $link .=  '<i class="icon-pencil icon-white"></i></a>&nbsp;'
          . '<a class="btn btn-mini btn-warning" href="#" ' 
          . "onclick=\"bootbox.confirm('Are you sure?', function(result) "
          . "   { if (result) $.ajax({url: '$deleteHref', success: function() " 
          . "       { $('#pager').load('$pagerHref'); "
          ;
    if (isset($this->targetId)) {     
      $link .= "  $('#" . $this->targetId ."Tab').click(); ";
    }
    $link .= " } });  });\"> "
          . '<i class="icon-remove icon-white"></i></a>&nbsp;'
          . '</div></td>';

    return $link;
  }

}