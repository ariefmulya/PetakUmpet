<?php

namespace PetakUmpet\Form;

class GroupFormField extends BaseFormField {

  private $childs;

  private $name;
  private $extra;
  private $label;
  private $id;

  private $type;

  protected $multiple;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $this->name = $name;
    $this->extra = $extra;
    $this->label = $label;
    $this->id = $id;
    $this->multiple = false;
    $this->useOptions = true;
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
    return ucwords(str_replace('_', ' ', $this->name));
  }

  public function setOptions(array $options)
  {
    foreach ($options as $k => $v) {
      $c = new BaseFormField(
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

  public function getLabelTag()
  {
    return '<label class="control-label">'.$this->getLabel().'</label>';
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
          $c->setChecked('checked');
        }
      } else {
        if ($inputVal !== null && $inputVal == $c->getValue()) $c->setChecked('checked');
      }
      $s .= $c;
      $s .= $c->getDescription();
      $s .= '</label>';
    }
    return $s;
  }

}