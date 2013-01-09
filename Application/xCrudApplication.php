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

use PetakUmpet\Pager\ModalPager;
use PetakUmpet\Pager\QueryPager;
use PetakUmpet\Filter;

use PetakUmpet\Database\Model;
use PetakUmpet\Database\Schema;

use PetakUmpet\Form;
use PetakUmpet\Form\Field;
use PetakUmpet\Form\Component\TableAdapterForm;
use PetakUmpet\Form\Component\SearchForm;


class xCrudApplication extends Application {

  const USE_QUERY_PAGER = 1;
  const USE_TABLE_PAGER = 2;

  private $appName;

  protected $crudTitle;
  protected $tableName;
  protected $columns;
  protected $skip;
  protected $hasScript;

  protected $tabs;

  protected $inlineForm;

  protected $pager;
  protected $form;
  protected $filter;
  protected $user;

  private $readOnly;  

  /* pager related variables */
  private $pagerAction;
  private $editAction;
  private $deleteAction;
  private $pagerOrderBy;
  private $pagerQuery;
  private $pagerParams;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    parent::__construct($process, $request, $session, $config);

    /* main variables */
    $this->appName = $this->request->getModule();
    $this->tableName = null;    /* main table to CRUD */
    $this->crudTitle = null;
    $this->columns = null;      /* columns to display in pager */
    $this->skip = null;      /* skipped columns */
    $this->tabs = null; /* tabs for related tables or actions */
    $this->inlineForm = true;  /* set true for inline form, useful for simple master tables */
    $this->hasScript = null;

    $this->readOnly = false;

    $this->pagerOrderBy = null;

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

  public function setColumnsSkip($skip)
  {
    $this->skip = $skip;
  }

  public function setReadOnly($state=true)
  {
    $this->readOnly = $state;
  }

  public function setPagerOrderBy($value=null)
  {
    $this->pagerOrderBy = $value;
  }

  public function setPagerQuery($query, $params)
  {
    $this->pagerQuery = $query;
    $this->pagerParams = $params;
  }

