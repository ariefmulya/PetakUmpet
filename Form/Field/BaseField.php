<?php

namespace PetakUmpet\Form\Field;

class BaseField {

  protected $startTag;
  protected $closeStartTag;
  protected $endTag;

  protected $attributes;
  protected $type;

  protected $useInnerValue;
  protected $errorText;
  protected $accessFilter;

  protected $description;
 
  protected $chainTarget;
  protected $chainUrl; 

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    if ($name === null ) throw new \Exception('Form field need to have name');

    if ($id === null) $id = $name;
    if ($label === null) $label = ucfirst($name);
    
    $this->attributes = array(
      'name' => $name,
      'id' => $id,
      'label' => $label,
    );

    if (is_array($extra) && count($extra) > 0) {
      $this->attributes = array_merge($this->attributes, $extra);
    }
 
    $this->startTag = '<input ';
    $this->closeStartTag = '';
    $this->endTag = '>';
    $this->errorText = '';

    $this->useInnerValue = false;
    $this->useOptions = false;
  }

  public function useOptions()
  {
    return $this->useOptions;
  }

  public function setAttribute($key, $val)
  {
    $this->attributes[$key] = $val;
  }

  public function getAttribute($key)
  {
    if (isset($this->attributes[$key])) return $this->attributes[$key];
    return null;
  }

  public function setValue($value)
  {
    $this->attributes['value'] = $value;
  }

  public function setLabel($value)
  {
    $this->attributes['label'] = $value;
  }

  public function setType($value)
  {
    $this->attributes['type'] = $value;
  }

  public function getName()
  {
    return $this->getAttribute('name');
  }

  public function getValue()
  {
    return $this->getAttribute('value');
  }

  public function getLabel()
  {
    $lb = '';
    if (($lb = $this->getAttribute('label')) !== null) {
      $lb = ucwords(str_replace('_', ' ', $lb));
    }
    return $lb;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function setDescription($value)
  {
    return $this->description = $value;
  }

  public function printAttributes()
  {
    $s = '';
    foreach ($this->attributes as $k => $v) {
      if (($this->useInnerValue && $k == 'value') || $k=='label') continue; 
      $s .= ' ' . $k . '="' . $v .'"';
    }
    return $s;
  }

  public function getLabelTag($labelClass='control-label')
  {
    $nm = $this->attributes['name'];
    return '<label class="'.$labelClass.'" for="'.$nm.'">'.$this->getLabel().'</label>';
  } 

  public function setChainTarget($target, $url)
  {
    $this->chainTarget = $target;
    $this->chainUrl = $url;
  }
  
  public function __toString()
  {
    $s = '';

    $s .= $this->startTag;
    $s .= $this->printAttributes();
    $s .= $this->closeStartTag;
    if ($this->useInnerValue && ($val = $this->getInnerValue()) !== null) $s .= $val;
    $s .= $this->endTag;
    $s .= "\n";

    if ($this->chainTarget !== false && $this->chainTarget != '') {
      $id = $this->getAttribute('id');
      $targetId = $this->chainTarget;
      $targetUrl = $this->chainUrl;

      // FIXME: this works, but we need to work-out how to send the value in "query" params
      $s .= "<script type=\"text/javascript\">
              $(document).ready(function() { 
                var origElem = $('#".$id."');
                var actualElem = $('#".$id."_actual');
                var elemObj = actualElem.length > 0 ? actualElem : origElem;
                elemObj.selectChain({ target: $('#".$targetId."'), url: '".$targetUrl."', type: 'post', data: {query : 'query'} });
              });
            </script>";
    }
    return $s;
  }

  public function getErrorText()
  {
    return $this->errorText;
  }

  public function setErrorText($error)
  {
    $this->errorText = $error;
  }

  public function setAccessFilter($value)
  {
    $this->accessFilter = $value;
  }

}