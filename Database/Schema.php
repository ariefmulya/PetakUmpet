<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

class Schema {

  const SC_COLNUM  = 'colnum';
  const SC_COLNAME = 'column';
  const SC_COLTYPE = 'type';
  const SC_MAXLEN  = 'maxlen';
  const SC_VARLEN  = 'varlen';
  const SC_PRIMARY = 'primary';
  const SC_NOTNULL = 'notnull';

  const FK_SRCTABLE = 'srctable';
  const FK_SRCID    = 'srcid';
  const FK_DSTTABLE = 'dsttable';
  const FK_DSTID    = 'dstid';

  const COL_TYPE = 'type';
  const COL_IS_STRING = 'string';

  private $db;
 
  private $tableName;

  private $mc;           // schema model cache
  private $columnNames;  // column names
  private $colTypeMap;   // column type maps SQL <=> php-types
  private $pkeys;        // primary keys
  private $fkeys;        // foreign keys

  function __construct($tableName, $db=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->tableName = $db->escapeInput($tableName);

    $this->db  = $db;

    $this->coltypeMap = array(
        'varchar' => 'string',
        'text' => 'string',
        'char' => 'string',
       /* TODO: complete with most used column type maps */ 
      );

    $this->build();
  }

  private function build()
  {
    $db =& $this->db;

    $res = $db->queryFetchAll($db->getDriver()->getTableSchemaQuery(), array($this->tableName));

    if (!$res) throw new \Exception('Building database schema failed');

    $mc = array();

    foreach ($res as $s) {
      if ($s[self::SC_PRIMARY]) {
        $this->pkeys[] = $s[self::SC_COLNAME];
      }
      $this->columnNames[] = $s[self::SC_COLNAME];

      $isString = ($this->mapColumnType($s[self::SC_COLTYPE]) == 'string');

      $s[self::COL_IS_STRING] = $isString; 
      $mc[$s[self::SC_COLNAME]] = $s;
    }

    $this->mc = $mc;

    // get foreign keys
    $fdata = $db->queryFetchAll($db->getDriver()->getForeignKeyQuery(), array($this->tableName));
    if ($fdata) {
      foreach ($fdata as $d) {
        $this->fkeys[$d[self::FK_SRCID]] = $d;
      }
    }
  }

  public function get()
  {
    return $this->mc;
  }

  public function getPK()
  {
    return $this->pkeys;
  }

  public function getFK()
  {
    return $this->fkeys;
  }

  public function getColumnNames()
  {
    return $this->columnNames;
  }

  public function getColumnRelation($colName)
  {
    if (isset($this->fkeys[$colName])) {
      return $this->fkeys[$colName];
    }
    return null;
  }

  public function mapColumnType($type)
  {
    if (isset($this->colTypeMap[$type])) {
      return $this->colTypeMap[$type];
    }
    return $type;
  }

}