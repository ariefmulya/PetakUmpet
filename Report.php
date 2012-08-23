<?php

namespace PetakUmpet;

class Report {

  private $title;
  private $header;
  private $columns;
  private $reportData;
  private $footer;

  private $formatter;

  public function __construct($title, $formatter='BaseFormatter')
  { 
    $this->title = $title;
    $this->header = array();
    $this->columns = array();
    $this->reportData = array();
    $this->footer = array();
    $this->formatter = $formatter;
  }

  public function getTitle()      { return $this->title; }
  public function getHeader()     { return $this->header; }
  public function getColumns()    { return $this->columns; }
  public function getReportData() { return $this->reportData; }
  public function getFooter()     { return $this->footer; }

  public function __toString()
  {
    $cname = '\\PetakUmpet\\Report\\' . $this->formatter;
    $formatter = new $cname($this);
    return (string) $formatter;
  }
}