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

  public function save($data, $pkeys=array())
  {
    $usedPKeys = (count($pkeys) > 0 ? $pkeys : $this->schema->getPK());

    $id = $this->dba->save($data, $usedPKeys);

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
