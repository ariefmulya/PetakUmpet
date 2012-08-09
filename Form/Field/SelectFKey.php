<?php

namespace PetakUmpet\Form\Field;

class SelectFKey extends Select {

  private $fkData;
  private $nameColumn;

  public function setDbo($db)
  {
    $this->db = $db;
  }

  public function setRelationData($fkData, $nameColumn='nama')
  {
    $this->filter = array();
    $this->fkData = $fkData;
    $this->nameColumn = $nameColumn;
  }

  public function setFilter($filter)
  {
    $this->filter = $filter;
  }

  private function getFilterForQuery()
  {
    $marker = array();

    foreach ($this->filter as $k => $v) {
      $s = " $k = :$k ";
      $marker[] = $s;
    }

    if (count($marker) > 0) {
      return ' WHERE ' . implode(' OR ', $marker);
    }
  }

  public function getOptionsFromRelation()
  {
    $opt = array();
    $db =& $this->db;

    $parent_id = $db->escapeInput($this->fkData['parentcol']);
    $parent_text = $db->escapeInput($this->nameColumn);
    $parent_table = $db->escapeInput($this->fkData['parenttable']);
    $query = sprintf("SELECT * FROM %s", $parent_table);
    $query .= $this->getFilterForQuery();

    if (($res = $db->QueryFetchAll($query, $this->filter))) {
      foreach ($res as $r) {
        if (isset($r[$parent_text])) {
          $opt[$r[$parent_id]] = $r[$parent_text]; 
        } else {
          // rather crude but simple hack
          // to get the parent column names
          // FIXME?
          foreach ($r as $k => $v) {
            if (is_string($v) && $k != $parent_id) {
              $opt[$r[$parent_id]] = $v;
              break;
            }
          }
        }
      }
    }
    parent::setOptions($opt);
  }

}