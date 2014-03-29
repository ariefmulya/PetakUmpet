<?php

namespace PetakUmpet\Formatter\Form;

use PetakUmpet\Formatter as Formatter;

class BootstrapVertical extends Formatter {

  private $form;

  public function __construct($form)
  {
    $this->form = $form;

    $this->formClass = 'form-vertical';
    $this->formStart = '<fieldset>';
    $this->formEnd = '</fieldset>';

    $this->fieldRowStart['normal'] = '<div class="control-group">';
    $this->fieldRowEnd['normal'] = '</div>';

    $this->fieldRowStart['hide'] = '<div class="control-group" style="display: none;">';

    $this->fieldRowStart['error'] = '<div class="control-group error">';
    $this->fieldRowEnd['error'] = '</div>';

    $this->fieldLabelClass = 'control-label';

    $this->fieldStart = '<div class="controls">';
    $this->fieldEnd = '</div>';

    $this->fieldHelpTagFormat = '<span class="help-inline">%s</span>';

    $this->actionStart = '<div class="form-actions">';
    $this->actionEnd = '</div>';
  }

  public function __toString()
  {
    $method = $this->form->getMethod();
    $name = $this->form->getName();
    $id = $this->form->getId();
    $action = $this->form->getAction();


    $s = '<form method="' . $method 
        . '" class="form-vertical" '
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

        $s .= $f->getLabelTag($this->fieldLabelClass);

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