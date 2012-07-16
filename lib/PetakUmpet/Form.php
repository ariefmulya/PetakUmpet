<?php

namespace PetakUmpet;

use PetakUmpet\Form\FormField as FormField;
use PetakUmpet\Form\FormField as FormButton;

class Form {

  private $childs;
  private $method;

  function __construct($method='POST')
  {
    $this->method = 'POST';
  }

  function __toString()
  {
    $s = '<form method="' . $this->method . '">'; 

    if (count($this->childs) > 0) {
      foreach ($this->childs as $f) {
        $s .= $f; 
      }
    }
    $s .= '</form>';

    return $s;
  }

  function bind(Request $request)
  {
    $status = true;

    foreach ($this->childs as $f) {
      $val = $request->getData($f->getName());

      if ($val !== null && $val != '' && $f instanceof FormField) {
        $f->setValue($val);
      }

      //iif (!$f->isValid()) {
      //  $status = false;
      //}
    }

    return $status;
  }

  public function add($child)
  {
    $this->childs[] = $child;
  }

}