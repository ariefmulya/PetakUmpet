<?php

namespace PetakUmpet\Form\Field;

class DateTime extends Text {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $d = new\DateTime;

    $extra['data-format'] = 'yyyy-MM-dd hh:mm:ss';
    // set value default to current time
    $extra['value'] = $d->format('Y-m-d H:i:s'); 
    parent::__construct($name, $extra, $label, $id);
  }

  public function __toString()
  {
    $id = $this->getAttribute('id');
    $this->removeAttribute('id');

    $s  = '<div id="'.$id.'" class="input-append date">';
    $s .= parent::__toString();
    $s .= '<span class="add-on"><i class="fa fa-calendar"></i></span>';
    $s .= '</div>';
    $s .= '<script type="text/javascript">$(\'#'.$id.'\').datetimepicker();</script>';
    return $s;
  }

  public function setValue($value)
  {
    if ($value) {
      $value = substr($value, 0, 19);
      $v = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
      parent::setValue($v->format('Y-m-d H:i:s'));
    }
  }
}