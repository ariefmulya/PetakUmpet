<?php

namespace PetakUmpet;

use PetakUmpet\Application;
use PetakUmpet\Response;

use PetakUmpet\Database\Accessor;

use PetakUmpet\Form;
use PetakUmpet\Form\Field;
use PetakUmpet\Form\DBConnector;
use PetakUmpet\Form\Custom\SearchForm;

class AjaxCRUDApplication extends Application {

  protected $appName;
  protected $inlineForm;

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
    $this->inlineForm = false;
    $this->submenuFile = null;

    $this->columns = null;
    $this->skips = null;
    $this->relationTabs = null;

    $this->appName = $this->request->getModule();
    $this->setup();
  }

  protected function setup()
  {
    $pager = new \PetakUmpet\Pager\TablePager($this->request);
    $pager->setFilter($this->request->getFilter());

    $pager->setInlineForm($this->inlineForm);
    $pager->setPagerAction($this->request->getAppUrl($this->appName . '/pager'));
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
                    'inlineForm' => $this->inlineForm,
                    'pager' => $this->pager,
                    'filterForm' => $filterForm,
                    'submenuFile' => $this->submenuFile,
                  ));
  }

  public function pagerAction()
  {
    $this->pager->build($this->tableName, $this->columns);

    return $this->renderView('AjaxCRUD/pager', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'inlineForm' => $this->inlineForm,
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

    $dbf->addFormAction(new Field\Submit('Save & Add', array('class' => 'btn')));

    $cancelAction = 'location.href=\''.$this->request->getAppUrl($this->appName .'/index').'\'';

    $dbf->addFormAction(new Field\Button('Cancel', array('class' => 'btn btn-warning', 'onclick' => $cancelAction)));

    if (!$this->request->isPost() && $this->request->getId()) {
      $dbf->importById($this->request->getId());
    } else {
      $dbf->build();
    }

    $dbf->setValue('user_id', $this->session->getUserid());

    if ($this->request->isPost()) {
      if ($dbf->bindValidateSave($this->request)) {
        if ($dbf->isClose()) {
          if ($this->inlineForm) {
            return new Response('');
          } else {
            return $this->redirect($this->appName . '/index');
          }
        }
        if ($dbf->isAdd()) {
          return $this->redirect($this->appName . '/edit');
        }
        $this->session->setFlash('Data is saved.');
      }
    }

    return $this->renderView('AjaxCRUD/edit', array(
                    'tableName' => $this->tableName,
                    'id' => $this->request->getId(),
                    'inlineForm' => $this->inlineForm,
                    'relations' => $this->relationTabs,  
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