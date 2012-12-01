<?php

namespace PetakUmpet\Form\Field;

class Typeahead extends BaseField {

  private $url;

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
    $this->actualValue = $value;
  }

  public function setActualValue($value)
  {
    $this->actualValue = $value;
  }

  public function getValue()
  {
    return $this->actualValue;
  }

  public function __toString()
  {
    $s = parent::__toString();

    // extra script for datepicker field
    $s .= '<input type="hidden" value="'.$this->actualValue.'" name="'.$this->getAttribute('name').'_actual" id="'.$this->getAttribute('id').'_actual">'
        . '<script type="text/javascript">
              var labels, mapped;
              $(\'#'.$this->getAttribute('id').'\').typeahead( 
                { 
                  source: function(query, process) { 
                    return $.get("'.$this->url.'", {query: query}, 
                      function (data) { 
                        labels = [];
                        mapped = {};

                        data = $.parseJSON(data);

                        $.each(data, function (i, item) {
                          mapped[item.label] = { id: item.id, label: item.label } ;
                          labels.push(item.label);
                        });
                        process(labels);
                      }
                    );
                  },
                  updater: function (label) {
                    var selObj = mapped[label];
                    $("#'.$this->getAttribute('id').'_actual").val(selObj.id);
                    $("#'.$this->getAttribute('id').'_actual").trigger("change");
                    return selObj.label;
                  }
                }
              );
          </script>';
    return $s;
  }

}