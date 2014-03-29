<?php

namespace PetakUmpet\Formatter\Report ;

use PetakUmpet\Formatter as Formatter;

class Generic extends Formatter {

  private $report;

  public function __construct(\PetakUmpet\Report $report)
  {
    $this->report = $report;
  }

  public function __toString()
  {
    $report = $this->report;
    $reportData = $report->getReportData();

    $s  = '<div><h2>' . $report->getTitle() . '</h2></div>';

    if (count($report->getHeader() > 0)) {
      $s .= '<div class="row">';
      foreach ($report->getHeader() as $k => $v) {
        $s .= '<p><strong>'.ucwords($k).'</strong>: '.$v.'</p>';
      }
      $s .= '</div>';
    }

    $s .= '<table class="table table-condensed table-striped table-bordered">';
    if (count($report->getColumns() > 0)) {
      $s .= '<thead><tr>';
      foreach ($report->getColumns() as $c) {
        $s .= '<th>' . strtoupper(str_replace('_', ' ', $c)) . '</th>';
      }
      $s .= '</tr></thead>';
    }

    if (count($reportData) > 0) {
      $s .= '<tbody>';
      foreach ($reportData as $d) {
        $s .= '<tr>';
        foreach ($report->getColumns() as $c) {
          $s .= '<td>'.(isset($d[$c]) ? $d[$c] : '').'</td>';;
        }
        $s .= '</tr>';
      }
      $s .= '</tbody>';
    } else {
      $s .= '<tr><td><strong>No data found</strong></td></tr>';
    }
    $s .= '</table>';

    if (count($report->getFooter() > 0)) {
      $s .= '<div>';
      foreach ($report->getFooter() as $k => $v) {
        $s .= '<p class="span2 columns"><strong>'.$k.'</strong>: '.$v.'</p>';
      }
      $s .= '</div>';
    }

    return $s;
  }

}