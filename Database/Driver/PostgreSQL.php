<?php

namespace PetakUmpet\Database\Driver;

use PetakUmpet\Mapping\PostgreSQL\Type;

class PostgreSQL {

  private $db;
  private $dataTypeMap;
  private $formFieldTypeMap;

  public function __construct()
  {
    $this->dataTypeMap = array(
        'bigint' => \PDO::PARAM_INT,
        'int8' => \PDO::PARAM_INT,
        'bigserial' => \PDO::PARAM_INT,
        'serial8' => \PDO::PARAM_INT,
        'bit' => \PDO::PARAM_INT,
        'bit varying' => \PDO::PARAM_INT,
        'varbit' => \PDO::PARAM_INT,
        'boolean' => \PDO::PARAM_BOOL,
        'bool' => \PDO::PARAM_BOOL,
        'box' => \PDO::PARAM_STR,
        'bytea' => \PDO::PARAM_LOB,
        'character varying' => \PDO::PARAM_STR,
        'varchar' => \PDO::PARAM_STR,
        'character' => \PDO::PARAM_STR,
        'char' => \PDO::PARAM_STR,
        'cidr' => \PDO::PARAM_STR,
        'circle' => \PDO::PARAM_STR,
        'date' => \PDO::PARAM_STR,
        'double precision' => \PDO::PARAM_INT,
        'float8' => \PDO::PARAM_INT,
        'inet' => \PDO::PARAM_STR,
        'integer' => \PDO::PARAM_INT,
        'int' => \PDO::PARAM_INT,
        'int4' => \PDO::PARAM_INT,
        'interval' => \PDO::PARAM_INT,
        'line' => \PDO::PARAM_STR,
        'lseg' => \PDO::PARAM_STR,
        'macaddr' => \PDO::PARAM_STR,
        'money' => \PDO::PARAM_INT,
        'numeric' => \PDO::PARAM_INT,
        'decimal' => \PDO::PARAM_INT,
        'path' => \PDO::PARAM_STR,
        'point' => \PDO::PARAM_STR,
        'polygon' => \PDO::PARAM_STR,
        'real' => \PDO::PARAM_INT,
        'float4' => \PDO::PARAM_INT,
        'smallint' => \PDO::PARAM_INT,
        'int2' => \PDO::PARAM_INT,
        'serial' => \PDO::PARAM_INT,
        'serial4' => \PDO::PARAM_INT,
        'text' => \PDO::PARAM_STR,
        'time' => \PDO::PARAM_STR,
        'time without timezone' => \PDO::PARAM_STR,
        'time with timezone' => \PDO::PARAM_STR,
        'timestamp' => \PDO::PARAM_STR,
        'timestamp without timezone' => \PDO::PARAM_STR,
        'timestamp with timezone' => \PDO::PARAM_STR, 
        'tsquery' => \PDO::PARAM_STR,
        'tsvector' => \PDO::PARAM_STR,
        'txid_snapshot' => \PDO::PARAM_STR,
        'uuid' => \PDO::PARAM_STR,
        'xml' => \PDO::PARAM_STR,
        'json' => \PDO::PARAM_STR,
      );

    $this->formFieldTypeMap = array(
        'text' => 'textarea',
        'serial' => 'hidden',
        'bigserial' => 'hidden',
        'serial8' => 'hidden',
        'serial' => 'hidden',
        'serial4' => 'hidden',
        'boolean' => 'radioGroup',
        'bool' => 'radioGroup',
        'money' => 'price',
        'numeric' => 'number',
        'decimal' => 'number',
        'date' => 'date',
        'timestamp' => 'dateTime',
        'time' => 'dateTime',
        'time without timezone' => 'dateTime',
        'time with timezone' => 'dateTime',
        'timestamp' => 'dateTime',
        'timestamp without timezone' => 'dateTime',
        'timestamp with timezone' => 'dateTime', 
      );
  }

  public function getTableSchemaQuery()
  {
    // PostgreSQL 9 query to get column details
    // might also worked in 8 and above
    return  "SELECT a.attnum as colnum, a.attname as column, c.typname as type, "
        . "a.atttypmod as maxlen, a.attlen as varlen, "
        . "case when a.attndims <> 0 then 1 else 0 end as isarray, "
        . "case when d.contype ='p' then 1 else 0 end as primary, "
        . "case when a.attnotnull ='t' then 1 else 0 end as notnull "
        . "FROM pg_attribute a JOIN pg_class b ON a.attrelid=b.oid "
        . "JOIN pg_type c ON a.atttypid = c.oid "
        . "LEFT JOIN pg_constraint d on a.attrelid = d.conrelid AND a.attnum = ANY(d.conkey) "
        . "WHERE b.relname = ? AND a.attnum > 0 AND a.attisdropped = false ORDER BY a.attnum " ;
  }

  public function getForeignKeyQuery()
  {
    return  "SELECT a.conname, b.relname AS srctable, c.attname as srcid, "
          . "d.relname AS dsttable, e.attname AS dstid "
          . "FROM "
          . "( "
          . "  SELECT conname, confrelid, conrelid, "
          . "     unnest(r.conkey) as ccol, unnest(r.confkey) as pcol "
          . "  FROM pg_catalog.pg_constraint r WHERE contype = 'f' "
          . ") a "
          . "JOIN pg_class b ON a.conrelid = b.oid "
          . "JOIN pg_attribute c ON a.conrelid = c.attrelid AND a.ccol = c.attnum "
          . "JOIN pg_class d ON a.confrelid = d.oid "
          . "JOIN pg_attribute e ON a.confrelid = e.attrelid AND a.pcol = e.attnum "
          . "WHERE b.relname = ?" ;
  }

  public function setDbo($db)
  {
    $this->db = $db;
  }

  public function generateDSN($host, $dbname, $extra=null)
  {
    if ($dbname===null) $dbname='template1';
    
    return 'pgsql:host='.$host.';dbname='.$dbname . ($extra==null ? '' : ';' . $extra);
  }

  public function generateLimit($query, $limit, $offset=null)
  {
    $s = $query . " LIMIT " . $this->db->escapeInput((string) $limit) ;
    if ($offset !== null && $offset != 0) 
      $s .= " OFFSET " . $this->db->escapeInput((string) $offset);

    return $s;
  }

  public function getLastIdQuery($idCol = 'id')
  {
    return " RETURNING " . $this->db->escapeInput($idCol) ;
  }

  public function getColumnQuote()
  {
    return '"';
  }

  public function getPdoTypeMap($sqlType)
  {
    if (isset($this->dataTypeMap[$sqlType])) 
      return $this->dataTypeMap[$sqlType];
    return \PDO::PARAM_STR;
  }

  public function getFormFieldTypeMap($type)
  {
    if (isset($this->formFieldTypeMap[$type])) 
      return $this->formFieldTypeMap[$type];
    return 'text';
  }

  public function convertArray($value)
  {
    // taken from: http://stackoverflow.com/questions/5631387/php-array-to-postgres-array
    // XXX: verify if this works
    settype($set, 'array'); // can be called with a scalar or array
    $result = array();
    foreach ($set as $t) {
        if (is_array($t)) {
            $result[] = to_pg_array($t);
        } else {
            $t = str_replace('"', '\\"', $t); // escape double quote
            if (! is_numeric($t)) // quote only non-numeric values
                $t = '"' . $t . '"';
            $result[] = $t;
        }
    }
    return '{' . implode(",", $result) . '}'; // format  
  }
}