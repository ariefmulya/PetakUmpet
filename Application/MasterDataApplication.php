<?php

namespace PetakUmpet\Application;

use PetakUmpet\Application;
use PetakUmpet\Filter;


abstract class MasterDataApplication extends Application {
  
  private $ajaxCrudApps;

  private $formTypes;
  private $formOptions;

  protected $user;

  public function __construct(\PetakUmpet\Process $process, \PetakUmpet\Request $request, \PetakUmpet\Session $session, \PetakUmpet\Config $config)
  {
    parent::__construct($process, $request, $session, $config); 
    $this->ajaxCrudApps = array();

    $this->formTypes = array();
    $this->formOptions = array();
    
    $this->user = $this->session->getUser();

    $this->configure();
  }

  /* to be called in child-class configure() function */
  protected function setCrudTables($tables)
  {
    foreach ($tables as $t => $columns) {
      $app = new xCrudApplication($this->process, $this->request, $this->session, $this->config);
      $app->setTableName($t);
      $app->setColumns($columns);

      $filter = new Filter;
      $app->getFilter()->addUrl('table', $t);
      $this->ajaxCrudApps[$t] = $app;
    }
  }

  public function setCrudFormTypes($tableName, $types)
  {
    $this->formTypes[$tableName] = $types;
  }

  public function setCrudFormOptions($tableName, $options)
  {
    $this->formOptions[$tableName] = $options;
  }

  abstract protected function configure() ;

  public function indexAction()
  {
    if (($t = $this->request->get('table', false)) !== false && isset($this->ajaxCrudApps[$t])) {
      return $this->ajaxCrudApps[$t]->indexAction();
    } else {
      $this->process->load404();
    }
  }

  public function editAction()
  {
    if (($t = $this->request->get('table', false)) !== false && isset($this->ajaxCrudApps[$t])) {

      if (isset($this->formTypes[$t]) || isset($this->formOptions[$t])) {
        $this->ajaxCrudApps[$t]->configureForm();

        if (isset($this->formTypes[$t])) $this->ajaxCrudApps[$t]->getForm()->setFormTypes($this->formTypes[$t]);
        if (isset($this->formOptions[$t])) $this->ajaxCrudApps[$t]->getForm()->setFormOptions($this->formOptions[$t]);
      }
      return $this->ajaxCrudApps[$t]->editAction();
    } else  {
      $this->process->load404();
    }

  }

  public function setReadOnly(array $tables)
  {
    foreach ($tables as $t) {
      if (isset($this->ajaxCrudApps[$t])) {
        $this->ajaxCrudApps[$t]->setReadOnly(true);
      }
    }
  }

  public function pagerAction()
  {
    if (($t = $this->request->get('table', false)) !== false && isset($this->ajaxCrudApps[$t])) {
      return $this->ajaxCrudApps[$t]->pagerAction();
    } else {
      $this->process->load404();
    }
  }

  public function deleteAction()
  {
    if (($t = $this->request->get('table', false)) !== false && isset($this->ajaxCrudApps[$t])) {
      return $this->ajaxCrudApps[$t]->deleteAction();
    } else {
      return new \PetakUmpet\Response('fail', 404);
    }
  }

}