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

  public function __toString()
  {
    $s  = '<div class="input-prepend">';
    $s .= '<span class="add-on">' . $this->currencyTag . '</span>';
    $s .= parent::__toString();
    $s .= '</div>';

    // extra script for datepicker field
    $s .= '<script type="text/javascript">$(\'#'.$this->getAttribute('id').'\').priceFormat({prefix: "", centsSeparator: "", centsLimit: 0 });</script>';
    return $s;
  }

}