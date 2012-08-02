<?php

namespace PetakUmpet;

use PetakUmpet\Application;
use PetakUmpet\Response;

use PetakUmpet\Database\Accessor;

use PetakUmpet\Form;
use PetakUmpet\Form\DBConnector;
use PetakUmpet\Form\Custom\SearchForm;

class AjaxCRUDApplication extends Application {

  protected $appName;

  protected $tableName;
  protected $columns;
  protected $skips;

  protected $pager;
  protected $dbf;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    parent::__construct($process, $request, $session, $config);

    $this->formOptions = array();
    $this->formTypes = array();
    $this->extraFormFields = array();

    $this->setup();
  }

  protected function setup()
  {
    $pager = new \PetakUmpet\Database\TablePager($this->request);
    $pager->setFilter($this->request->getFilter());

    $pager->setPagerAction($this->request->getAppUrl($this->appName . '/table'));
    $pager->setEditAction($this->request->getAppUrl($this->appName . '/edit'));
    $pager->setDeleteAction($this->request->getAppUrl($this->appName . '/delete'));
    $pager->setTargetDiv('pager');

    $this->pager = $pager;
  }

  public function indexAction()
  {

    $filterForm = new SearchForm;

    $filterForm->setValue($this->request->getFilter());

    if ($this->request->isPost()) {
      $filterForm->bind($this->request);

      $this->pager->setFilter($filterForm->getValue());
    }

    $this->pager->build($this->tableName, $this->columns);

    return $this->renderView('AjaxCRUD/index', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'pager' => $this->pager,
                    'filterForm' => $filterForm,
                  ));
  }

  public function pagerAction()
  {
    $this->pager->build($this->tableName, $this->columns);

    return $this->renderView('AjaxCRUD/pager', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'pager' => $this->pager,
                  ));
  }

  public function editAction()
  {
    $dbf = new DBConnector($this->tableName, array(), array(), $this->request->getAppUrl($this->appName . '/edit'));

    foreach ($this->formOptions as $k => $v) {
      $dbf->setOptions($k, $v);
    }

    foreach ($this->formTypes as $k => $v) {
      $dbf->setType($k, $v);
    }

    foreach ($this->extraFormFields as $k => $v) {
      $dbf->add($k, $v);
    }

    if (!$this->request->isPost() && $this->request->getId()) {
      $dbf->importById($this->request->getId());
    } else {
      $dbf->build();
    }

    $dbf->setValue('user_id', $this->session->getUserid());

    if ($this->request->isPost()) {
      if ($dbf->bindValidateSave($this->request)) {
        if ($dbf->isClose()) return new Response('');
        if ($dbf->isAdd()) return $this->redirect($this->appName . '/edit');
        $this->session->setFlash('Data is saved.');
      }
    }

    return $this->renderView('AjaxCRUD/edit', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'pager' => $this->pager,
                    'form' => $dbf,
                  ));
  }

  public function deleteAction()
  {
    $dba = new Accessor($this->tableName);
    if ($dba->delete(array('id' => $this->request->get('id')))) {
      return new Response('success');
    }
    return new Response('fail', 404);
  }

}