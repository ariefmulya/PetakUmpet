<?php

namespace PetakUmpet\Form\Field;

class Wysiwyg extends Textarea {

  public $explanation = '';

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    // $this->setAttribute('class', 'wysiwyg');
    $this->explanation = '';
  }

  public function setExplanation($val)
  {    
    $this->explanation = '<br />'.$val;
  }

  public function __toString()
  {
    $js = '<script type="text/javascript">
            bkLib.onDomLoaded(function() {
              new nicEditor({';
    // feature added here                
    $js .= "uploadURI : '../../app/cargo/Lib/nicUpload.php', ";
    $js .= "iconsPath:'../img/nicEditorIcons.gif',";
    $js .= "fullPanel: true,";

    $js .= "})";

    $js .= ".panelInstance('".$this->getAttribute('id')."');";
    $js .= "});";

    $js .= "</script>";

    // $s = $this->js;
    $s = $js.parent::__toString();
    $s .= $this->explanation;
    
    return $s;
  }

}