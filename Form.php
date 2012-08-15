<?php

namespace PetakUmpet;

use PetakUmpet\Form\Field\BaseField;

class Form {

  protected $fields;
  protected $actionFields;

  protected $name;
  protected $action;
  protected $method;
  protected $validator;

  protected $formatter;

  public function __construct($name='Form', $action=null, $id=null, $method='POST', $formatter='BootstrapHorizontal')
  {
    $this->name = $name;
    $this->id = $id === null ? $name : $id;
    $this->action = $action;
    $this->method = $method;

    $this->formatter = $formatter;
  }

  public function __toString()
  {
    $cname = '\\PetakUmpet\\Form\\Formatter\\' . $this->formatter;
    $formatter = new $cname($this);

    return (string) $formatter;
  }

  public function getFormName()
  {
    return $this->name;
  }

  public function getFormId()
  {
    return $this->id;
  }

  public function setFormMethod($method)
  {
    $this->method = $method;
  }

  public function getFormMethod()
  {
    return $this->method;
  }

  public function getFormAction()
  {
    return $this->action;
  }
  
  public function createField($field, $name=null, $extra=null, $label=null, $id=null)
  {
    // some hard-coded aliases
    if ($field == 'checkbox') $field = 'checkboxGroup';
    if ($field == 'radio') $field = 'radioGroup';
    // funnily the above hardcode is actually good to keep our 
    // class names reasonable while still providing fallback for user-error

    $class_name = '\\PetakUmpet\\Form\\Field\\' . ucfirst($field);
    if (class_exists($class_name)) {
      $f = new $class_name($name, $extra, $label, $id);
    } else {
      $type = $field;
      $f = new BaseField($name, $extra, $label, $id);
      $f->setType($type);
    }
    assert($f instanceof BaseField);
    return $f;
  }

  public function add($field, $name=null, $extra=null, $label=null, $id=null)
  {
    if (!($field instanceof BaseField)) {
      $f = $this->createField($field, $name, $extra, $label, $id);
      if ($f) $this->fields[strtolower($f->getName())] = $f;

    } else {
      $this->fields[$field->getName()] = $field;
    }
  }

  public function addAction(BaseField $field)
  {
    $this->actionFields[] = $field;
  }

  public function setValidator(\PetakUmpet\Validator $validator)
  {
    $this->validator = $validator;
  }

  public function getField($name)
  {
    return $this->fields[$name];
  }

  public function getFields()
  {
    return $this->fields;
  }

  public function setFieldValue($name, $value)
  {
    if (isset($this->fields[$name]) && $this->fields[$name] instanceof BaseField) {
      $this->fields[$name]->setValue($value);
      return true;
    }
    return false;
  }

  public function getFieldValue($name)
  {
    if (isset($this->fields[$name]) && $this->fields[$name] instanceof BaseField) {
      return $this->fields[$name]->getValue();
    }
    return false;
  }

  public function getActions()
  {
    return $this->actionFields;
  }

  public function getValues()
  {
    $v = array();
    foreach ($this->fields as $name => $f) {
      $v[$name] = $f->getValue();
    }
    return $v;
  }

  public function bindValidate(Request $request)
  {
    $status = true;

    foreach ($this->fields as $k => $f) {
      $name = $f->getName();
      $value = $request->get($name);

      if ($value !== null && $value != '' && $f instanceof BaseField) {
        $f->setValue($value);
      }

      if (isset($this->validator)) {
        if (!$this->validator->check($name, $f->getValue())) {
          $f->setErrorText($this->validator->getErrorText($name));
          $status = false;
        }
      }
    }
    return $status;
  }

  public function bind($data)
  {
    foreach ($this->fields as $k => $f) {
      $name = $f->getName();
      if (isset($data[$name])) {
        $f->setValue($data[$name]);
      }
    }
  }

}