<?php

namespace PetakUmpet\Form\Field;

class Tinymce extends BaseField {

  private $url    = 'js/tinymce/tiny_mce.js';
  private $theme  = 'simple';

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);

    $this->startTag = '<textarea ';
    $this->closeStartTag = '>';
    $this->endTag = '</textarea>';
  }

  public function setSourceUrl($url)
  {
    $this->url = $url;
  }

  public function setTheme($theme)
  {
    $this->theme = $theme;
  }

  public function __toString()
  {
    $s = parent::__toString();
    $s .= '<script type="text/javascript">
              $().ready(function() {
               $("textarea#'.$this->getAttribute('id').'").tinymce({
                  script_url : "'.$this->url.'",
                  theme : "simple",
               });
          </script>';

    return $s;
  }

}