<?php

namespace PetakUmpet\Form\Field;

class Wysiwyg extends Textarea {

  public $explanation = '';

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setAttribute('class', 'wysiwyg');
    $this->explanation = '';
  }

  public function setExplanation($val)
  {    
    $this->explanation = '<br />'.$val;
  }

  public function __toString()
  {
    $s = parent::__toString();
    $s .= $this->explanation;
    
    return $s;
  }

}