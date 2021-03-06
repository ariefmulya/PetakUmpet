<?php

namespace PetakUmpet\Pager;

use PetakUmpet\Request;
use PetakUmpet\Pager;

class QueryPager extends Pager {

  protected $id;

  public function __construct(Request $request, $pagerRows=null)
  {
    parent::__construct($request, $pagerRows);
  }

  public function build($query, $params=array(), $columnNames, $id='id')
  {
    $db = \PetakUmpet\Singleton::acquire('\\PetakUmpet\\Database');

    $this->id = $id;

    $countQuery = "SELECT COUNT(*) AS cnt FROM ( $query ) src ";

    $count = $db->queryFetchOne($countQuery, $params);

    $this->totalRows = $count;
    $this->totalPage = ceil($count/$this->pagerRows);

    if ($this->page > $this->totalPage) $this->page = $this->totalPage;

    $limit = $this->pagerRows;
    $offset = max(($this->page-1) * $limit, 0);

    $query = $db->getDriver()->generateLimit($query, $limit, $offset);

    $this->setHeader($columnNames);
    $this->setPagerData($db->queryFetchAll($query, $params));
  }

  public function headerCallback($headerData)
  {
    if ($this->getReadOnly()) return '';

    return '<th>ACTIONS</th>';
  }

  public function rowCallback($rowData)
  {
    if ($this->getReadOnly()) return '';
    
    $id = $rowData[$this->id];

    $editHref = $this->editAction . '&id=' . $id;
    $deleteHref = $this->deleteAction . '&id=' . $id;
    $pagerHref = $this->pagerAction;

    $link = '<td><div class="btn-group btn-group-xs">' ;

    if ($this->getInlineForm()) {
      $link .=  "<a class=\"btn btn-primary\" href=\"#\" onclick=\"$('#crud-form').load('$editHref');\" >";
    } else {
      $link .=  "<a class=\"btn btn-primary\" href=\"$editHref\" >";
    }

    $link .=  '<i class="glyphicon glyphicon-pencil"></i></a>&nbsp;'
          . '<a class="btn btn-warning" href="#" ' 
          . "onclick=\"bootbox.confirm('Are you sure?', function(result) "
          . "   { if (result) $.ajax({url: '$deleteHref', success: function() " 
          . "       { $('#pager').load('$pagerHref'); } });  });\"> "
          . '<i class="glyphicon glyphicon-remove"></i></a>&nbsp;'
          . '</div></td>';

    return $link;
  }

}