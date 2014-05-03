<?php

namespace PetakUmpet\Pager;

use PetakUmpet\Request;

class DataTablePager {

  protected $id;

  /* adapter for DataTable ajax request */
  private function prepare(Request $request)
  {
    $limit = -1; 
    $offset = 10; 
    $order = array();
    $filterOr = array();
    $filterAnd = array();
    $aColumns = $this->columns;
    $ncol = count($aColumns);

    if ($request->get('iDisplayStart') !== null && $request->get('iDisplayLength') != '-1') {
      $offset  = intval($request->get('iDisplayStart'));
      $limit = intval($request->get('iDisplayLength'));
    }
    
    if ($request->get('iSortCol_0')!==null) {
      for ($i=0; $i < intval($request->get('iSortingCols')); $i++) {
        if ($request->get('bSortable_'.intval($request->get('iSortCol_'.$i))) == "true") {
          $col = $aColumns[ intval($request->get('iSortCol_'.$i)) ]; 
          $dir = strtolower($request->get('sSortDir_'.$i))==='asc' ? 'ASC' : 'DESC' ;
          $order[$col] = $dir;
        }
      }
    }

    /* multi column filtering, for uniform search */
    if ($request->get('sSearch') && $request->get('sSearch') != "") {
      for ( $i=0 ; $i<$ncol; $i++ ) {
        $col = $aColumns[$i];
        $val = '%'.$request->get('sSearch').'%';
        $filterOr[$col] = $val;
      }
    }
    
    /* Individual column filtering, useful for per-column search */
    for ($i=0; $i<$ncol; $i++) {
      if ($request->get('bSearchable_'.$i) && 
            $request->get('bSearchable_'.$i) == "true" && 
              $request->get('sSearch_'.$i) != '' ) {
        $col = $aColumns[$i];
        $val = '%'.$request->get('sSearch_'.$i).'%';
        $filterAnd [$col] = $val;
      }
    }  

    $this->limit = $limit; $this->offset=$offset;
    $this->sEcho = intval($request->get('sEcho'));
    $this->order = $order; $this->filterOr = $filterOr;
    $this->filterAnd = $filterAnd;
  }

  private function filterToString($filter, $type="OR") 
  {
    $where = '';
    $params = array();

    if ($type != 'OR' && $type != 'AND') { // possible breach happened 
      throw new \Exception('Possible SQL problem: DataTablePager filter type is not correct');
    }

    if (count($filter) > 0) {
      $marker = array();
      foreach ($filter as $k => $v) {
        if ($k == 'id') { continue; }
        if (in_array($k, $this->columns)) {
          $k = $this->db->escapeInput($k);
          $s =  $k . ' ILIKE :' . $k;
          $marker[] = $s;
          $params[$k] = $v;
        }
      }
      if (count($marker) > 0) {
        $where = ' (' . implode(' '. $type . ' ', $marker) . ') ' ;
      } 
    }
    return array($where, $params);
  }

  private function buildOrder()
  {
    $s = '';
    if (count($this->order) > 0) {
      foreach($this->order as $col => $dir) {
        $s .= ', ' . $this->db->escapeInput($col) . ' ' . $this->db->escapeInput($dir);
      }
      $s = substr($s, 1); // remove initial comma
      $s = ' ORDER BY ' . $s;
    } 
    return $s;
  }

  private function buildQuery() 
  {
    $q = "SELECT * FROM " . $this->db->escapeInput($this->source) . " ";

    $where = '';
    $params = array();

    list($criteria, $cond) = $this->filterToString($this->filterOr);
    if (count($cond) > 0) {
      $where = ' WHERE ' . $criteria;
      $params = $cond;
    }

    list($criteria, $cond) = $this->filterToString($this->filterAnd, 'AND');
    if (count($cond) > 0) {
      if ($where == '') { 
        $where = ' WHERE ' . $criteria;
        $params = $cond;
      } else {
        $where .= ' AND '  . $criteria;
        $params = array_merge($params, $cond);
      }
    } 

    $q .= $where;
    $countQuery = $q;

    $q .= $this->buildOrder();

    if ($this->limit != -1) {
      $q = $this->db->getDriver()->generateLimit($q, $this->limit, $this->offset);
    }

    return array($q, $countQuery, $params);
  }

  private function execute()
  {
    $db = \PetakUmpet\Singleton::acquire('\\PetakUmpet\\Database');
    $this->db = $db;

    // count available data
    if (strstr($this->source, "SELECT")) {
      $query = "SELECT COUNT(*) AS cnt FROM (SELECT * FROM " . $db->escapeInput($this->source) . ") src" ;
    } else {
      $query = "SELECT COUNT(*) AS cnt FROM " . $db->escapeInput($this->source) . " src" ;
    }
    $count = $db->queryFetchOne($query);

    // get displayed data
    $query = '';
    $params = array();
    list($query, $countQuery, $params) = $this->buildQuery();

    $filterCountQuery = "SELECT COUNT(*) AS cnt FROM ( " .$countQuery. " ) src ";
    $filterCount = $db->queryFetchOne($filterCountQuery, $params);
    $aaData = $db->queryFetchAll($query, $params);

    $output = array(
        'sEcho' => $this->sEcho,
        'iTotalRecords' => $count,
        'iTotalDisplayRecords' => $filterCount,
        'aaData' => $aaData,
      );


    return $output;
  }

  public function __construct($source, $columns, Request $request)
  {
    $this->source = $source; // can be table name or query
    $this->columns = $columns;
    $this->prepare($request);
  }

  public function __toString()
  {
    echo json_encode($this->execute());
    exit;
  }

}
