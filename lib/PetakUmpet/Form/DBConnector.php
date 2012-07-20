<?php

namespace PetakUmpet\Form;

use PetakUmpet\Request;
use PetakUmpet\Form;
use PetakUmpet\Validator;
use PetakUmpet\Database\Builder;

class DBConnector extends Form {

  private $tableName;
  private $builder;


  private function columnTypeMap($coltype)
  {
    $a = array(
        'text' => 'textarea',
        'hidden' => 'hidden',
        'date' => 'date',
        'timestamp' => 'date',
        'datetime' => 'date',
      );
    if (isset($a[$coltype])) return $a[$coltype];

    return 'text';
  }

  public function __construct($tableName, $columns=array(), $skip=array())
  {
    parent::__construct($tableName);

    $this->builder = new Builder($tableName);

    $this->tableName = $tableName;

    $count = 0;

    $vld = new Validator;

    foreach ($this->builder->getSchema() as $s) {
      $name = $s[Builder::SC_COLNAME];
      $type = $this->columnTypeMap($s[Builder::SC_COLTYPE]);

      // check if we need to skip columns
      if (count($columns) > 0 && !in_array($name, $columns) || 
          (count($skip) > 0 && in_array($name, $skip))) {
        continue;
      }

      if ($s[Builder::SC_PRIMARY]) {
        $this->add('hidden', $name);
        continue;
      }

      if ($s[Builder::SC_NOTNULL]) {
        // TODO: add ability to configure fields, need to save the fields in a list variables first
        $vld->set($name, new Validator\Required);
      }
      $this->add($type, $name);
      $count++;
    }

    if ($count > 0) {
      $this->add(new Form\Submit);
      $this->setValidator($vld);
    }

  }

  public function bindValidateSave(Request $request)
  {
    $status = parent::bindValidate($request);

    if (!$status) return false;
   
    $this->builder->import($request->getData());   

    return $this->builder->save();
  }

}