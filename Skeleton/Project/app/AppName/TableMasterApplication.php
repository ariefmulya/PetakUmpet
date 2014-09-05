<?php

namespace AppName;

use PetakUmpet\Application\MasterDataApplication;

class TableMasterApplication extends MasterDataApplication {

	protected function configure()
	{
    $crudTables = array(
        'roledata' => array('id', 'name'),
        'accessdata' => array('id', 'name'),
      );

    $this->setCrudTables($crudTables);

    $this->setReadonly(array('accessdata'));
	}
}
