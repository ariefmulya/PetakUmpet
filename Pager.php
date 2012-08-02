<?php

namespace PetakUmpet;

class Pager {

  const FORMAT_NONE = 0;
  const FORMAT_UCASE = 1;
  const FORMAT_LCASE = 2;
  const FORMAT_UCFIRST = 4;
  const FORMAT_UCWORDS = 8;


  private $nLinksBeforeAfter;
  private $minDistance;

  protected $url;
  protected $targetDiv;

  protected $pagerRows;
  protected $pagerData;

  protected $header;
  protected $footer;

  protected $page;
  protected $totalRows;
  protected $totalPage;

  public function __construct(Request $request, $pagerRows=10)
  {
    $this->pagerRows = $pagerRows;
    $this->pagerData = array();
    $this->header = null;
    $this->footer = null;
    $this->page = $request->get('page', 1);
    $this->total = null;

    $this->nLinksBeforeAfter = 3;
    $this->minDistance = 5;

    $url = $request->getFullUrl();

    if (!strstr($url, '?')) $url .= '?paging=paging';

    $url = preg_replace('/&page=[0-9]+/', '', $url);

    $this->url = $url;
  }

  public function __toString()
  {
    if (count($this->pagerData[0]) <= 0)
      return 'No data found';

    $s = '<table class="table table-condensed table-bordered table-striped">';

    if (is_array($this->header)) {
      $s .= '<thead><tr>';
      $s .= $this->formatTableRow($this->header, 'th', self::FORMAT_UCASE);
      $s .= $this->headerCallback($this->header);
      $s .= '</tr></thead>';
    }

    $s .= '<tbody>';
    $cnt = 1;
    foreach ($this->pagerData as $d) {
      if ($cnt > $this->pagerRows) break;
      if (is_array($this->header)) {

        $s .= '<tr>';
        foreach ($this->header as $h) {
          $val = '';
          if (isset($d[$h])) {
            $val = $this->formatValue($d[$h]);
          }
          $s .= '<td>'.$val.'</td>';
        }
        $s .= $this->rowCallback($d);
        $s .= '</tr>';

      } else {
        $s .= '<tr>';
        $s .= $this->formatTableRow($d);
        $s .= $this->rowCallback($d);
        $s .= '</tr>';
      }
      $cnt++;
    }
    $s .= '</tbody>';

    if (is_array($this->footer)) {
      $s .= '<tfoot>';
      $s .= $this->formatTableRow($this->footer);
      $s .= '</tfoot>';
    }

    $s .= '</table>';

    if ($this->needPager()) {
      $s .= $this->formatPager();
    }

    return $s;
  }

  public function headerCallback($rowData) { throw new \Exception('Need to be implemented in child class'); }
  public function rowCallback($rowData) { throw new \Exception('Need to be implemented in child class'); }

  function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') 
      return $this->get(lcfirst(substr($name, 3)));
    if (substr($name, 0,3) == 'set') 
      return $this->set(lcfirst(substr($name, 3)), $args[0]);
  }

  public function setPagerAction($url)
  {
    $this->url = $url;
  }

  public function setTargetDiv($target)
  {
    $this->targetDiv = $target;
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

  public function formatValue($value)
  {
    if (is_bool($value)) {
      return ($value ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>');
    }
    return $value;
  }

  public function formatTableRow($data, $coltype='td', $format = self::FORMAT_NONE) 
  {
    $s = '';
    foreach ($data as $d) {
      $d = str_replace('_', ' ', $d);
      switch ($format) {
        case self::FORMAT_UCASE:
          $d = strtoupper($d);
          break;
      }
      $s .= '<'. $coltype . '>' . $this->formatValue($d) . '</' . $coltype . '>'; 
    }

    return $s;
  }

  public function needPager()
  {
    return true;
  }

  private function pageLink($id, $mode='' /* active, disabled */)
  {
    $page = $id;
    if ($id == 'Next') $page = min($this->page + 1, $this->totalPage);
    else if ($id == 'Prev') $page = max($this->page - 1, 1);

    if ($id == $this->page) $class = 'class="active"';
    else if ($id == '...') $class = 'class="disabled"';
    else $class = $mode == '' ? '' : 'class="'.$mode.'"';

    return '<li ' . $class . '><a href="#" onclick="$(\'#'. $this->targetDiv . '\').load(\''.$this->url.'&page='.$page.'\');" >'.$id.'</a></li>';
  }

  public function formatPager()
  {
    $s  = '<div class="pagination pagination-centered"><ul>';
    $s .= $this->formatLeftPager();
    $s .= $this->formatRightPager();
    $s .= '</ul></div>';

    return $s;
  }

  public function formatLeftPager()
  {
    assert($this->minDistance > $this->nLinksBeforeAfter);

    if ($this->page == 1) {
      return $this->pageLink('Prev', 'disabled') . $this->pageLink(1);
    }

    $s = $this->pageLink('Prev');

    $distance = $this->page - 1;

    $links = array();

    if ($distance < $this->minDistance) {
      for ($i=1; $i<=$this->page; $i++) {
        $links[] = $i;
      }
    } else {
      $links[] = 1;
      $links[] = '...';

      for ($i = ($this->page - $this->nLinksBeforeAfter); $i <= $this->page; $i++) {
        $links[] = $i;
      }
    }

    foreach ($links as $l) {
      $s .= $this->pageLink($l);
    }

    return $s;
  }

  public function formatRightPager()
  {
    assert($this->minDistance > $this->nLinksBeforeAfter);

    if ($this->page == $this->totalPage) {
      return $this->pageLink('Next', 'disabled'); 
    }

    $distance = $this->totalPage - $this->page;
     
    $links = array();

    if ($distance < $this->minDistance) {
      for ($i=$this->page+1; $i <= $this->totalPage; $i++) {
        $links[] = $i;
      }
    } else {
      for ($i = $this->page+1; $i <= $this->page + $this->nLinksBeforeAfter; $i++) {
        $links[] = $i;
      }
      $links[] = '...';
      $links[] = $this->totalPage;
    }

    $s = '';
    foreach ($links as $l) {
      $s .= $this->pageLink($l);
    }
    $s .= $this->pageLink('Next');

    return $s;
  }

}