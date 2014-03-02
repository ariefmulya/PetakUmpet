<?php

namespace PetakUmpet\Form\Component;


use PetakUmpet\Singleton;
use PetakUmpet\Request;
use PetakUmpet\Form;
use PetakUmpet\Form\Field;
use PetakUmpet\Validator;
use PetakUmpet\Database\Schema;
use PetakUmpet\Database\Model;

class TableAdapterForm {

  private $db;
  private $tableName;
  private $schema;
  private $form;
  private $filter;

  private $readOnly;

  public function __construct($tableName, $columns=array(), $skip=array(), $action=null, $formatter=null)
  {
    $this->db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->tableName = $tableName;

    $this->schema = new Schema($tableName);

    if ($formatter === null) {
      $this->form = new Form($tableName . 'Form', $action);
    } else {
      $this->form = new Form($tableName . 'Form', $action, null, 'POST', $formatter);
    }

    $this->readOnly = false;

    $this->cancelAction = 'history.go(-1)'; // default form cancel action

    $this->useSaveButton = true;
    $this->useSaveAddButton = true;
    $this->useCancelButton = true;

    $schemaDetail = $this->schema->getSchemaDetail();
    $vld = new Validator;

    foreach ($schemaDetail as $s) {

      $name  = $s[Schema::SC_COLNAME];
      $type  = $s[Schema::SC_FFIELDTYPE];
      $label = ucwords(str_replace('_', ' ', str_replace('_id', '', $name)));
      $extra = array();

      // check if we need to skip columns
      if (count($columns) > 0 && !in_array($name, $columns) || 
          (count($skip) > 0 && in_array($name, $skip))) {
        continue;
      }

      if ($s[Schema::SC_PRIMARY]) {
        $this->form->add('hidden', $name, $extra);
        continue;
      }

      if ($s[Schema::SC_NOTNULL]) {
        $vld->add($name, new Validator\Required);
        $extra = array('required' => true);
      }

      if (strstr($s[Schema::SC_PDOTYPE], \PDO::PARAM_INT)) {
        $vld->add($name, new Validator\Numeric);
      }

      if (($rel = $this->schema->getColumnRelation($name))) {
        $select = new \PetakUmpet\Form\Field\Select($name, $extra, $label);
        $options = $this->getOptionsFromRelation($rel);
        $select->setOptions($options);
        $this->form->add($select);
        continue;
      }

      $this->form->add($type, $name, $extra, $label);
    }

    $this->form->setValidator($vld);
  }

  public function __toString()
  {
    if ($this->readOnly === false) {
      if ($this->useSaveButton) $this->form->addAction(new Field\Submit('Save'));
      if ($this->useSaveAddButton) $this->form->addAction(new Field\Submit('Save & Add', array('class' => 'btn')));
      if ($this->useCancelButton) $this->form->addAction(new Field\Button('Back', 
                    array('class' => 'btn btn-warning', 'onclick' => $this->cancelAction )));
    }
    return (string) $this->form;
  }

  private function columnTypeMap($coltype)
  {
    $a = array(
        'text' => 'textarea',
        'hidden' => 'hidden',
        'date' => 'date',
        'timestamp' => 'dateTime',
        'datetime' => 'dateTime',
        'boolean' => 'radioGroup',
        'bool' => 'radioGroup',
      );
    if (isset($a[$coltype])) return $a[$coltype];

    return 'text';
  }

  public function getName()
  {
    return $this->getFormObject()->getName();
  }
  
  public function setCancelAction($value)
  {
    $this->cancelAction = $value;
  }

  public function addValidator($name, \PetakUmpet\Validator\Base $validator)
  {
    $this->form->addValidator($name, $validator);
  }

  public function removeValidator($name)
  {
    $this->form->removeValidator($name);
  }

  public function setReadOnly($state)
  {
    $this->readOnly = $state;
    $this->form->setReadOnly($state);
  }

  public function getFormObject()
  {
    return $this->form;
  }

  public function getFormName()
  {
    return $this->form->getName();
  }

  private function getOptionsFromRelation($relation)
  {
    $db =& $this->db;

    $relId = $db->escapeInput($relation[Schema::FK_DSTID]);
    $relTable = $db->escapeInput($relation[Schema::FK_DSTTABLE]);

    $query = sprintf("SELECT * FROM %s", $relTable);    

    $dstId = null;
    $opt = array();

    if (($res = $db->queryFetchAll($query, $this->filter))) {
      foreach ($res as $r) {
        if ($dstId !== null) {
          if (isset($r[$dstId])) {
            $opt[$r[$relId]] = $r[$dstId];
          }
        } else {
          /* look for first column that contain string data */
          foreach ($r as $k => $v) {
            if (is_string($v) && $k != $relId) {
              $opt[$r[$relId]] = $v;
              $dstId = $k;
              break;
            }
          }
        }
      }
    }
    return $opt;
  }

  public function isSaveAndAdd(Request $request)
  {
    return ($request->get('submit') == 'Save & Add');
  }

  public function bindValidate(Request $request)
  {
    return $this->form->bindValidate($request);
  }

  public function save()
  {
    $dbm = new Model($this->tableName);
    $id = $dbm->save($this->form->getValues());

    if ($id) {
      $this->form->setFieldValue('id', $id);
      return $id;
    }
    return false; 
  }

  public function bindValidateSave(Request $request)
  {
    if ($this->bindValidate($request)) 
      return $this->save();

    return false;
  }

  public function setActionButtons($save=true, $saveadd=true, $cancel=true)
  {
    $this->useSaveButton = $save;
    $this->useSaveAddButton = $saveadd;
    $this->useCancelButton = $cancel;
  }

  public function setFormValidator(\PetakUmpet\Validator $vld)
  {
    $this->form->setValidator($vld);
  }

  public function setFieldValues($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldValue($k, $v);
    }
  }

  public function setFieldAttributes($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldAttribute($k, key($v), current($v));
    }
  }

  public function setFieldLabels($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldLabel($k, $v);
    }
  }

  public function setFieldTypes($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldType($k, $v);
    }
  }

  public function setFieldOptions($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldOptions($k, $v);
    }
  }

  public function getValues()
  {
    return $this->form->getValues();
  }
  
  public function setValuesById($id)
  {
    $dbm = new Model($this->tableName);
    $data = $dbm->get($id);
    $this->form->bind($data);
  }

  public function setValuesBy($params)
  {
    $dbm = new Model($this->tableName);
    $data = $dbm->getBy($params);
    $this->form->bind($data);
  }

}