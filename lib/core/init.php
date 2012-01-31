<?php

include_once(PU_CWD . 'slib/util.php');
include_once(PU_CWD . 'slib/log.php');
include_once(PU_CWD . 'slib/db.php');
include_once(PU_CWD . 'slib/dbm.php');
include_once(PU_CWD . 'slib/form.php');
include_once(PU_CWD . 'slib/request.php');
include_once(PU_CWD . 'slib/view.php');
include_once(PU_CWD . 'slib/controller.php');
include_once(PU_CWD . 'slib/crud.php');
include_once(PU_CWD . 'slib/mailer.php');


function pu_main()
{
  $db      = db_init();
  $request = request_init();
  $view    = view_init();

  if (!$db || !$view)  {
    log_debug('ctrl_execute_action(): Initialization failed');

    // FIXME: Add mechanism for error handling, system and module errors
    return;
  }
  
  return ctrl_start_engine($db, $request, $view);
}


