<?php

namespace PetakUmpet\Database;
use PetakUmpet\Database\Builder;

class Builder {

  private $db;
  private $dbm;

  private $tableName;
  private $tableData;

  private $mc;    // schema model cache
  private $pkeys; // primary keys
  private $fkeys; // foreign keys

  function __construct($db=null, $tableName)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->tableName = $db->escapeInput($tableName);

    $this->db  = $db;
    $this->dba = new \PetakUmpet\Database\Accessor($db, $tableName);

    $this->mc =$this->buildTableSchema();
  }

  private function buildTableSchema()
  {
    $db =& $this->db;

    $this->mc = $db->QueryFetchAll($db->getBaseDbo()->getTableSchemaQuery(), array($this->tableName));

    if (!$this->mc) throw new \Exception('Building database schema failed');

    foreach ($this->mc as $s) {
      if ($s['primary']) {
        $this->pkeys[] = $s['column'];
      }
    }

    $this->fkeys = $db->QueryFetchAll($db->getBaseDbo()->getForeignKeyQuery(), array($this->tableName));
  }

  public function importData($data)
  {
    if (!isset($data[0])) {
      $data = array ( 0 => $data );
    }
    $this->tableData = $data;
  }

  public function save()
  {
    foreach ($this->tableData as $d) {
      $insertMode = false;

      $pkvals = array();

      foreach ($this->pkeys as $pk) {
        if (!isset($d[$pk]) || empty($d[$pk]) || $d[$pk] === null) {
          $insertMode=true;
          break;
        }
        $pkvals[$pk] = $d[$pk];
      }

      if ($insertMode) $this->dba->insert($d);
      else $this->dba->update($d, $pkvals);

      unset($pkvals);
    }
  }
}
