<?php

namespace PetakUmpet;

use PetakUmpet\Form\BaseFormField as BaseFormField;

class Form {

  const GRID_BOOTSTRAP = 1;

  protected $childs;
  protected $name;
  protected $method;
  protected $validator;

  protected $gridFormat;

  function __construct($name='Form', $class='well', $method='POST')
  {
    $this->name = $name;
    $this->method = $method;
    $this->gridFormat = self::GRID_BOOTSTRAP;

    $this->formClass = $class;

    $this->formStart = array(
        self::GRID_BOOTSTRAP => '<fieldset>'
      );
    $this->formEnd = array(
        self::GRID_BOOTSTRAP => '</fieldset>'
      );

    $this->fieldRowStart['normal'] = array(
        self::GRID_BOOTSTRAP => '<div class="control-group">'
      );
    $this->fieldRowStart['error'] = array(
        self::GRID_BOOTSTRAP => '<div class="control-group error">'
      );
    $this->fieldRowEnd['normal'] = array(
        self::GRID_BOOTSTRAP => '</div>'
      );
    $this->fieldRowEnd['error'] = array(
        self::GRID_BOOTSTRAP => '</div>'
      );
    $this->fieldLabelClass = array(
        self::GRID_BOOTSTRAP => 'control-label'
      );
    $this->fieldStart = array(
        self::GRID_BOOTSTRAP => '<div class="controls">'
      );
    $this->fieldEnd = array(
        self::GRID_BOOTSTRAP => '</div>'
      );
    $this->fieldHelpTagFormat = array(
        self::GRID_BOOTSTRAP => '<span class="help-inline">%s</span>'
      );
  }

  function __toString()
  {
    $s = '<form method="' . $this->method . '" class="'.$this->formClass.'" >'; 

    if (count($this->childs) > 0) { 
      $s .= $this->formStart[$this->gridFormat];
      foreach ($this->childs as $k => $f) {
        if ($f instanceof \PetakUmpet\Form\Hidden) {
          $s .= $f;
          continue;
        }

        $errorText = $f->getErrorText();
        $fieldStatus = $errorText == '' ? 'normal' : 'error';

        $s .= $this->fieldRowStart[$fieldStatus][$this->gridFormat];
        $s .= $f->getLabelTag($this->fieldLabelClass[$this->gridFormat]);
        $s .= $this->fieldStart[$this->gridFormat];
        $s .= $f; 
        if ($errorText != '') {
          $s .= sprintf($this->fieldHelpTagFormat[$this->gridFormat], $errorText);
        }
        $s .= $this->fieldEnd[$this->gridFormat];
        $s .= $this->fieldRowEnd[$fieldStatus][$this->gridFormat];

      }
      $s .= $this->formEnd[$this->gridFormat];
    }
    $s .= '</form>';

    return $s;
  }

  function __call($name, $args)
  {
    if (substr($name, 0, 3) == 'get') {
      $index = strtolower(substr($name, 3));

      if (!isset($this->childs[$index])) return null;

      return $this->childs[$index]->getValue();
    }
  }
  public function add($child, $name=null, $extra=null, $label=null, $id=null)
  {
    if (!($child instanceof BaseFormField)) {
      $class_name = '\\PetakUmpet\\Form\\' . ucfirst($child);
      if (class_exists($class_name)) {
        $child = new $class_name($name, $extra, $label, $id);
      } else {
        $type = $child;
        $child = new BaseFormField($name, $extra, $label, $id);
        $child->setType($type);
      }
    }
    assert($child instanceof BaseFormField);
    $this->childs[strtolower($child->getName())] = $child;
  }

  public function setValidator(\PetakUmpet\Validator $validator)
  {
    $this->validator = $validator;
  }

  function bindValidate(Request $request)
  {
    $status = true;

    foreach ($this->childs as $k => $f) {
      $name = $f->getName();
      $value = $request->get($name);

      if ($value !== null && $value != '' && $f instanceof BaseFormField) {
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

}