<?php

namespace PetakUmpet\Application;

use PetakUmpet\Filter;

abstract class MasterDataApplication extends Application {
  
  private $ajaxCrudApps;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    parent::__construct($process, $request, $session, $config); 
    $this->ajaxCrudApps = array();

    $this->configure();
  }

  /* to be called in child-class setup() function */
  protected function setAjaxCrudApps($tables)
  {
    foreach ($tables as $t => $columns) {
      $app = new xCrudApplication($this->process, $this->request, $this->session, $this->config);
      $app->setTable($t);
      $app->setColumns($columns);

      $filter = new Filter;
      $app->getFilter()->addUrl('table', $t);
      $app->setFormAction($this->request->getAppUrl($this->request->getModule() . '/edit&table=' . $t));
      $this->ajaxCrudApps[$t] = $app;
    }
  }

  abstract protected function setup() ;

  public function indexAction()
  {
    if (($t = $this->request->get('table', false)) !== false) {
      $this->ajaxCrudApps[$t]->setInlineForm(true);
      return $this->ajaxCrudApps[$t]->indexAction();
    } else {
      $this->renderView('AjaxCRUD/masterIndex');
    }
  }

  public function editAction()
  {
    if (($t = $this->request->get('table', false)) !== false) {
      $this->ajaxCrudApps[$t]->setInlineForm(true);
      return $this->ajaxCrudApps[$t]->editAction();
    }
  }

  public function pagerAction()
  {
    if (($t = $this->request->get('table', false)) !== false) {
      $this->ajaxCrudApps[$t]->setInlineForm(true);
      return $this->ajaxCrudApps[$t]->pagerAction();
    }
  }

  public function deleteAction()
  {
    if (($t = $this->request->get('table', false)) !== false) {
      $this->ajaxCrudApps[$t]->setInlineForm(true);
      return $this->ajaxCrudApps[$t]->deleteAction();
    }
  }

}