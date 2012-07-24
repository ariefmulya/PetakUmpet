<?php

namespace PetakUmpet\Database;

use PetakUmpet\Pager;
use PetakUmpet\Database\Builder;

class TablePager extends Pager {

  private $tableName;
  private $builder;

  public function __construct($tableName, $pagerRows=25)
  {
    parent::__construct($pagerRows);

    $this->tableName = $tableName;
    $this->builder = new Builder($tableName);
    $this->builder->importAll();
  }

  public function build()
  {
    $this->setHeader($this->builder->getColumnNames());
    $this->setPagerData($this->builder->getTableData());
  }
}