<?php

namespace PetakUmpet\UI;

use PetakUmpet\Request;

class DataTables {


  public function __construct(Request $request)
  {
    $this->request = $request;
    $this->page = $request->getPage();
    $this->actAdd = null;
    $this->actView = null;
    $this->actEdit = null;
    $this->actDelete = null;
    $this->server_side = true;
    $this->id = uniqid("puDT".str_replace("/", "", str_replace('.', '', $request->getServerName() . $request->getApplication() . $this->page)));
  }

  public function setDataSourceAction($page)
  {
    $this->page = $page;
  }

  public function setColumnNames($columns = array())
  {
    $this->columns = $columns;
  }

  public function setColumnAlias($aliases = array())
  {
    $this->column_aliases = $aliases;
  }

  public function setAddAction($v)
  {
    $this->actAdd = $v;
  }

  public function setViewAction($v)
  {
    $this->actView = $v;
  }

  public function setEditAction($v)
  {
    $this->actEdit = $v;
  }

  public function setDeleteAction($v)
  {
    $this->actDelete = $v;
  }

  public function setServerSide($bool=false)
  {
    $this->server_side = $bool;
  }

  public function __toString()
  {
    $link = $this->request->getAppUrl($this->page);
    $add = $this->actAdd === null ? 
                  $this->request->getAppUrl($this->page) . '?dtact=Add' :
                           $this->request->getAppUrl($this->actAdd);
    $view = $this->actView === null ? 
                  $this->request->getAppUrl($this->page) . '?dtact=View&id=' :
                           $this->request->getAppUrl($this->actView) . '?id='; 
    $edit = $this->actEdit === null ? 
                  $this->request->getAppUrl($this->page) . '?dtact=Edit&id=' :
                           $this->request->getAppUrl($this->actEdit) . '?id='; 
    $delete = $this->actDelete === null ? 
                  $this->request->getAppUrl($this->page) . '?dtact=Delete&id=' :
                           $this->request->getAppUrl($this->actDelete) . '?id='; 

    $ncol = count($this->columns);

    // setup column headers
    $thColNames = '';
    foreach ($this->columns as $c) {
      if ($c == 'id') { $ncol--; continue ; } // special column name, and need to discount it from total column count

      $name = isset($this->column_aliases[$c]) ? $this->column_aliases[$c] : ucwords(str_replace('_', ' ', $c)); 
      $thColNames .= '<th>' . $name . '</th>' ;
    }

    // where to read column data
    $colNames = '';
    foreach ($this->columns as $c) {
      if ($c == 'id') continue; // special column name, oh and 1 discount is enough ;-)
      $colNames .= ',{ "mData": "' . $c . '" }' ;
    }
    $colNames = substr($colNames, 1); // remove beginning ','

    $actionTh = '';
    $actionScript = '';
    if (in_array('id', $this->columns)) {
      $actionTh = '<th>Actions</th>';
      $actionScript =  ', "aoColumnDefs": [ { ' .
                  '"aTargets": [ '.$ncol.' ], ' .
                  '"mData": "id", ' .  
                  '"mRender": function ( data, type, full ) { ' . 
                    'return \' ' . 
                    '<a href="' . $view . 
                      '\'+data+\'"><span class="glyphicon glyphicon-list-alt"></span></a> &nbsp; ' .
                    '<a href="' . $edit . 
                      '\'+data+\'"><span class="glyphicon glyphicon-edit"></span></a> &nbsp; '.
                    '<a href="#" onclick="bootbox.confirm(\\\'Are you sure?\\\', '  . 
                      'function(result) { ' .
                      '  if (result) $.ajax({ url: \\\'' . $delete . '\'+data+\'\\\', ' . 
                      '    success: function() { '. 
                      '     dtRowClick(\'+data+\');' .
                      '  } }); ' . 
                      '});"> ' .
                      '<span class="glyphicon glyphicon-remove"></span></a> '.
                    '\';' .  
                  '}' .
                '} ]' ;
    }

    $serverSide = '"bServerSide" : true, ';
    if (!$this->server_side) {
      $serverSide = '"bServerSide" : false, ';
    }

    $s = '<div id="puDTDiv'. $this->id.'"></div>';

    $s .= '<table id="'.$this->id.'" class="table table-condensed table-bordered table-hover"><thead><tr>';
    $s .= $thColNames . $actionTh;
    $s .= '</tr></thead></table>' ;

    $s .= '<script language="javascript"> ' .
          '$(document).ready(function() {' .
              '$("#' . $this->id . '").dataTable( {' .
                '"bLengthChange": false,'.
                '"bProcessing" : true, ' .
                $serverSide .
                '"sAjaxSource" : "' . $link . '", ' .
                '"aoColumns": [ '  .
                  $colNames .
                ']' . $actionScript .

                ',"sDom": \'<"icon-search"r><"H"lf>Tt<"F"ip>\''.
                ',"oTableTools": {'.
                  '"sSwfPath": "../res/datatables/media/swf/copy_csv_xls_pdf.swf", ' .
                  '"aButtons": ['.
                    '{'.
                      '"sExtends":    "text",'.
                      '"sButtonText": "Add record",'.
                      '"fnClick": function ( nButton, oConfig, oFlash ) {'.
                      'window.location.href = "'.$add.'";'.
                      '}'.
                    '},'.
                    '{'.
                      '"sExtends":    "collection",'.
                      '"sButtonText": "Export",'.
                      '"aButtons":    [ "csv", "pdf" ]'.
                    '},'.
                    // $this->morebuttons. for more flexible button addition

                  ']'.
                '}'.


             '});' .
          '});' . "\n" . 
          'function dtRowClick(rid) { ' . "\n" . 
          '  var tbl = $("#' . $this->id . '").dataTable( {"bRetrieve" : true, "bDestroy" : true } ); ' . "\n" .
          '  tbl.fnDeleteRow(rid); ' . "\n" . 
          '}' . "\n" . 
        '</script>';

    return $s;
  }

}
