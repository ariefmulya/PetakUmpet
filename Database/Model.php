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

    $this->dba = new Accessor($this->tableName);
    $this->schema = new Schema($this->tableName);
  }

  public function save($data)
  {
    $id = $this->dba->save($data, $this->schema->getPK(), $this->schema->getColumnNames());

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

  public function getBy($params)
  {
    return $this->dba->findOneBy($params);
  }

}
