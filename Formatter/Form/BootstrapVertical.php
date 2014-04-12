<?php

namespace PetakUmpet\Formatter\Form;

class BootstrapVertical extends BaseFormFormatter {

  protected $form;

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


}