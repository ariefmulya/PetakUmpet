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
      $colName   = $s[Schema::SC_COLNAME];
      $colType   = $s[Schema::SC_COLTYPE];
      $pdoType   = $db->getDriver()->getPdoTypeMap($colType);
      $fieldType = $db->getDriver()->getFormFieldTypeMap($colType);
      $isString  = ($pdoType == \PDO::PARAM_STR  
                      && !(stristr($colType, 'time')) 
                      && !(stristr($colType, 'date')) );

      if ($s[Schema::SC_PRIMARY]) {
        $schema->pkeys[] = $colName;
      }

      $schema->columns[] = $colName;
      $schema->types[$colName] = $colType;
      $schema->pdoTypes[$colName] = $pdoType;
      $schema->isString[$colName] = $isString;

      $s[Schema::SC_PDOTYPE] = $pdoType;
      $s[Schema::SC_FFIELDTYPE] = $fieldType;
      $s[Schema::SC_ISSTRING] = $isString;
      $schema->detail[$colName] = $s;
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