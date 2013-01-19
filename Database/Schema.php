<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

class Schema {

  const SC_COLNUM     = 'colnum';
  const SC_COLNAME    = 'column';
  const SC_COLTYPE    = 'type';
  const SC_MAXLEN     = 'maxlen';
  const SC_VARLEN     = 'varlen';
  const SC_PRIMARY    = 'primary';
  const SC_NOTNULL    = 'notnull';
  const SC_ISSTRING   = 'isstring';
  const SC_ISARRAY    = 'isarray';
  const SC_PDOTYPE    = 'pdotype';
  const SC_FFIELDTYPE = 'fieldtype';

  const FK_SRCTABLE = 'srctable';
  const FK_SRCID    = 'srcid';
  const FK_DSTTABLE = 'dsttable';
  const FK_DSTID    = 'dstid';

  public $pkeys;
  public $fkeys;
  public $columns;
  public $types;
  public $pdoTypes;
  public $detail;
  public $isString;

  public function __construct($tableName, $db=null)
  {
    SchemaBuilder::build($this, $tableName, $db);
  }

  public function getSchemaDetail() { return $this->detail; }

  public function getPK() { return $this->pkeys; }

  public function getFK() { return $this->fkeys; }

  public function getPdoTypes() { return $this->pdoTypes; }

  public function getColumnNames() { return $this->columns; }

  public function getColumnSchema($colName)
  {
    if (isset($this->detail[$colName])) {
      return $this->detail[$colName];
    }
    return null;
  }

  public function getColumnRelation($colName)
  {
    if (isset($this->fkeys[$colName])) {
      return $this->fkeys[$colName];
    }
    return null;
  }

  public function getColumnNativeType($colName)
  {
    if (isset($this->types[$colName])) {
      return $this->types[$colName];
    }
    return null;
  }

  public function getColumnPdoType($colName)
  {
    if (isset($this->pdoTypes[$colName])) {
      return $this->pdoTypes[$colName];
    }
    return null;
  }

  public function isStringColumn($colName)
  {
    if (isset($this->isString[$colName])) {
      return $this->isString[$colName];
    }
    return false;
  }

  public function isArrayColumn($colName) 
  {
    if (isset($this->detail[$colName])) {
      return $this->detail[$colName][self::SC_ISARRAY];
    }
    return false;
  }
  
}