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
    $normalizedData = array();
    foreach ($this->schema->getColumnNames() as $c) {
      if (isset($data[$c])) {
        $k = $c; $v = $data[$c];
        $newv = $v;
        if (is_array($v)) {
          if ($this->schema->isArrayColumn($k)) {
            $arrayVal = $this->db->getDriver()->convertArray($v); 
            $newv = $arrayVal;
          } else {
            $newv = $v[0];
          }
        }
        if ($this->schema->getColumnPdoType($k) == \PDO::PARAM_BOOL) {
          if ($v == '' || $v === null) {
            $newv = 0;
          }
        }
        $normalizedData[$k] = $newv;
      }
    }

    return $normalizedData;
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
