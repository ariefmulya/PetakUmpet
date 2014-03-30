<?php

namespace PetakUmpet\Pager;

use PetakUmpet\Request;
use PetakUmpet\Pager;
use PetakUmpet\Database\Accessor;
use PetakUmpet\Database\Schema;

class TablePager extends Pager {

  private $tableName;
  private $orderBy;
  private $orderAsc;

  public function __construct(Request $request, $pagerRows=null)
  {
    parent::__construct($request, $pagerRows);
    $this->orderBy = 'id';
    $this->orderAsc = true;
  }

  public function setOrderBy($value, $asc=true)
  {
    $this->orderBy = $value;
    $this->orderAsc = $asc;
  }

  public function build($tableName, $columns=array())
  {
    $this->tableName = $tableName;

    $schema = new Schema($tableName);
    $dba = new Accessor($tableName, null, $schema);

    if (count($columns) == 0) {
      $columns = $schema->getColumnNames();
    }

    $count = $dba->countPagerData($this->filter);

    $this->totalRows = $count;
    $this->totalPage = ceil($count/$this->pagerRows);

    if ($this->page > $this->totalPage) $this->page = $this->totalPage;

    $this->setHeader($columns);

    $data = $dba->findPagerData($this->page, $this->pagerRows, $this->filter, $this->orderBy, $this->orderAsc);

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

    $link = '<td><div class="btn-group btn-group-xs">' ;

    if ($this->getInlineForm()) {
      $link .=  "<a class=\"btn btn-primary\" href=\"#\" onclick=\"$('#crud-form').load('$editHref');\" >";
    } else {
      $link .=  "<a class=\"btn btn-primary\" href=\"$editHref\" >";
    }

    $link .=  '<span class="glyphicon glyphicon-edit"></span></a>&nbsp;'
          . '<a class="btn btn-warning" href="#" ' 
          . "onclick=\"bootbox.confirm('Are you sure?', function(result) "
          . "   { if (result) $.ajax({url: '$deleteHref', success: function() " 
          . "       { $('#pager').load('$pagerHref'); } });  });\"> "
          . '<span class="glyphicon glyphicon-remove"></span></a>&nbsp;'
          . '</div></td>';

    return $link;
  }

}