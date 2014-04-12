<?php

namespace PetakUmpet\Formatter\Form;

class BootstrapHorizontal extends BaseFormFormatter {

  protected $form;

  public function __construct($form)
  {
    $this->form = $form;

    $this->formClass = 'form-horizontal';
    $this->formStart = '<fieldset>';
    $this->formEnd = '</fieldset>';

    $this->fieldRowStart['normal'] = '<div class="form-group">';
    $this->fieldRowEnd['normal'] = '</div>';

    $this->fieldRowStart['hide'] = '<div class="form-group" style="display: none;">';

    $this->fieldRowStart['error'] = '<div class="form-group has-error">';
    $this->fieldRowEnd['error'] = '</div>';

    $this->fieldLabelClass = 'col-sm-2 control-label';

    $this->fieldStart = '<div class="col-sm-10">';
    $this->fieldEnd = '</div>';

    $this->fieldHelpTagFormat = '<span class="help-block">%s</span>';

    $this->actionStart = '<div class="form-group"><div class="col-sm-offset-2 col-sm-10">';
    $this->actionEnd = '</div></div>';
  }


}