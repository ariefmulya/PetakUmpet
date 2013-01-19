<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;
use PetakUmpet\Database\Accessor;
use PetakUmpet\Database\Schema;

class Model {

  private $db;
  private $dba;
  private $schema;

  private $tableName;

  public function __construct($tableName, $db=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->db = $db;
    $this->tableName = $db->escapeInput($tableName);

    $this->schema = new Schema($this->tableName);
    $this->dba = new Accessor($this->tableName, $db, $this->schema);
  }

  private function adaptForDB($data)
  {
    foreach ($data as $k => $v) {
      if (is_array($v)) {
        if ($this->schema->isArrayColumn($k)) {
          $arrayVal = $this->db->getDriver()->convertArray($v); 
          $data[$k] = $arrayVal;
        } else {
          $v = $v[0];
          $data[$k] = $v;
        }
      }
      if ($this->schema->getColumnPdoType($k) == \PDO::PARAM_BOOL) {
        if ($v == '' || $v === null) {
          $v = 0;
          $data[$k] = $v;
        }
      }
    }

    return $data;
  }

  public function save($data, $pkeys=array(), $columns=null)
  {
    $usedPKeys = (count($pkeys) > 0 ? $pkeys : $this->schema->getPK());

    $data = $this->adaptForDB($data);

    $id = $this->dba->save($data, $usedPKeys, $columns);

    if ($id) {
      return $id;
    }
    return false; 
  }

  public function delete($params=array())
  {
    return $this->dba->delete($params);
  }

  public function get($id)
  {
    return $this->dba->findOneBy(array('id' => $id));
  }

  public function getBy($params, $compareType=array())
  {
    return $this->dba->findOneBy($params, $compareType);
  }

}
