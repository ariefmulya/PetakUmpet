<?php

namespace PetakUmpet\Form\Field;

class Price extends BaseField {

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
    $value = str_replace(",", "", $value);
    $value = str_replace(".", "", $value);

    return $value;
  }

  public function __toString()
  {
    $s  = '<div class="input-prepend">';
    $s .= '<span class="add-on">' . $this->currencyTag . '</span>';
    $s .= parent::__toString();
    $s .= '</div>';

    return $s;
  }

}