<?php

namespace PetakUmpet;

class Pager {

  const FORMAT_NONE = 0;
  const FORMAT_UCASE = 1;
  const FORMAT_LCASE = 2;
  const FORMAT_UCFIRST = 4;
  const FORMAT_UCWORDS = 8;


  private $pagerRows;
  private $pagerData;

  private $header;
  private $footer;

  private $page;
  private $total;

  public function __construct($pagerRows=25)
  {
    $this->pagerRows = $pagerRows;
    $this->pagerData = array();
    $this->header = null;
    $this->footer = null;
    $this->page = 1;
    $this->total = null;
  }

  public function __toString()
  {
    if (count($this->pagerData) <= 0)
      return 'No data found';

    $s = '<table class="table">';

    if (is_array($this->header)) {
      $s .= '<thead>';
      $s .= $this->formatTableRow($this->header, 'th', self::FORMAT_UCASE);
      $s .= '</thead>';
    }

    $s .= '<tbody>';
    foreach ($this->pagerData as $d) {

      if (is_array($this->header)) {

        $s .= '<tr>';
        foreach ($this->header as $h) {
          if (isset($d[$h])) {
            $val = $d[$h];
            if (is_bool($d[$h])) {
              $val = $d[$h] ? 'true' : 'false';
            }
          }
          $s .= '<td>'.$val.'</td>';
        }
        $s .= '</tr>';

      } else {
        $s .= $this->formatTableRow($d);
      }
    }
    $s .= '</tbody>';

    if (is_array($this->footer)) {
      $s .= '<tfoot>';
      $s .= $this->formatTableRow($this->footer);
      $s .= '</tfoot>';
    }

    $s .= '</table>';
    return $s;
  }

  function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') 
      return $this->get(lcfirst(substr($name, 3)));
    if (substr($name, 0,3) == 'set') 
      return $this->set(lcfirst(substr($name, 3)), $args[0]);
  }

  public function get($name, $default=null)
  {
    if (isset($this->$name))
      return $this->$name;
    return $default;
  }

  public function set($name, $value)
  {
    $this->$name = $value;
  }

  public function formatTableRow($data, $coltype='td', $format = self::FORMAT_NONE) 
  {
    $s = '<tr>';
    foreach ($data as $d) {
      $d = str_replace('_', ' ', $d);
      switch ($format) {
        case self::FORMAT_UCASE:
          $d = strtoupper($d);
          break;
      }
      $s .= '<'. $coltype . '>' . $d . '</' . $coltype . '>'; 
    }
    $s .= '</tr>';

    return $s;
  }

}