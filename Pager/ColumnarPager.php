<?php

namespace PetakUmpet\Pager;

use PetakUmpet\Request;
use PetakUmpet\Pager;

class ColumnarPager extends QueryPager {

  protected $id;

  public function __construct(Request $request, $pagerRows=null)
  {
    parent::__construct($request, 1);
    $this->tableClass = '';
  }

  public function __toString()
  {
    if (count($this->pagerData) <= 0 || count($this->pagerData[0]) <= 0)
      return 'No data found';

    $s = '<table class="'.$this->tableClass.'">';

     $cnt = 1;
    foreach ($this->pagerData as $d) {
      if ($cnt > $this->pagerRows) break;
      if (is_array($this->header)) {
        foreach ($this->header as $h) {
          if ($h == 'id') continue;
          $val = '';
          if (isset($d[$h])) {
            $val = $this->formatValue($d[$h]);
          }
          $h = strtoupper($h);
          $s .= "<tr><th align='left'>$h</th><td>$val</td></tr>";
        }
        if ($this->readOnly === false) $s .= $this->rowCallback($d);

      } else {
        return 'Pager not properly setup, missing header.';
      }
      $cnt++;
    }
    $s .= '</table>';

    if ($this->needPager()) {
      $s .= $this->formatPager();
    }

    return $s;
  }
}