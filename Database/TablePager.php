<?php

namespace PetakUmpet\Database;

use PetakUmpet\Request;
use PetakUmpet\Pager;
use PetakUmpet\Database\Builder;

class TablePager extends Pager {

  private $tableName;
  private $builder;
  private $filter;
  private $editAction;

  public function __construct(Request $request, $pagerRows=8)
  {
    parent::__construct($request, $pagerRows);

    $this->filter=null;
  }

  public function build($tableName)
  {
    $this->tableName = $tableName;

    $this->builder = new Builder($tableName);

    $count = $this->builder->getCountPagerData($this->filter);

    $this->totalRows = $count;
    $this->totalPage = ceil($count/$this->pagerRows);
    if ($this->page > $this->totalPage) $this->page = $this->totalPage;

    $this->setHeader($this->builder->getColumnNames());

    $this->builder->importPagerData($this->page, $this->pagerRows, $this->filter);

    $this->setPagerData($this->builder->getTableData());

  }

  public function setFilter($value=null)
  {
    if ($value===null) return;

    $this->filter = $value;
    $this->url = preg_replace('/&filter=.+[&]*/', '', $this->url);
    $this->url .= '&filter=' . $value;
  }

  public function setEditAction($target)
  {
    $this->editAction = $target;
  }

  public function headerCallback($headerData)
  {
    return '<th>ACTIONS</th>';
  }

  public function rowCallback($rowData)
  {
    $id = $rowData['id'];

    $href = $this->editAction . '&id=' . $id;

    $link  = '<td><a class="label label-info" href="'.$href.'">Edit</a>&nbsp;';
    $link .= '<a class="label label-info" href="'.$href.'">Delete</a></td>';

    return $link;
  }

}