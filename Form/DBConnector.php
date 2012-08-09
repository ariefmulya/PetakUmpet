<?php

namespace PetakUmpet\Form;

use PetakUmpet\Request;
use PetakUmpet\Form;
use PetakUmpet\Form\Field;

use PetakUmpet\Validator;
use PetakUmpet\Database\Builder;

class DBConnector {

  private $tableName;
  private $action;

  private $builder;
  private $fields;
  private $validator;
  private $form;
  private $formActions;

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

  public function __construct($tableName, $columns=array(), $skip=array(), $action=null)
  {
    $this->tableName = $tableName;
    $this->action = $action;

    $this->formActions = array();

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
        $this->fields[$name]['extra'] = array('required' => true);
      }

      if (($rel = $this->builder->getColumnRelation($name))) {
        $this->fields[$name]['relation'] = $rel;
        $this->fields[$name]['relname'] = 'nama';
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

  public function setRelationName($name, $value)
  {
    if (isset($this->fields[$name])) {
      $this->fields[$name]['relname'] = $value;
    }
  }

  public function setRelationFilter($name, $value)
  {
    if (isset($this->fields[$name])) {
      $this->fields[$name]['relfilter'] = $value;
    }
  }

  public function setValue($name, $value)
  {
    if ($this->form instanceof \PetakUmpet\Form && $this->form->setFieldValue($name, $value)) 
      return true;

    if (isset($this->fields[$name])) {
        $this->fields[$name]['value'] = $value;
      return true;
    }
    return false;
  }

  public function getValue($name)
  {
    if ($this->form->getFieldValue($name))
      return true;

    if (isset($this->fields[$name]) && isset($this->fields[$name]['value'])) {
        return $this->fields[$name]['value'];
    }
    return false;
  }

  public function getFormName()
  {
    return $this->tableName;
  }

  public function getForm()
  {
    return $this->form;
  }

  public function build()
  {
    $form =& $this->form;

    $form = new Form($this->tableName, $this->action);

    foreach ($this->fields as $name => $f) {
      $label = isset($f['label']) ? $f['label'] : null;

      $extra = null;
      if (isset($f['extra'])) $extra = $f['extra'];
      
      if (isset($f['relation']) && $f['type'] != 'hidden') {
        $child = $form->createField('selectFKey', $name, $extra, $label);
        $child->setDbo($this->builder->getDbo());
        $child->setRelationData($f['relation'], $f['relname']);

        if (isset($f['relfilter'])) {
          $child->setFilter($f['relfilter']);
        }
        $child->getOptionsFromRelation();

      } else {
        $child = $form->createField($f['type'], $name, $extra, $label);

        if (isset($f['options']) && $child->useOptions()) {
          $child->setOptions($f['options']);
        }
      }

      $form->add($child);
    }

    if (count($this->fields) > 0) {
      $form->addAction(new Field\Submit('Submit'));
      foreach ($this->formActions as $f) {
        $form->addAction($f);
      }
      $form->setValidator($this->validator);
    }
    return $form;
  }

  public function addFormAction($field)
  {
    $this->formActions[] = $field;
  }

  public function isClose()
  {
    return ($this->form->getActionValue() == 'Save & Close');
  }

  public function isAdd()
  {
    return ($this->form->getActionValue() == 'Save & Add');
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