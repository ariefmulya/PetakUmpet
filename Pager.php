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
  protected $inlineForm;

  protected $tableClass;

  protected $pagerRows;
  protected $pagerData;

  protected $header;
  protected $footer;

  protected $page;
  protected $totalRows;
  protected $totalPage;

  protected $filter;

  protected $actions;

  protected $editAction;
  protected $cancelAction;
  protected $deleteAction;

  protected $readOnly;
  protected $useAjax;

  public function __construct(Request $request, $pagerRows=10)
  {
    $this->pagerRows = $pagerRows === null ? 10 : $pagerRows;
    $this->pagerData = array();
    $this->header = null;
    $this->footer = null;
    $this->page = $request->get('page', 1);
    $this->total = null;

    $this->tableClass = "table table-condensed table-bordered table-striped table-hover";

    $this->nLinksBeforeAfter = 1;
    $this->minDistance = 5;

    $this->readOnly = false;
    $this->targetDiv = 'pager';
    $this->inlineForm = false;
    $this->useAjax = true;

    // extra actions goes here
    $this->actions = array();

    $url = $request->getFullUrl();

    if (!strstr($url, '?')) $url .= '?paging=paging';

    $this->pagerAction = preg_replace('/&page=[0-9]+/', '', $url);
  }

  public function useAjax($value)
  {
    $this->useAjax = $value;
  }

  public function setTableClass($value)
  {
    $this->tableClass = $value;
  }

  public function __toString()
  {
    if (count($this->pagerData) <= 0 || count($this->pagerData[0]) <= 0)
      return 'No data found';

    $s = '<table class="'.$this->tableClass.'">';

    if (is_array($this->header)) {
      $s .= '<thead><tr>';

      // show header for number
      $s .= '<th>NO</th>';

      $s .= $this->formatTableRow($this->header, 'th', self::FORMAT_UCASE);
      if ($this->readOnly === false)  $s .= $this->headerCallback($this->header);
      $s .= '</tr></thead>';
    }

    $s .= '<tbody>';
    $cnt = 1;
    foreach ($this->pagerData as $d) {
      $align = '';
      if ($cnt > $this->pagerRows) break;
      if (is_array($this->header)) {

        $row_id = (isset($d['id']))?$d['id']:$cnt;
        $s .= '<tr id="row_'.$row_id.'" alt="'.$row_id.'">';

        // just a running number
        $s .= '<td>' . ((($this->page-1)*$this->pagerRows) + $cnt) . '</td>';

        foreach ($this->header as $h) {
          $align = '';
          if ($h == 'id') continue;
          $val = '';
          if (is_numeric($d[$h])) $align= 'align="right"';
          if (isset($d[$h])) {
            $val = $this->formatValue($d[$h]);
          }
          $s .= '<td '.$align.'>'.$val.'</td>';
        }
        if ($this->readOnly === false) $s .= $this->rowCallback($d);
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

  public function isBuilt()
  {
    return (count($this->pagerData) > 0);
  }

  public function headerCallback($rowData)
  { 
    // If needed child class can implement this to add more columns
    return;
  }

  public function rowCallback($rowData)
  {
    // If needed child class can implement this to add more columns
    return;
  }

  public function formatValue($value)
  {
    if (is_bool($value)) {
      return ($value ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>');
    }
    // } else if (is_numeric($value)) {
    //   $value = number_format($value, 2);
    // }

    return $value;
  }

  public function formatTableRow($data, $coltype='td', $format = self::FORMAT_NONE) 
  {
    $s = '';
    foreach ($data as $d) {
      $align = '';
      if ($d == 'id') continue;
      $d = str_replace('_', ' ', $d);
      switch ($format) {
        case self::FORMAT_UCASE:
          $d = strtoupper($d);
          break;
      }
      if (is_numeric($d)) $align= 'align="right"';
      $s .= '<'. $coltype . ' '.$align.'>' . $this->formatValue($d) . '</' . $coltype . '>'; 
    }

    return $s;
  }

  public function needPager()
  {
    return ($this->totalPage > 1);
  }

  private function pageLink($id, $mode='' /* active, disabled */)
  {
    $page = $id;
    if ($id == 'Next') $page = min($this->page + 1, $this->totalPage);
    else if ($id == 'Prev') $page = max($this->page - 1, 1);

    if ($id == $this->page) $class = 'class="active"';
    else if ($id == '...') $class = 'class="disabled"';
    else $class = $mode == '' ? '' : 'class="'.$mode.'"';

    $link = '<li ' . $class . '><a href="#" onclick="$(\'#'. $this->targetDiv . '\').load(\''.$this->pagerAction.'&page='.$page.'\');" >'.$id.'</a></li>';

    if (!$this->useAjax) {
      $link = '<td>' . 
                ($id == $this->page || ($id == 'Prev' && $this->page == 1) || ($id == 'Next' && $this->page == $this->totalPage) ? '' : '<a href="'.$this->pagerAction.'&page='.$page.'" >') .
                $id .
                ($id == $this->page || ($id == 'Prev' && $this->page == 1) || ($id == 'Next' && $this->page == $this->totalPage) ? '' : '</a>') .
                '</td>';
    }

    return $link;
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

  /* some helper setter and getter for child classes */

  public function setReadOnly($state=true)
  {
    $this->readOnly = $state;
  }

  public function getReadOnly()
  {
    return $this->readOnly;
  }

  public function setFilter($filter)
  {
    $this->filter = $filter;
  }

  public function setHeader($data)
  {
    $this->header = $data;
  }

  public function setPagerData($data)
  {
    $this->pagerData = $data;
  }
  
  public function setTargetDiv($target)
  {
    $this->targetDiv = $target;
  }

  public function getInlineForm()
  {
    return $this->inlineForm;
  }

  public function setInlineForm($state=true)
  {
    $this->inlineForm = $state;
  }

  public function setEditAction($action)
  {
    $this->editAction = $action;
  }

  public function setDeleteAction($action)
  {
    $this->deleteAction = $action;
  }
  
  public function setPagerAction($action)
  {
    $this->pagerAction = $action;
  }

  public function addAction($name, $act)
  {
    $this->actions[$name] = $act;
  }
}
