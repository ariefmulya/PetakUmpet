<?php

namespace PetakUmpet\Form\Component;


use PetakUmpet\Singleton;
use PetakUmpet\Request;
use PetakUmpet\Form;
use PetakUmpet\Form\Field;
use PetakUmpet\Validator;
use PetakUmpet\Database\Schema;
use PetakUmpet\Database\Accessor;

class TableAdapterForm {

  private $db;
  private $tableName;
  private $schema;
  private $form;
  private $filter;

  private $readOnly;

  public function __construct($tableName, $columns=array(), $skip=array(), $action=null)
  {
    $this->db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->tableName = $tableName;

    $this->schema = new Schema($tableName);

    $this->form = new Form($tableName, $action);

    $this->readOnly = false;

    $this->cancelAction = 'history.go(-1)'; // default form cancel action

    $this->useSaveButton = true;
    $this->useSaveAddButton = true;
    $this->useCancelButton = true;

    $schema = $this->schema->get();
    $vld = new Validator;

    foreach ($schema as $s) {
      $name  = $s[Schema::SC_COLNAME];
      $type  = $this->columnTypeMap($s[Schema::SC_COLTYPE]);
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
      if ($this->useCancelButton) $this->form->addAction(new Field\Button('Cancel', 
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
        'timestamp' => 'date',
        'datetime' => 'date',
        'boolean' => 'radioGroup',
        'bool' => 'radioGroup',
      );
    if (isset($a[$coltype])) return $a[$coltype];

    return 'text';
  }

  public function setCancelAction($value)
  {
    $this->cancelAction = $value;
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
    $dba = new Accessor($this->tableName);
    $id = $dba->save($this->form->getValues(), $this->schema->getPK());

    if ($id) {
      $this->form->setFieldValue('id', $id);
      return $id;
    }
    return false; 
  }

  public function setActionButtons($save=true, $saveadd=true, $cancel=true)
  {
    $this->useSaveButton = $save;
    $this->useSaveAddButton = $saveadd;
    $this->useCancelButton = $cancel;
  }

  public function bindValidateSave(Request $request)
  {
    if ($this->bindValidate($request)) 
      return $this->save();

    return false;
  }

  public function setFormValues($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldValue($k, $v);
    }
  }

  public function setFormTypes($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldType($k, $v);
    }
  }

  public function setFormOptions($params)
  {
    foreach ($params as $k => $v) {
      $this->form->setFieldOptions($k, $v);
    }
  }

  public function setValuesById($id)
  {
    $dba = new Accessor($this->tableName);
    $data = $dba->findOneBy(array('id' => $id));
    $this->form->bind($data);
  }

  public function setValuesBy($params)
  {
    $dba = new Accessor($this->tableName);
    $data = $dba->findOneBy($params);
    $this->form->bind($data);
  }

}