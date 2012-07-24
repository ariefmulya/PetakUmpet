<?php

namespace PetakUmpet;

use PetakUmpet\Form\Text;

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

  function bind(Request $request)
  {
    if (($v = $request->getSearch())) {
      $this->input->setValue($v);
    }
  }
}