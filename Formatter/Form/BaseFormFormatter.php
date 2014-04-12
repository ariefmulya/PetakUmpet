<?php

namespace PetakUmpet\Formatter\Form;

use PetakUmpet\Formatter as Formatter;

class BaseFormFormatter extends Formatter {

  public function __toString()
  {
    $method = $this->form->getMethod();
    $name = $this->form->getName();
    $id = $this->form->getId();
    $action = $this->form->getAction();


    $s = '<form role="form" method="' . $method 
        . '" class="' . $this->formClass . '" '
        . 'name="'.$name.'" id="'.$id.'" '
        . ($this->form->isMultipart() ? ' enctype="multipart/form-data" ' : '' )
        . ($action !== null ? 'action="'. $action . '" >': ' >'); 

    if (($fields = $this->form->getFields()) && count($fields) > 0) { 
      $s .= $this->formStart;

      foreach ($fields as $k => $f) {
        if ($f instanceof \PetakUmpet\Form\Field\Hidden) {
          $s .= (string) $f;
          continue;
        }

        $errorText = $f->getErrorText();
        $fieldStatus = $errorText == '' ? 'normal' : 'error';

        if ($f->getShowDisplay() === false) {
          $s .= $this->fieldRowStart['hide'];
        } else {
          $s .= $this->fieldRowStart[$fieldStatus];
        }

        if ($f->useLabel()) {
          $s .= $f->getLabelTag($this->fieldLabelClass);
        }

        $s .= $this->fieldStart;

        $s .= (string) $f; 
        if ($errorText != '') {
          $s .= sprintf($this->fieldHelpTagFormat, $errorText);
        }
        $s .= $this->fieldEnd;
        $s .= $this->fieldRowEnd[$fieldStatus];

      }

      if (($actions = $this->form->getActions()) && count($actions) > 0 ) {
        $s .= $this->actionStart;
        foreach ($actions as $f) {
          $s .= (string) $f;
        }
        $s .= $this->actionEnd;
      }

      $s .= $this->formEnd;
    }
    $s .= '</form>';
    $s .= $this->form->getScript();
    return $s;
  }

}
