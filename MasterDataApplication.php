<?php

namespace PetakUmpet;

class MasterDataApplication extends Application {
  
  private $ajaxCrudApps;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    parent::__construct($process, $request, $session, $config); 
    $this->ajaxCrudApps = array();

    $this->setup();
  }

  protected function setAjaxCrudApps($tables)
  {
    foreach ($tables as $t => $columns) {
      $app = new AjaxCRUDApplication($this->process, $this->request, $this->session, $this->config);
      $app->setTable($t);
      $app->setColumns($columns);
      $app->setExtraPageFilter(array('table'=>$t));
      $app->setFormAction($this->request->getAppUrl($this->request->getModule() . '/edit&table=' . $t));
      $this->ajaxCrudApps[$t] = $app;
    }
  }

  protected function setup()
  {
    // child shall need to implement this and call 
    // setAjaxCrudApps inside
  }

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