<?php

function dbm_guess_table_schema($db, $tablename=null, $with_cache=true)
{
  static $model_cache;

  if (!isset($model_cache[$tablename]) || !$with_cache) {
    $res = db_get_table_schema($db, $tablename);

    if (!$res) {
      return false;
    }
    $model_cache[$tablename] = $res->fetchAll();
  }

  return $model_cache[$tablename];
}

function dbm_get_table_pkeys($schema)
{
  $pkeys = array();
  foreach ($schema as $s) {
    if ($s['primary']) {
      $pkeys[] = $s['column'];
    }
  }
  return $pkeys;
}

function dbm_get_row_by_pkeys($db, $tablename, $pkvals)
{
  $table_schema = dbm_guess_table_schema($db, $tablename);

  $pkeys = dbm_get_table_pkeys($table_schema);

  if (count($pkeys) <= 0) {
    return false;
  }

  foreach($pkeys as $k) {
    $marker[] = '?';
  }
  
  $query = "SELECT * FROM %s WHERE (" . implode(', ', $pkeys) . ") = (" . implode(', ', $marker). ") ; ";

  // FIXME: can we turn this into prepared statement?
  $query = sprintf($query, $tablename);

  $res = db_prepared_query($db, $query, (array) $pkvals);

  if (!$res) {
    return false;
  }

  return $res->fetch();
}

function dbm_get_modified_cols($schema, $values)
{
  $columns = $new_values = $pkeys = $pkvals = array() ;

  foreach ($schema as $c) {
    $col = $c['column'];

    if (isset($values[$col])) {
      if ($c['primary']) {
        $pkeys[] = $col;
        $pkvals[] = $values[$col];
      } else {
        $columns[] = $col;
        $new_values[$col] = $values[$col];
      }
    }
  }

  return array($columns, $new_values, $pkeys, $pkvals);
}

function dbm_addedit_row($db, $tablename, $values)
{
  $table_schema = dbm_guess_table_schema($db, $tablename);

  list($columns, $new_values, $pkeys, $pkvals) = dbm_get_modified_cols($table_schema, $values);

  $query = '';
  $params = array();

  if (count($new_values) > 0) {
    if (count($pkeys) == 0 || !dbm_get_row_by_pkeys($db, $tablename, $pkvals)) {
      // INSERT
      //
      foreach($new_values as $nv) {
        $marker[] = '?';
      }
      $query = sprintf('INSERT INTO %s ('.implode(', ', $columns).') VALUES ('.implode(', ', $marker).');', $tablename);
      $params = array_values($new_values);
    } else {
      // UPDATE 
      $start = sprintf('UPDATE %s SET ', $tablename);

      $n = 0;
      $set = '';
      foreach ($columns as $c) {
        $set .= ($n == 1 ? ', ' : '') . $c . " = ? ";
        $n = 1;
      }

      foreach($pkvals as $nv) {
        $marker[] = '?';
      }

      $end = " WHERE (".implode(', ', $pkeys).") =  (".implode(', ', $marker).") ; ";

      $query = $start . $set . $end;
      $params = array_values(array_merge($new_values, $pkvals));
    }
  } else {
    // FIXME: Add for case where there is no primary key
  }

  return db_prepared_query($db, $query, $params);
}

function dbm_select_one_row($db, $tablename, $pkvals=array(), $pkeys=array('id'))
{
  // FIXME: get limit from db layer
  $query  = sprintf("SELECT * FROM %s ", $tablename);
  $query .= " WHERE (".implode(', ', $pkeys).") =  (".implode(', ', $pkvals).") LIMIT 1 ; ";

  return db_query_fetch_row($db, $query);
}

function dbm_select_paging(&$db, &$request, $tablename, $columns=array(), $filter='', $page=1, $rows=10)
{
  if (!isset($request['page'])) {
    $request['page'] = $page;
  }

  $offset = $rows * $page - $rows; 

  $show_columns = '*';
  $order_by = 'id'; // FIXME: need more clever way to figure out default sort

  if (is_array($columns) && count($columns) > 0) {
    $order_by = $columns[0];
    $show_columns = implode(',' , $columns);
  }

  $count_query = sprintf("SELECT COUNT(*) as cnt FROM ( "
    . "SELECT " . $show_columns . " FROM %s " 
    . "$filter "
    . ") src "
    , $tablename
  );
  $cnt = db_query_fetch_one($db, $count_query);

  // FIXME: Here we assume id is the pkey
  if ($cnt && $cnt > 0) {
    $query = sprintf("SELECT * FROM ( "
      . "SELECT ".$show_columns." FROM %s "
      . "$filter "
      . ") src ORDER BY ".$order_by." LIMIT %d OFFSET %d "
      , $tablename
      , $rows
      , $offset
    ); 
    return array($cnt, db_query_fetch_all($db, $query));
  }

  return false;
}

/* 
 * create a form for a table based on its schema 
 *
 * fields are structured based on the column types and definition
 *
 * TODO: implement validation rules
 *
 */
function dbm_create_form_for_table($db, $tablename, $custom=array())
{
  $schema = dbm_guess_table_schema($db, $tablename);

  if (is_array($schema) && count($schema) > 0) {
    $fields = array();
    foreach ($schema as $s) {
      if ($s['primary']) { // primary key
        $fields[$s['column']]['type'] = 'hidden';
      } else if ($s['type'] == 'text') {
        $fields[$s['column']]['type'] = 'textarea';
        $fields[$s['column']]['advance'] = true;
      } else {
        $fields[$s['column']]['type'] = 'input';
      }
    }
    $form = form_create($tablename, $fields, $custom);
    return $form;
  }
  return false;
}
