<?php

namespace PetakUmpet\Form\Field;

class Number extends TagInput {

  private $currencyTag;
  private $currency;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('text');

    $this->currencyTag = 'Rp.';
    $this->currency = 'IDR';
    $this->setAttribute('style', 'text-align: right;');
    $this->setAttribute('class', 'input-medium');
    $this->setAttribute('data-price', 'true');
  }

  public function setCurrencyTag($value)
  {
    $this->currencyTag = $value;
  }

  public function setCurrency($value)
  {
    $this->currency = $value;
  }

  public function getValue()
  {
    $value = $this->getAttribute('value');
    return $value;
  }

}