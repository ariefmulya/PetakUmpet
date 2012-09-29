<?php

namespace PetakUmpet\Pager;

use PetakUmpet\Request;
use PetakUmpet\Pager;
use PetakUmpet\Database\Accessor;
use PetakUmpet\Database\Schema;

class TablePager extends Pager {

  private $tableName;

  public function __construct(Request $request, $pagerRows=null)
  {
    parent::__construct($request, $pagerRows);
  }

  public function build($tableName, $columns=array())
  {
    $this->tableName = $tableName;

    $dba = new Accessor($tableName);

    $schema = new Schema($tableName);

    if (count($columns) == 0) {
      $columns = $schema->getColumnNames();
    }

    $count = $dba->countPagerData($this->filter, $schema->get());

    $this->totalRows = $count;
    $this->totalPage = ceil($count/$this->pagerRows);

    if ($this->page > $this->totalPage) $this->page = $this->totalPage;

    $this->setHeader($columns);

    $data = $dba->findPagerData($this->page, $this->pagerRows, $this->filter, $schema->get());

    $this->setPagerData($data);
  }

  public function headerCallback($headerData)
  {
    return '<th>ACTIONS</th>';
  }

  public function rowCallback($rowData)
  {
    $id = $rowData['id'];

    $editHref = $this->editAction . '&id=' . $id;
    $deleteHref = $this->deleteAction . '&id=' . $id;
    $pagerHref = $this->pagerAction;

    $link = '<td><div class="btn-group">' ;

    if ($this->getInlineForm()) {
      $link .=  "<a class=\"btn btn-mini btn-primary\" href=\"#\" onclick=\"$('#crud-form').load('$editHref');\" >";
    } else {
      $link .=  "<a class=\"btn btn-mini btn-primary\" href=\"$editHref\" >";
    }

    $link .=  '<i class="icon-pencil icon-white"></i></a>&nbsp;'
          . '<a class="btn btn-mini btn-warning" href="#" ' 
          . "onclick=\"bootbox.confirm('Are you sure?', function(result) "
          . "   { if (result) $.ajax({url: '$deleteHref', success: function() " 
          . "       { $('#pager').load('$pagerHref'); } });  });\"> "
          . '<i class="icon-remove icon-white"></i></a>&nbsp;'
          . '</div></td>';

    return $link;
  }

}