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

  public function getHeaderKey($key)     { if (isset($this->header[$key])) return $this->header[$key]; }
  public function getReportDataKey($key) { if (isset($this->reportData[$key])) return $this->reportData[$key]; }
  public function getFooterKey($key)     { if (isset($this->footer[$key])) return $this->footer[$key]; }

  public function setTitle($value)      { $this->title = $value; }
  public function setHeader($value)     { $this->header = $value; }
  public function setColumns($value)    { $this->columns = $value; }
  public function setReportData($value) { $this->reportData = $value; }
  public function setFooter($value)     { $this->footer = $value; }
  public function setFormatter($value)  { $this->formatter = $value; }

  public function __toString()
  {
    $cname = '\\PetakUmpet\\Report\\' . $this->formatter;
    $formatter = new $cname($this);
    return (string) $formatter;
  }
}