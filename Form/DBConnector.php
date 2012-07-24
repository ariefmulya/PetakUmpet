<?php

namespace PetakUmpet\Form;

use PetakUmpet\Request;
use PetakUmpet\Form;
use PetakUmpet\Validator;
use PetakUmpet\Database\Builder;

class DBConnector {

  private $tableName;
  private $builder;
  private $fields;
  private $validator;
  private $form;

  private function columnTypeMap($coltype)
  {
    $a = array(
        'text' => 'textarea',
        'hidden' => 'hidden',
        'date' => 'date',
        'timestamp' => 'date',
        'datetime' => 'date',
        'boolean' => 'radioGroup',
        'bool' => 'radioGroup',
      );
    if (isset($a[$coltype])) return $a[$coltype];

    return 'text';
  }

  public function __construct($tableName, $columns=array(), $skip=array())
  {
    $this->tableName = $tableName;

    $this->builder = new Builder($tableName);

    $this->form = new Form($tableName);

    $this->fields = array();

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
        $this->fields[$name]['type'] = 'hidden'; 
        continue;
      }

      if ($s[Builder::SC_NOTNULL]) {
        // TODO: add ability to configure fields, need to save the fields in a list variables first
        $vld->set($name, new Validator\Required);
      }
      $this->fields[$name]['type'] = $type;
    }
    $this->validator = $vld;
  }

  public function setOptions($name, $options)
  {
    if (isset($this->fields[$name])) {
      $this->fields[$name]['options'] = $options;
    }
  }

  public function setType($name, $type)
  {
    if (isset($this->fields[$name])) {
      $this->fields[$name]['type'] = $type;
    }
  }

  public function build()
  {
    $form =& $this->form;
    foreach ($this->fields as $name => $f) {
      $child = $form->createField($f['type'], $name);
      if (isset($f['options'])) $child->setOptions($f['options']);

      $form->add($child);
    }

    if (count($this->fields) > 0) {
      $form->add('submit', 'Submit');
      $form->setValidator($this->validator);
    }
    return $form;
  }

  public function __toString()
  {
    return (string) $this->form;
  }

  public function bindValidateSave(Request $request)
  {
    $status = $this->form->bindValidate($request);

    if (!$status) return false;

    $this->builder->import($this->form->getValues());

    return $this->builder->save();
  }

}