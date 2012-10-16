<?php

namespace PetakUmpet;

use PetakUmpet\Form\Field\BaseField;

class Form {

  protected $fields;
  protected $actionFields;

  protected $validator;
  protected $formatter;

  private $readOnly;
  private $useMultipart;

  public function __construct($name='Form', $action=null, $id=null, $method='POST', $formatter='\\PetakUmpet\\Form\\Formatter\\BootstrapHorizontal')
  {
    $this->name   = $name;
    $this->id     = ($id === null ? $name : $id);
    $this->action = $action;
    $this->method = $method;

    $this->readOnly = false;
    $this->useMultipart = false;

    $this->formatter = $formatter;
  }

  public function getName()     { return $this->name;   }
  public function getId()       { return $this->id;     }
  public function getAction()   { return $this->action; }
  public function getMethod()   { return $this->method; }
  public function isMultipart() { return $this->useMultipart; }

  public function setFormAction($action)    { $this->action = $action; }
  public function setReadOnly($state=true)  { $this->readOnly = $state; }
  public function setMultipart($state=true) { $this->useMultipart = $state; }

  public function __toString()
  {
    $cname = $this->formatter;
    $formatter = new $cname($this);

    if ($this->readOnly === true) {
      foreach ($this->fields as $f) {
        $f->setAttribute('readonly', 'readonly');
      }
    }

    return (string) $formatter;
  }

  public function getFormScript()
  {
    // any javascript function needed for the form should go here
    $s = "<script>
      $(document).ready(function() {
        prices = $('#".$this->id."').find('input[data-price=true]');
        for (var i=0; i<prices.length; i++) {
          p = jQuery(prices[i]);
          p.priceFormat({prefix: '', centsSeparator: '', centsLimit: 0 });
        }
      });
    </script>
    ";

    return $s;
  }

  private function create($field, $name=null, $extra=null, $label=null, $id=null)
  {
    if ($field instanceof BaseField) {
      return $field;
    }

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
      $f = $this->create($field, $name, $extra, $label, $id);
      if ($f) $this->fields[strtolower($f->getName())] = $f;

    } else {
      $this->fields[$field->getName()] = $field;
    }
  }

  public function remove($name)
  {
    if (isset($this->fields[$name])) {
      unset($this->fields[$name]);
    }
  }

  public function replace($name, BaseField $field)
  {
    $this->fields[$name] = $this->create($field, $name);
  }

  public function setFieldAttribute($name, $key, $value)
  {
    if (isset($this->fields[$name])) {
      return $this->fields[$name]->setAttribute($key, $value);
    }
  }

  public function setFieldValue($name, $value)
  {
    if (isset($this->fields[$name])) {
      return $this->fields[$name]->setValue($value);
    }
  }

  public function setFieldLabel($name, $value)
  {
    if (isset($this->fields[$name])) {
      return $this->fields[$name]->setLabel($value);
    }
  }

  public function setFieldType($name, $type)
  {
    if (isset($this->fields[$name])) {
      $f = $this->create($type, $name);
      $this->replace($name, $f);
    }
  }

  public function setFieldOptions($name, $options)
  {
    if (isset($this->fields[$name])) {
      return $this->fields[$name]->setOptions($options);
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

  public function addValidator($name, \PetakUmpet\Validator\Base $validator)
  {
    $this->validator->add($name, $validator);
  }

  public function removeValidator($name)
  {
    $this->validator->remove($name);
  }
  
  public function getField($name)
  {
    if (isset($this->fields[$name])) {
      return $this->fields[$name];
    }
    return null;
  }

  public function getFields()  { return $this->fields;       }
  public function getActions() { return $this->actionFields; }

  public function getFieldValue($name)
  {
    if (isset($this->fields[$name])) {
      return $this->fields[$name]->getValue();
    }
    return null;
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

  public function reset()
  {
    foreach ($this->fields as $k => $f) {
      $f->setValue(null);
    }
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
