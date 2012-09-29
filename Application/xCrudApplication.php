<?php

namespace PetakUmpet\Application;

use PetakUmpet\Application;
use PetakUmpet\Process;
use PetakUmpet\Request;
use PetakUmpet\Session;
use PetakUmpet\Response;
use PetakUmpet\Config;
use PetakUmpet\Pager;
use PetakUmpet\Pager\TablePager;
use PetakUmpet\Pager\QueryPager;
use PetakUmpet\Filter;

use PetakUmpet\Database\Accessor;
use PetakUmpet\Database\Schema;

use PetakUmpet\Form;
use PetakUmpet\Form\Field;
use PetakUmpet\Form\Component\TableAdapterForm;
use PetakUmpet\Form\Component\SearchForm;


class xCrudApplication extends Application {

  const USE_QUERY_PAGER = 1;
  const USE_TABLE_PAGER = 2;

  private $appName;
  protected $tableName;
  protected $columns;
  protected $hasScript;

  protected $relationTabs;
  protected $inlineForm;

  protected $pager;
  protected $form;
  protected $filter;
  protected $user;

  private $readOnly;  

  private $pagerAction;
  private $editAction;
  private $deleteAction;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    parent::__construct($process, $request, $session, $config);

    /* main variables */
    $this->appName = $this->request->getModule();
    $this->tableName = null;    /* main table to CRUD */
    $this->columns = null;      /* columns to display in pager */
    $this->relationTabs = null; /* tabs for related tables or actions */
    $this->inlineForm = true;  /* set true for inline form, useful for simple master tables */
    $this->hasScript = null;

    $this->readOnly = false;

    /* filters */
    $this->filter = new Filter;
    $this->filter->addQuery('search', $this->request->get('search'));
    $this->filter->addUrl('search', $this->request->get('search'));
    $this->filter->add('search', $this->request->get('search'));
    
    $this->user = $this->session->getUser();
  }

  public function getFilter()
  {
    return $this->filter;
  }

  public function getForm()
  {
    return $this->form;
  }

  public function setTableName($tableName)
  {
    $this->tableName = $tableName;
  }

  public function setColumns($columns)
  {
    $this->columns = $columns;
  }

  public function setReadOnly($state=true)
  {
    $this->readOnly = $state;
  }

  public function configurePager($query=null, $params=array())
  {
    if ($query === null) {
      $this->pager = new TablePager($this->request, 5);
    } else {
      $this->pager = new QueryPager($this->request, 5);
    }

    // setting up filter for pager
    $schema = new Schema($this->tableName);
    $columns = $schema->getColumnNames();
    foreach ($columns as $c) {
      if ($c == 'id') continue;
      $this->filter->addQuery($c, $this->request->get('search'));
    }

    /* action links */
    $pageUrl = $this->filter->getUrlFilter();  /* this is useful for keeping extra filters in request */
    $this->pagerAction = $this->request->getAppUrl($this->appName . '/pager') . $pageUrl;
    $this->editAction = $this->request->getAppUrl($this->appName . '/edit') . $pageUrl;
    $this->deleteAction = $this->request->getAppUrl($this->appName . '/delete') . $pageUrl;

    $this->pager->setFilter($this->filter);
    $this->pager->setInlineForm($this->inlineForm);

    $this->pager->setPagerAction($this->pagerAction);
    $this->pager->setEditAction($this->editAction);
    $this->pager->setDeleteAction($this->deleteAction);

    $this->pager->setReadOnly($this->readOnly);

    if ($query === null) {
      $this->pager->build($this->tableName, $this->columns);
    } else {
      $this->pager->build($query, $params, $this->columns);
    }
  }

  public function configureForm($action=null, $cancelAction=null)
  {
    static $isConfigured = false;

    if ($isConfigured === false) {
      $formAction = $action !== null ? $action 
                      : $this->request->getAppUrl($this->appName . '/edit');
      $formAction .= $this->filter->getUrlFilter();

      $cancelAction = $cancelAction !== null ? $cancelAction 
                        : $this->request->getAppUrl($this->appName .'/index');
      $cancelAction .= $this->filter->getUrlFilter();

      if (! $this->form  instanceof TableAdapterForm) {
        $this->form = new TableAdapterForm($this->tableName, array(), array(), $formAction); 
      } else {
        $this->form->setFormAction($formAction);
      }
      $this->form->setReadOnly($this->readOnly);

      /* user_id field is always hidden */
      $this->form->getFormObject()->setFieldType('user_id', 'hidden');
      /* and set value for user_id from session */
      $this->form->getFormObject()->setFieldValue('user_id', $this->session->getUser()->getId());

      $this->form->setCancelAction('location.href = \'' . $cancelAction . '\'');

      $isConfigured = true;
    }
  }

  public function indexAction()
  {
    $this->configurePager();
    echo $this->filter->getValue('search');

    $filterForm = new SearchForm;

    $filterForm->setValue($this->filter->getValue('search'));

    if ($this->request->isPost()) {
      $filterForm->bind($this->request);
      $this->filter->setValue('search', $filterForm->getValue());
    }

    return $this->renderView(Response::PetakUmpetView . 'xCrud/index', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'inlineForm' => $this->inlineForm,
                    'pager' => $this->pager,
                    'filterForm' => $filterForm,
                    'editAction' => $this->editAction,
                    'readOnly' => $this->readOnly,
                    'hasScript' => $this->hasScript,
                  ));
  }

  public function pagerAction()
  {
    $this->configurePager();

    return $this->renderView(Response::PetakUmpetView . 'xCrud/pager', array(
                    'tableName' => $this->tableName,
                    'appName' => $this->appName,
                    'inlineForm' => $this->inlineForm,
                    'pager' => $this->pager,
                    'pagerAction' => $this->pagerAction,
                    'hasScript' => $this->hasScript,
                  ));
  }

  public function editAction()
  {
    if ($this->readOnly === true) return;
    
    if ($this->inlineForm) $this->configurePager();
    $this->configureForm();
    
    if (!$this->request->isPost() && $this->request->get('id')) {
      $this->form->setValuesById($this->request->get('id'));
    }
    $retId = false;

    if ($this->request->isPost()) {
      if (($retId = $this->form->bindValidateSave($this->request))) {
        if ($this->form->isSaveAndAdd($this->request)) {
          $this->session->setFlash('Data is saved, please enter another one.');
          return $this->redirect($this->appName . '/edit' . $this->filter->getUrlFilter());
        }
        $this->session->setFlash('Data is saved.');
      }
    }

    $id = $this->request->get('id', $retId);

    return $this->renderView(Response::PetakUmpetView . 'xCrud/edit', array(
                    'tableName' => $this->tableName,
                    'id' => $id,
                    'inlineForm' => $this->inlineForm,
                    'relations' => $this->relationTabs,  
                    'appName' => $this->appName,
                    'pagerAction' => $this->pagerAction,
                    'pager' => $this->pager,
                    'form' => $this->form,
                    'readOnly' => $this->readOnly,
                  ));
  }

  public function deleteAction()
  {
    if ($this->readOnly === true) return;

    $dba = new Accessor($this->tableName);
    if ($dba->delete(array('id' => $this->request->get('id')))) {
      return new Response('success');
    }
    return new Response('fail', 404);
  }

}
