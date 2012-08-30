<?php

namespace PetakUmpet\Form\Formatter;

class BootstrapHorizontal {

  private $form;

  public function __construct($form)
  {
    $this->form = $form;

    $this->formClass = 'form-horizontal';
    $this->formStart = '<fieldset>';
    $this->formEnd = '</fieldset>';

    $this->fieldRowStart['normal'] = '<div class="control-group">';
    $this->fieldRowEnd['normal'] = '</div>';

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
        . '" class="form-horizontal" '
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

        $s .= $this->fieldRowStart[$fieldStatus];

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
    return $s;
  }

}