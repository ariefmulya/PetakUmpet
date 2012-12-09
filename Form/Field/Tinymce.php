<?php

namespace PetakUmpet\Form\Field;

class Tinymce extends Textarea {

  private $url    = 'js/nicEdit.js';
  private $explanation = '';

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setAttribute('class', 'wysiwyg');
  }

  public function setSourceUrl($url)
  {
    $this->url = $url;
  }

  public function setTheme($theme)
  {
    $this->theme = $theme;
  }

  public function setExplanation($explanation)
  {
    $this->explanation = '<br />'.$explanation;
  }

  public function __toString()
  {
    $s = parent::__toString();
    $s .= '<script type="text/javascript">
          $(document).ready(function() {
            $(".tinymce").tinymce();
          });
          </script>';    
    $s .= $explanation;
    return $s;
  }

}