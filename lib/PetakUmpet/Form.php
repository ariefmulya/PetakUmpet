<?php

namespace PetakUmpet;

class Form {

  private $fields;
  private $method;

  function __construct($method='POST')
  {
    $this->method = 'POST';
  }

  function __toString()
  {
    $s = '<form method="' . $this->method . '">'; 

    if (count($this->fields) > 0) {
      foreach ($this->fields as $f) {
        $s .= $f; 
      }
    }
    $s .= '</form>';

    return $s;
  }

  function bind(Request $request)
  {
    $status = true;

    foreach ($this->fields as $f) {
      $f->setValue($request->getData($f->getName()));
      if (!$f->isValid()) {
        $status = false;
      }
    }

    return $status;
  }

  public function addField($field)
  {
    $this->fields[] = $field;
  }

}