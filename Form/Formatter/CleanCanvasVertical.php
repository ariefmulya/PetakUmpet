<?php

namespace PetakUmpet\Form\Formatter;

class CleanCanvasVertical {

  private $form;

  public function __construct($form)
  {
    $this->form = $form;
  }

  public function __toString()
  {
    $method = $this->form->getMethod();
    $name = $this->form->getName();
    $id = $this->form->getId();
    $action = $this->form->getAction();


    $s = '<div class="form"><form method="' . $method 
        . '" class="form-vertical small" '
        . 'name="'.$name.'" id="'.$id.'" '
        . ($this->form->isMultipart() ? ' enctype="multipart/form-data" ' : '' )
        . ($action !== null ? 'action="'. $action . '" >': ' >'); 

    if (($fields = $this->form->getFields()) && count($fields) > 0) { 
      foreach ($fields as $k => $f) {
        if ($f instanceof \PetakUmpet\Form\Field\Hidden) {
          $s .= (string) $f;
          continue;
        }

        $errorText = $f->getErrorText();

        $s .= $f->getLabelTag();
        $s .= (string) $f; 
        if ($errorText != '') {
          $s .= sprintf('<span class="help-inline">%s</span>', $errorText);
        }

      }

      if (($actions = $this->form->getActions()) && count($actions) > 0 ) {
        $s .= '<div class="form-actions">';
        foreach ($actions as $f) {
          $s .= (string) $f;
        }
        $s .= '</div>';
      }
    }
    $s .= '</form></div>';
    $s .= $this->form->getScript();
    return $s;
  }

}