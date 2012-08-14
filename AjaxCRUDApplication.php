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
    $this->formRelationFilter = array();
    $this->formAccessFilter = array();

    $this->extraFormFields = array();
    $this->extraFilter = array();
    $this->extraPageFilter = array();

    $this->inlineForm = false;

    $this->columns = null;
    $this->skips = null;
    $this->relationTabs = null;

    $this->appName = $this->request->getModule();

    // run setup before the actions
    $this->setup();
  }

  protected function getPageQueryFilter()
  {
    $s = '&search=' . $this->request->get('search');
    foreach ($this->extraFilter as $k => $v) {
      $s .= "&$k=$v";
    }
    foreach ($this->extraPageFilter as $k => $v) {
      $s .= "&$k=$v";
    }
    return $s;
  }

  public function setTable($table)
  {
    $this->tableName = $table;
  }

  public function setColumns($columns)
  {
    $this->columns = $columns;
  }

  public function setInlineForm($isInline)
  {
    $this->inlineForm = $isInline;
  }

  public function setExtraPageFilter($filter)
  {
    $this->extraPageFilter = $filter;
  }

  public function setFormAction($action)
  {
    $this->formAction = $action;
  }

  protected function setup()
  {
    if (!is_object($this->pager)) {
      $this->pager = new \PetakUmpet\Pager\TablePager($this->request);
    }

    $pageQry = $this->getPageQueryFilter();

    $this->pagerAction = $this->request->getAppUrl($this->appName . '/pager') . $pageQry;
    $this->editAction = $this->request->getAppUrl($this->appName . '/edit') . $pageQry;
    $this->deleteAction = $this->request->getAppUrl($this->appName . '/delete') . $pageQry;

    $this->pager->setFilter($this->request->getFilter());
    $this->pager->setInlineForm($this->inlineForm);
    $this->pager->setExtraFilter($this->extraFilter);

    $this->pager->setPagerAction($this->pagerAction);
    $this->pager->setEditAction($this->editAction);
    $this->pager->setDeleteAction($this->deleteAction);

    $this->pager->setTargetDiv('pager');
  }

  public function indexAction()
  {
    $pageQry = $this->getPageQueryFilter();
    $this->pagerAction = $this->request->getAppUrl($this->appName . '/pager') . $pageQry;
    $this->editAction = $this->request->getAppUrl($this->appName . '/edit') . $pageQry;
    $this->deleteAction = $this->request->getAppUrl($this->appName . '/delete') . $pageQry;

    $this->pager->setPagerAction($this->pagerAction);
    $this->pager->setEditAction($this->editAction);
    $this->pager->setDeleteAction($this->deleteAction);
    $this->pager->setInlineForm($this->inlineForm);

    $filterForm = new SearchForm;

    $filterForm->setValue($this->request->getFilter());

    if ($this->request->isPost()) {
      $filterForm->bind($this->request);

      $this->pager->setFilter($filterForm->getValue());
    }

    $this->pager->setExtraFilter($this->extraFilter);

    if ($this->pager instanceof \PetakUmpet\Pager\TablePager) {
      $this->pager->build($this->tableName, $this->columns);
    }

    return $this->renderView('AjaxCRUD/index', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'inlineForm' => $this->inlineForm,
                    'pager' => $this->pager,
                    'filterForm' => $filterForm,
                    'editAction' => $this->editAction,
                  ));
  }

  public function pagerAction()
  {
    $pageQry = $this->getPageQueryFilter();
    $this->pagerAction = $this->request->getAppUrl($this->appName . '/pager') . $pageQry;
    $this->editAction = $this->request->getAppUrl($this->appName . '/edit') . $pageQry;
    $this->deleteAction = $this->request->getAppUrl($this->appName . '/delete') . $pageQry;

    $this->pager->setPagerAction($this->pagerAction);
    $this->pager->setEditAction($this->editAction);
    $this->pager->setDeleteAction($this->deleteAction);
    $this->pager->setInlineForm($this->inlineForm);

    if ($this->pager instanceof \PetakUmpet\Pager\TablePager)
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
    $pageQry = $this->getPageQueryFilter();
    $this->pagerAction = $this->request->getAppUrl($this->appName . '/pager') . $pageQry;
    $this->editAction = $this->request->getAppUrl($this->appName . '/edit') . $pageQry;
    $this->deleteAction = $this->request->getAppUrl($this->appName . '/delete') . $pageQry;

    $this->pager->setPagerAction($this->pagerAction);
    $this->pager->setEditAction($this->editAction);
    $this->pager->setDeleteAction($this->deleteAction);
    $this->pager->setInlineForm($this->inlineForm);

    $formAction = $this->request->getAppUrl($this->appName . '/edit');
    if (isset($this->formAction)) {
      $formAction = $this->formAction;
    }

    $dbf = new DBConnector($this->tableName, array(), array(), $formAction); 

    foreach ($this->formOptions as $k => $v) {
      $dbf->setOptions($k, $v);
    }

    foreach ($this->formTypes as $k => $v) {
      $dbf->setType($k, $v);
    }

    foreach ($this->extraFormFields as $k => $v) {
      $dbf->add($k, $v);
    }

    foreach ($this->formRelationFilter as $k => $v) {
      $dbf->setRelationFilter($k, $v);
    }

    foreach ($this->formAccessFilter as $k => $v) {
      $dbf->setAccessFilter($k, $v);
    }

    // user_id field is always hidden
    $dbf->setType('user_id', 'hidden');

    $dbf->addFormAction(new Field\Submit('Save & Add', array('class' => 'btn')));

    $cancelAction = 'location.href=\''.$this->request->getAppUrl($this->appName .'/index').'\'';

    $dbf->addFormAction(new Field\Button('Cancel', array('class' => 'btn btn-warning', 'onclick' => $cancelAction)));

    if (!$this->request->isPost() && $this->request->getId()) {
      $dbf->importById($this->request->getId());
    } else {
      $dbf->build();
    }

    // set value for user_id here
    $dbf->setValue('user_id', $this->session->getUserid());
    $retId = false;

    if ($this->request->isPost()) {
      if (($retId = $dbf->bindValidateSave($this->request))) {
        if ($dbf->isClose()) {
          if ($this->inlineForm) {
            return new Response('');
          } else {
            return $this->redirect($this->appName . '/index' . $pageQry);
          }
        }
        if ($dbf->isAdd()) {
          return $this->redirect($this->appName . '/edit' . $pageQry);
        }
        $this->session->setFlash('Data is saved.');
      }
    }

    $id = $this->request->get('id', $retId);

    return $this->renderView('AjaxCRUD/edit', array(
                    'tableName' => $this->tableName,
                    'id' => $id,
                    'inlineForm' => $this->inlineForm,
                    'relations' => $this->relationTabs,  
                    'appName' => $this->appName,
                    'pagerAction' => $this->pagerAction,
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