<?php

namespace PetakUmpet;

class PostgreSQLDatabase {

  private $db;

  private function getQueryForTableSchema()
  {
    // PostgreSQL 9 query to get column details
    // might also worked in 8 and above
    return  "SELECT a.attname as column, c.typname as type, "
        . "a.atttypmod as maxlen, a.attlen as varlen, c.typbyval as datalen, case when d.contype ='p' then 1 else 0 end as primary "
        . "FROM pg_attribute a JOIN pg_class b ON a.attrelid=b.oid "
        . "JOIN pg_type c ON a.atttypid = c.oid "
        . "LEFT JOIN pg_constraint d on a.attrelid = d.conrelid AND a.attnum = ANY(d.conkey) "
        . "WHERE b.relname = ? AND a.attnum > 0 AND a.attisdropped = false" ;
  }

  function setDbo($db)
  {
    $this->db = $db;
  }

  function getTableSchema($tablename)
  {
    return $this->db->preparedQuery($this->getQueryForTableSchema(), array($tablename));
  }

  function generateDSN($host, $dbname, $extra=null)
  {
    return 'pgsql:host='.$host.';dbname='.$dbname . ($extra==null ? '' : ';' . $extra);
  }
}