  public function configurePager()
  {
    // to avoid php strict mode error
    $numArgs = func_num_args();
    $query = $numArgs >= 1 ? func_get_arg(0) : null;
    $params = $numArgs >= 2 ? func_get_arg(1) : array();

    if ($query === null && !isset($this->pagerQuery)) {
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

    if (isset($this->pagerOrderBy) && $this->pager instanceof TablePager) {
      $this->pager->setOrderBy($this->pagerOrderBy);
    }

    $this->pager->setPagerAction($this->pagerAction);
    $this->pager->setEditAction($this->editAction);
    $this->pager->setDeleteAction($this->deleteAction);

    $this->pager->setReadOnly($this->readOnly);

    if ($query === null && !isset($this->pagerQuery)) {
      $this->pager->build($this->tableName, $this->columns);
    } else {
      $this->pager->build(($query === null ? $this->pagerQuery : $query), 
        ($query === null ? $this->pagerParams : $params), $this->columns);
    }
  }

  public function configureForm()
  {
    // to avoid php strict mode error
    $numArgs = func_num_args();
    $action = $numArgs >= 1 ? func_get_arg(0) : null;
    $cancelAction = $numArgs >= 2 ? func_get_arg(1) : null;

    static $isConfigured = false;

    if ($isConfigured === false) {
      $formAction = $action !== null ? $action 
                      : $this->request->getAppUrl($this->appName . '/edit');
      $formAction .= $this->filter->getUrlFilter();

      $cancelAction = $cancelAction !== null ? $cancelAction 
                        : $this->request->getAppUrl($this->appName .'/index');
      $cancelAction .= $this->filter->getUrlFilter();

      if (! $this->form  instanceof TableAdapterForm) {
        $skip = ($this->skip == null) ? array() : $this->skip;
        $this->form = new TableAdapterForm($this->tableName, array(), $skip, $formAction); 
      } else {
        $this->form->setAction($formAction);
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

    $filterForm = new SearchForm;

    $filterForm->setValue($this->filter->getValue('search'));

    if ($this->request->isPost()) {
      $filterForm->bind($this->request);
      $this->filter->setValue('search', $filterForm->getValue());
    }

    if ($this->crudTitle === null) $this->crudTitle = $this->tableName;

    $module = $this->request->getModule();

    return $this->renderView(Response::PetakUmpetView . 'xCrud/index', array(
                    'crudTitle' => $this->crudTitle,
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

    if ($this->crudTitle === null) $this->crudTitle = $this->tableName;

    return $this->renderView(Response::PetakUmpetView . 'xCrud/pager', array(
                    'crudTitle' => $this->crudTitle,
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
    if ($this->crudTitle === null) $this->crudTitle = $this->tableName;

    return $this->renderView(Response::PetakUmpetView . 'xCrud/edit', array(
                    'crudTitle' => $this->crudTitle,      
                    'tableName' => $this->tableName,
                    'tabHref' => $this->request->getAppUrl($this->request->getModule() . '/tabPager'),
                    'id' => $id,
                    'inlineForm' => $this->inlineForm,
                    'tabs' => $this->tabs,  
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

    $dbm = new Model($this->tableName);
    if ($dbm->delete(array('id' => $this->request->get('id')))) {
      return new Response('success');
    }
    return new Response('fail', 404);
  }

  public function tabPagerAction()
  {
    $tabId  = $this->request->get('tabid');

    if (!isset($this->tabs[$tabId])) {
      return $this->process->load404();
    }

    $tab = $this->tabs[$tabId];
    $relKey = $tab['relKey'];
    $relVal  = $this->request->get('relval');

    $module = $this->request->getModule();
    $editHref = $this->request->getAppUrl($module . '/tabForm&tabid=' . $tabId  . '&relkey=' . $relKey . '&relval=' . $relVal);
    $delHref = $this->request->getAppUrl($module . '/tabDelete&tabid=' . $tabId );

    $pager = new ModalPager($this->request);
    $pager->setEditAction($editHref);
    $pager->setDeleteAction($delHref);
    $pager->setTargetId($tabId);
    $pager->setTargetDiv($tabId.'Div');

    $params= array($relKey => $relVal);

    if (isset($tab['relatedData']) && count($tab['relatedData']) > 0) {
      foreach ($tab['relatedData'] as $relId => $relVal) {
        $params[$relId] = $relVal;
      }
    }

    $pager->build($tab['query'], $params, $tab['columns']);

    return $this->renderView(Response::PetakUmpetView . 'xCrud/tabPager', array(
        'pager' => $pager,
        'href' => $editHref,
        'targetId' => $tabId,
      ));
  }

  public function tabFormAction()
  {
    $tabId  = $this->request->get('tabid');

    if (!isset($this->tabs[$tabId])) {
      return $this->process->load404();
    }

    $tab = $this->tabs[$tabId];

    $module = $this->request->getModule();

    $relVal = $this->request->get('relval');
    $relKey = $this->request->get('relkey');

    $formAction = $this->request->getAppUrl($module . '/tabForm&tabid=' . $tabId  . '&relkey=' . $relKey . '&relval=' . $relVal . '&id=' . $this->request->get('id'));

    $formColumns = isset($tab['formColumns']) ? $tab['formColumns'] : array();

    if (count($formColumns) > 0 && !in_array($relKey, $formColumns)) {
      $formColumns[] = $relKey;
    }

    if (isset($tab['relatedData']) && count($tab['relatedData']) > 0) {
      foreach ($tab['relatedData'] as $relId => $relVal) {
        if (count($formColumns) > 0 && !in_array($relId, $formColumns)) {
          $formColumns[] = $relId;
        }
      }
    }

    $form = new TableAdapterForm($tab['tableName'], $formColumns, array(), $formAction);

    $form->setFieldTypes(array($relKey => 'hidden'));
    $form->setFieldValues(array($relKey => $relVal));

    // for extra params to be saved, useful for adding
    // default set of params to every forms.
    // the extra-params could come from unrelated data (things like current userid)
    if (isset($tab['relatedData']) && count($tab['relatedData']) > 0) {
      foreach ($tab['relatedData'] as $relId => $relVal) {
        $form->setFieldTypes(array($relId => 'hidden'));
        $form->setFieldValues(array($relId => $relVal));
      }
    }

    if (isset($tab['formFieldTypes']) && count($tab['formFieldTypes']) > 0) {
      foreach ($tab['formFieldTypes'] as $field => $type) {
        $form->setFieldTypes(array($field=> $type));
      }
    }

    if (isset($tab['formFieldOptions']) && count($tab['formFieldOptions']) > 0) {
      foreach ($tab['formFieldOptions'] as $field => $options) {
        $form->setFieldOptions(array($field=> $options));
      }
    }

    // 'id' is really a special field for our framework, don't ever forget that ;-)
    if (($id = $this->request->get('id'))) {
      $form->setValuesById($id);
    }
 
    if ($this->request->isPost() && $form->bindValidateSave($this->request)) {
      $this->session->setFlash('Data is saved');
    }

    return $this->renderView(Response::PetakUmpetView . 'xCrud/tabForm', array('targetId' => $tabId, 'form' => $form));
  }

  public function tabDeleteAction()
  {
    $tabId  = $this->request->get('tabid');

    if (!isset($this->tabs[$tabId])) {
      return $this->process->load404();
    }

    $tab = $this->tabs[$tabId];

    $dbm = new Model($tab['tableName']);

    if ($dbm->delete(array('id' => $this->request->get('id')))) {
      return new Response('success');
    }
    return new Response('fail', 404);
  }
}
