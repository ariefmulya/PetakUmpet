<?php

namespace PetakUmpet\Form\Field;

class Typeahead extends BaseField {

  private $url;

  private $actualName;
  private $actualValue;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('text');
    $this->setAttribute('autocomplete', 'off');
    $this->url = '#';
  }

  public function setSourceUrl($url)
  {
    $this->url = $url;
  }

  public function setValue($value)
  {
    parent::setValue($value);
    $this->setActualValue($value);
  }

  private function setActualValue($value)
  {
    $this->actualValue = $value;
  }

  public function __toString()
  {
    $s = parent::__toString();

    // extra script for datepicker field
    $s .= '<script type="text/javascript">
              $(\'#'.$this->getAttribute('id').'\').typeahead( 
                { 
                  source: function(query, process) { 
                    return $.get("'.$this->url.'", {query: query}, 
                      function (data) { 
                        res = $.parseJSON(data);
                        process(res);
                      }
                    );
                  }
                }
              );
          </script>';
    return $s;
  }

}