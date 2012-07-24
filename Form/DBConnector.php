<?php

namespace PetakUmpet\Form;

use PetakUmpet\Request;
use PetakUmpet\Form;
use PetakUmpet\Form\Field;

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
        $vld->set($name, new Validator\Required);
      }

      $options = $this->builder->getOptionsFromRelations($name);

      if (count($options) > 0) {
        $type = 'select';
        $this->fields[$name]['options'] = $options;
      }
      $this->fields[$name]['type'] = $type;
      $this->fields[$name]['label'] = ucwords(str_replace('_', ' ', str_replace('_id', '', $name)));

    }
    $this->validator = $vld;
  }

  public function __toString()
  {
    return (string) $this->form;
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

  public function setValue($name, $value)
  {
    if ($this->form->setChildValue($name, $value)) 
      return true;

    if (isset($this->fields[$name])) {
        $this->fields[$name]['value'] = $value;
      return true;
    }
    return false;
  }

  public function build()
  {
    $form =& $this->form;

    $form = new Form($this->tableName);

    foreach ($this->fields as $name => $f) {
      $label = isset($f['label']) ? $f['label'] : null;
      
      $child = $form->createField($f['type'], $name, null, $label);

      if (isset($f['options']) && $child->useOptions()) {
        $child->setOptions($f['options']);
      }

      $form->add($child);
    }

    if (count($this->fields) > 0) {
      $form->add(new Field\Submit, 'Submit');
      $form->setValidator($this->validator);
    }
    return $form;
  }

  public function bindValidateSave(Request $request)
  {
    $status = $this->form->bindValidate($request);

    if (!$status) return false;

    $this->builder->import($this->form->getValues());

    $id = $this->builder->save();

    if ($id) {
      $this->setValue('id', $id);
      return $id;
    }
    return false;
  }

  public function importById($id)
  {
    $this->builder->importById($id);

    if (!isset($this->form)) {
      $this->build();
    }

    foreach ($this->builder->getTableData() as $d) {
      $this->form->bind($d);
      break; // only support 1 row data for now
    }
  }

}