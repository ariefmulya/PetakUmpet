<?php


namespace PetakUmpet\Form\Field;

class Select extends BaseField {

  private $options;

  private $chainTarget;
  private $chainUrl;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->startTag = '<select ';
    $this->closeStartTag = '>';
    $this->endTag = '</select>';
    $this->useInnerValue = true;
    $this->useOptions = true;
    $this->options = array();
    $this->chainTarget = false;
    $this->chainUrl = false;
  }

  public function setOptions(array $options)
  {
    $this->options = $options;
  }

  public function getInnerValue()
  {
    $s = '';
    $s .= '<option value=""></option>';

    if (count($this->options) > 0) {
      foreach ($this->options as $k => $v) {
        $t = $k == $this->getValue() ? 'selected' : '';
        $s .= '<option value="'.$k.'" '.$t.'>'.$v.'</option>';
      }
    }
    return $s;
  }

  public function setChainTarget($target, $url)
  {
    $this->chainTarget = $target;
    $this->chainUrl = $url;
  }

  public function __toString()
  {
    $s  = parent::__toString();

    if ($this->chainTarget !== false && $this->chainTarget != '') {
      $id = $this->getAttribute('id');
      $targetId = $this->chainTarget;
      $targetUrl = $this->chainUrl;

      $s .= "<script type='text/javascript'>
              $(document).ready(function() { 
                  $('#$id').selectChain({ target: $('#$targetId'), url: '$targetUrl', type: 'post' });
              });
            </script>";
    }

    return $s;
  }
}
