<?php


namespace PetakUmpet\Form;

class Select extends BaseFormField {

  private $options;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->startTag = '<select ';
    $this->closeStartTag = '>';
    $this->endTag = '</select>';
    $this->useInnerValue = true;
  }

  public function setOptions(array $options)
  {
    $this->options = $options;
  }

  public function getInnerValue()
  {
    $s = '';
    foreach ($this->options as $k => $v) {
      $t = $k == $this->getValue() ? 'selected' : '';
      $s .= '<option value="'.$k.'" '.$t.'>'.$v.'</option>';
    }
    return $s;
  }

}