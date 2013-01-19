<?php

namespace PetakUmpet\Form\Field;

class GroupField extends BaseField {

  private $childs;

  private $name;
  private $extra;
  private $label;
  private $id;

  protected $multiple;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $this->name = $name;
    $this->extra = $extra;
    $this->label = $label;
    $this->id = $id;
    $this->multiple = false;
    $this->useOptions = true;
    $this->childs = array();
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getLabel()
  {
    $label = $this->label;
    if ($label === null) {
      $label = $this->name;
    }
    return ucwords(str_replace('_', ' ', $label));
  }

  public function setOptions(array $options)
  {
    foreach ($options as $k => $v) {
      $c = new BaseField(
          $this->name . ($this->multiple == true ? '[]' : ''), 
          $this->extra, 
          $this->label, 
          $this->id
        );

      $c->setType($this->getType());
      $c->setValue($k);
      $c->setDescription($v);
      $this->childs[] = $c;
    }
  }
  public function getLabelTag($labelClass='control-label')
  {
    return '<label class="'.$labelClass.'">'.$this->getLabel().'</label>';
  }

  public function __toString()
  {
    $s = '';

    $inputVal = $this->getValue();

    foreach ($this->childs as $c) {
      $s .= '<label class="'.$this->getType().' inline">';

      if ($this->multiple) {
        if (
            $inputVal !== null && is_array($inputVal) && 
            in_array($c->getValue(), $this->getValue())
          ) {
          $c->setAttribute('checked', 'checked');
        }
      } else {
        if ($inputVal !== null && $inputVal == $c->getValue()) {
          $c->setAttribute('checked', 'checked');
        }
      }
      $s .= $c;
      $s .= $c->getDescription();
      $s .= '</label>';
    }
    return $s;
  }

}
