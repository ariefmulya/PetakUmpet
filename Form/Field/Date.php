<?php

namespace PetakUmpet\Form\Field;

class Date extends Text {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $d = new\DateTime;

    $extra['data-format'] = 'yyyy-MM-dd';

    // set value default to today
    $extra['value'] = $d->format('Y-m-d'); 

    parent::__construct($name, $extra, $label, $id);
  }

  public function __toString()
  {
    $id = $this->getAttribute('id');
    $this->removeAttribute('id');

    $s  = '<div id="'.$id.'" class="input-append">';
    $s .= parent::__toString();
    $s .= '<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>';
    $s .= '</div>';
    $s .= '<script type="text/javascript">$(\'#'.$id.'\').datetimepicker({pickTime: false});</script>';
    return $s;
  }

  public function setValue($value)
  {
    if ($value) {
      $value = substr($value, 0, 19);
      $v = \DateTime::createFromFormat('Y-m-d', $value);
      parent::setValue($v->format('Y-m-d'));
    }
  }
}