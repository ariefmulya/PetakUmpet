<?php

function pu_crud_form_handler(&$db, &$request, &$view, $tablename, $mode, $schema=null, $saving_callback='dbm_addedit_row')
{
  if ($schema === null) {
    $view['form'] = dbm_create_form_for_table($db, $tablename);
  } else {
    $view['form'] = form_create('CrudForm', $schema);
  }

  if (request_is_post()) {
    $a = form_get_values($request);

    form_bind_values($view['form'], $a);

    $ret = call_user_func($saving_callback, $db, $tablename, $a);

    if (!$ret) {
      view_set_db_error($view, $db);
      return false;
    } 
    view_set_flash('Successfully saved into table `'. $tablename .'`');
  } else if ($mode == 'edit') {
    $a = dbm_select_one_row($db, $tablename, array(request_getvar_or_404($request, 'i')));
    form_bind_values($view['form'], $a);
  }

  return true;
}

function pu_crud_request_handler(&$db, &$request, &$view, $tablename, $schema=null, $saving_callback='dbm_addedit_row', $list_columns=array())
{
  $mode = request_getvar($request, 'o');
  $view['tablename'] = $tablename;
  $view['mode'] = $mode;

  if ($mode == 'add' || $mode == 'edit') {
    if (!pu_crud_form_handler($db, $request, $view, $tablename, $mode, $schema, $saving_callback)) {
      return 'stpl_crud_error_addedit';
    }
    return 'stpl_crud_addedit';

  } else if ($mode == 'del') {

    return 'stpl_crud_del';
  } else { // default, listing
    $row = 2; // FIXME: should be tuneable
    $page = (isset($request['page']) ? $request['page'] : 1);

    list($view['count'], $view['table_' .$tablename]) = dbm_select_paging($db, $request, $tablename, $list_columns, null, $page, $row);
    $view['current_page'] = $page;
    $view['total_page'] = ceil($view['count'] / $row);

    return 'stpl_crud_index';
  }
}

function pu_crud_setup(&$db, &$request, &$view, $config)
{
  if (!isset($config['tablename'])) {
    return false;
  }

  $tablename = $config['tablename'];
  $schema    = (isset($config['schema']) ? $config['schema'] : null);
  $list_columns    = (isset($config['list_columns']) ? $config['list_columns'] : array());
  $saving_callback = (isset($config['saving_callback']) ? $config['saving_callback'] : 'dbm_addedit_row');

  return pu_crud_request_handler($db, $request, $view, $tablename, $schema, $saving_callback, $list_columns);
}
