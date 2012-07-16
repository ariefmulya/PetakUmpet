<?php

namespace PetakUmpet\Form;

class BaseFormField {

  protected $startTag;
  protected $endTag;
  protected $attributes;

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
    $this->endTag = ' />';
  }

  public function __call($name, $arg)
  {
    if (substr($name, 0,3) == 'set')
      return $this->setAttribute(strtolower(substr($name, 3)), $arg);

    if (substr($name, 0, 3) == 'get')
      return $this->getAttribute(strtolower(substr($name, 3)));
  }

  public function setAttribute($key, $val)
  {
    $this->attributes[$key] = $val[0];
  }
  public function getAttribute($key)
  {
    return $this->attributes[$key];
  }

  public function printAttributes()
  {
    $s = '';
    foreach ($this->attributes as $k => $v) {
      $s .= ' ' . $k . '="' . $v .'"';
    }
    return $s;
  }

  public function getLabelTag()
  {
    $s = '';
    if (isset($this->attributes['label']) && $this->attributes['label'] !== null) {
      $nm = $this->attributes['name'];
      $lb = $this->attributes['label'];
      $s  = '<label for="'.$nm.'">'.$lb.'</label>';
    }
    return $s;
  } 
  public function __toString()
  {
    $s = '';

    $s .= $this->startTag;
    $s .= $this->printAttributes();
    $s .= $this->endTag;
    $s .= "\n";
    return $s;
  }

}