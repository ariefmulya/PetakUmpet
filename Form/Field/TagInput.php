<?php

namespace PetakUmpet\Form\Field;

class TagInput extends BaseField {

  private $prependTag;
  private $appendTag;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('text');

    $this->prependTag = null;
    $this->appendTag  = null;
  }

  public function setPrependTag($value)
  {
    $this->prependTag = $value;
  }

  public function setAppendTag($value)
  {
    $this->appendTag = $value;
  }

  public function __toString()
  {
    $class = '';
    if ($this->prependTag !== null) {
      $class = ' input-prepend ';
    }
    if ($this->appendTag !== null) {
      $class = ' input-append ';
    }

    if ($this->prependTag === null && $this->appendTag === null) 
      return parent::__toString();

    $s  = '<div class="'.$class.'">';
    if ($this->prependTag !== null) $s .= '<span class="add-on">' . $this->prependTag . '</span>';
    $s .= parent::__toString();
    if ($this->appendTag !== null) $s .= '<span class="add-on">' . $this->appendTag . '</span>';
    $s .= '</div>';

    return $s;
  }

}