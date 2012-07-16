<?php

namespace PetakUmpet;

use PetakUmpet\Form\BaseFormField as BaseFormField;

class Form {

  const GRID_TABLE = 1;

  private $childs;
  private $method;

  private $gridFormat;

  function __construct($method='POST')
  {
    $this->method = 'POST';
    $this->gridFormat = self::GRID_TABLE;
  }

  function __toString()
  {
    $s = '<form method="' . $this->method . '">'; 

    if (count($this->childs) > 0) { 
      $s .= $this->gridFormat === self::GRID_TABLE ? '<table>' : '';
      foreach ($this->childs as $f) {
        $s .= $this->gridFormat === self::GRID_TABLE ? '<tr><td>' : '';
        $s .= $f->getLabelTag();
        $s .= $this->gridFormat === self::GRID_TABLE ? '<td>' : '';
        $s .= $f; 
      }
      $s .= $this->gridFormat === self::GRID_TABLE ? '</table>' : '';
    }
    $s .= '</form>';

    return $s;
  }

  function bind(Request $request)
  {
    $status = true;

    foreach ($this->childs as $f) {
      $val = $request->getData($f->getName());

      if ($val !== null && $val != '' && $f instanceof BaseFormField) {
        $f->setValue($val);
      }

      //iif (!$f->isValid()) {
      //  $status = false;
      //}
    }

    return $status;
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
    $this->childs[] = $child;
  }

}