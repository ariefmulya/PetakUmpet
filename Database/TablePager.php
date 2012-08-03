<?php

namespace PetakUmpet\Database;

use PetakUmpet\Request;
use PetakUmpet\Pager;
use PetakUmpet\Database\Builder;

class TablePager extends Pager {

  private $tableName;
  private $builder;
  private $filter;
  private $extraFilter;

  private $editAction;
  private $deleteAction;

  public function __construct(Request $request, $pagerRows=8)
  {
    parent::__construct($request, $pagerRows);

    $this->filter = null;
    $this->extraFilter = null;
  }

  public function build($tableName, $displayCols=array())
  {
    $this->tableName = $tableName;

    $this->builder = new Builder($tableName);

    $buildFilter = array();

    if ($this->filter !== null && $this->filter != '') {
      foreach ($this->builder->getColumnNames() as $c) {
        if ($c == 'id') continue;
        $buildFilter[$c] = $this->filter;
      }
    }

    if (is_array($this->extraFilter)) {
      foreach ($this->extraFilter as $k => $v) $buildFilter[$k] = $v;
    }

    $count = $this->builder->getCountPagerData($buildFilter);

    $this->totalRows = $count;
    $this->totalPage = ceil($count/$this->pagerRows);
    if ($this->page > $this->totalPage) $this->page = $this->totalPage;

    $this->setHeader(count($displayCols) > 0 ? $displayCols : $this->builder->getColumnNames());

    $this->builder->importPagerData($this->page, $this->pagerRows, $displayCols, $buildFilter);

    $this->setPagerData($this->builder->getTableData());

  }

  public function setFilter($value=null, $columns = array())
  {
    if ($value===null) return;

    $this->filter = $value;
    $this->url = preg_replace('/&filter=.+[&]*/', '', $this->url);
    $this->url .= '&filter=' . $value;
  }

  public function setExtraFilter($filter)
  {
    $this->extraFilter = $filter;
  }

  public function setEditAction($target)
  {
    $this->editAction = $target;
  }

  public function setDeleteAction($target)
  {
    $this->deleteAction = $target;
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

    $link  = '<td><div class="btn-group">' 
            . "<a class=\"btn btn-mini btn-primary\" href=\"#\" onclick=\"$('#crud-form').load('$editHref');\" >"
            . '<i class="icon-pencil icon-white"></i></a>&nbsp;'
            . '<a class="btn btn-mini btn-warning" href="#" ' 
            . "onclick=\"bootbox.confirm('Are you sure?', function(result) "
            . "   { if (result) return location.href = '$deleteHref'});\"> "
            . '<i class="icon-remove icon-white"></i></a>&nbsp;'
            . '</div></td>';

    return $link;
  }

}