<?php

namespace PetakUmpet\Database\Driver;

class PostgreSQL {

  private $db;

  function getTableSchemaQuery()
  {
    // PostgreSQL 9 query to get column details
    // might also worked in 8 and above
    return  "SELECT a.attnum as colnum, a.attname as column, c.typname as type, "
        . "a.atttypmod as maxlen, a.attlen as varlen, "
        . "case when d.contype ='p' then 1 else 0 end as primary, "
        . "case when a.attnotnull ='t' then 1 else 0 end as notnull "
        . "FROM pg_attribute a JOIN pg_class b ON a.attrelid=b.oid "
        . "JOIN pg_type c ON a.atttypid = c.oid "
        . "LEFT JOIN pg_constraint d on a.attrelid = d.conrelid AND a.attnum = ANY(d.conkey) "
        . "WHERE b.relname = ? AND a.attnum > 0 AND a.attisdropped = false ORDER BY a.attnum " ;
  }

  function getForeignKeyQuery()
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

  function setDbo($db)
  {
    $this->db = $db;
  }

  function generateDSN($host, $dbname, $extra=null)
  {
    if ($dbname===null) $dbname='template1';
    
    return 'pgsql:host='.$host.';dbname='.$dbname . ($extra==null ? '' : ';' . $extra);
  }

  function generateLimit($query, $limit, $offset=null)
  {
    $s = $query . " LIMIT " . $this->db->escapeInput((string) $limit) ;
    if ($offset !== null && $offset != 0) 
      $s .= " OFFSET " . $this->db->escapeInput((string) $offset);

    return $s;
  }

  function getLastIdQuery($idCol = 'id')
  {
    return " RETURNING " . $this->db->escapeInput($idCol) ;
  }

  public function getColumnQuote()
  {
    return '"';
  }
}