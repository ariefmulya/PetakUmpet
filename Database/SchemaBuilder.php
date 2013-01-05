<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

abstract class SchemaBuilder {

  static public function build($schema, $tableName, $db=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $res = $db->queryFetchAll($db->getDriver()->getTableSchemaQuery(), array($tableName));

    if (!$res) {
      throw new \Exception('Building database schema failed, check if table ' . $tableName . ' existed.');
    }

    foreach ($res as $s) {
      $colName = $s[Schema::SC_COLNAME];
      $colType = $s[Schema::SC_COLTYPE];
      $pdoType = $db->getDriver()->getPdoTypeMap($coltype);

      if ($s[Schema::SC_PRIMARY]) {
        $schema->pkeys[] = $colName;
      }

      $schema->columns[] = $colName;
      $schema->type[$colname] = $colType;
      $schema->pdoType[$colname] = $pdoType;
      $schema->isString[$colName] = ($pdoType == \PDO::PARAM_STR);
      $schema->detail[$colname] = $s;
    }

    // get foreign keys
    $fdata = $db->queryFetchAll($db->getDriver()->getForeignKeyQuery(), array($tableName));
    if ($fdata) {
      foreach ($fdata as $d) {
        $schema->fkeys[$d[Schema::FK_SRCID]] = $d;
      }
    }
  }

}