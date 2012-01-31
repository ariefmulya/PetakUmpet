<?php

include_once(PU_CWD . 'lib/core/util.php');
include_once(PU_CWD . 'lib/core/log.php');
include_once(PU_CWD . 'lib/core/db.php');
include_once(PU_CWD . 'lib/core/dbm.php');
include_once(PU_CWD . 'lib/core/form.php');
include_once(PU_CWD . 'lib/core/request.php');
include_once(PU_CWD . 'lib/core/view.php');
include_once(PU_CWD . 'lib/core/controller.php');
include_once(PU_CWD . 'lib/core/crud.php');
include_once(PU_CWD . 'lib/core/mailer.php');


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


