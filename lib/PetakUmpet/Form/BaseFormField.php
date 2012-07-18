<?php

namespace PetakUmpet\Form;

class BaseFormField {

  protected $startTag;
  protected $closeStartTag;
  protected $endTag;
  protected $attributes;
  protected $useInnerValue;
  protected $errorText;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    if ($name === null ) throw new \Exception('TextboxField need to have name');

    if ($id === null) $id = $name;
    if ($label === null) $label = ucfirst($name);
    
    $this->attributes = array(
      'name' => $name,
      'id' => $id,
      'label' => $label,
    );

    if (is_array($extra) && count($extra) > 0) {
      $this->attributes = array_merge($this->attributes, $extra);

    } else if ($extra !== null) {
      $params = explode('=', $extra);
      if (is_array($params) && count($params) > 0) {
        $this->attributes[$params[0]] = isset($params[1]) ? $params[1] : "true";
      }

    }
 
    $this->startTag = '<input ';
    $this->closeStartTag = '';
    $this->endTag = '>';
    $this->useInnerValue = false;
    $this->errorText = '';
  }

  public function __call($name, $arg)
  {
    if (substr($name, 0,3) == 'set')
      return $this->setAttribute(strtolower(substr($name, 3)), $arg);

    if (substr($name, 0, 3) == 'get' || substr($name, 0, 2) == 'is')
      return $this->getAttribute(strtolower(substr($name, 3)));
  }

  public function setAttribute($key, $val)
  {
    $this->attributes[$key] = $val[0];
  }
  public function getAttribute($key)
  {
    if (isset($this->attributes[$key])) return $this->attributes[$key];
    return null;
  }

  public function printAttributes()
  {
    $s = '';
    foreach ($this->attributes as $k => $v) {
      if ($this->useInnerValue && $k == 'value') continue; 
      $s .= ' ' . $k . '="' . $v .'"';
    }
    return $s;
  }

  public function getLabelTag($labelClass='label')
  {
    $s = '';
    if (($lb = $this->getAttribute('label')) !== null) {
      $nm = $this->attributes['name'];
      $s  = '<label class="'.$labelClass.'" for="'.$nm.'">'.$lb.'</label>';
    }
    return $s;
  } 
  
  public function __toString()
  {
    $s = '';

    $s .= $this->startTag;
    $s .= $this->printAttributes();
    $s .= $this->closeStartTag;
    if ($this->useInnerValue && ($val = $this->getAttribute('value')) !== null) $s .= $val;
    $s .= $this->endTag;
    $s .= "\n";
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

}