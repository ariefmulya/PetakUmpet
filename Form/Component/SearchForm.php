<?php

namespace PetakUmpet\Form\Component;

use PetakUmpet\Form\Field\Text;

class SearchForm {

  protected $name;
  protected $method;
  protected $formClass;
  private $input;

  function __construct($name='Form', $class='well form-search', $method='POST')
  {
    $this->name = $name;
    $this->method = $method;
    $this->formClass = $class;
    $this->input = new Text('search', array('input-medium search-query'));
  }

  function __toString()
  {
    $s = '
    <form class="'.$this->formClass.'" name="'.$this->name.'" method="'.$this->method.'">
    ';
    $s .= (string) $this->input;
    $s .= '
      <button type="submit" class="btn">Search</button>
    </form>
    ';

    return $s;
  }

  function bind(\PetakUmpet\Request $request)
  {
    if (($v = $request->get('search'))) {
      $this->input->setValue($v);
    }
  }

  function getValue()
  {
    return $this->input->getValue(); 
  }

  function setValue($value)
  {
    return $this->input->setValue($value); 
  }
  
}