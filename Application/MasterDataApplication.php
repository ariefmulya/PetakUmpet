<?php

namespace PetakUmpet\Application;

use PetakUmpet\Application;
use PetakUmpet\Filter;


abstract class MasterDataApplication extends Application {
  
  private $ajaxCrudApps;

  private $formTypes;
  private $formOptions;
  private $fieldLabels;

  protected $user;

  public function __construct(\PetakUmpet\Process $process, \PetakUmpet\Request $request, \PetakUmpet\Session $session, \PetakUmpet\Config $config)
  {
    parent::__construct($process, $request, $session, $config); 
    $this->ajaxCrudApps = array();

    $this->formTypes = array();
    $this->formOptions = array();
    $this->fieldLabels = array();

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

  public function setCrudInlineForm($tables, $mode)
  {
    foreach ($tables as $t) {
      $this->ajaxCrudApps[$t]->setInlineForm($mode);
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

  public function setCrudFormFieldLabel($tableName, $labelOptions)
  {
    $this->fieldLabels[$tableName][] = $labelOptions;
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

      if (isset($this->formTypes[$t]) || isset($this->formOptions[$t]) || isset($this->fieldLabels[$t])) {
        $this->ajaxCrudApps[$t]->configureForm();

        if (isset($this->formTypes[$t])) $this->ajaxCrudApps[$t]->getForm()->setFieldTypes($this->formTypes[$t]);
        if (isset($this->formOptions[$t])) $this->ajaxCrudApps[$t]->getForm()->setFieldOptions($this->formOptions[$t]);
        if (isset($this->fieldLabels[$t])) {
          $form = $this->ajaxCrudApps[$t]->getForm()->getFormObject();  
          foreach ($this->fieldLabels[$t] as $arr) {
            $form->setFieldLabel(
                $arr['fieldName'], 
                $arr['fieldLabel']
              );
          }
        }
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

  public function setPagerOrderBy($tableName, $value)
  {
    if (isset($this->ajaxCrudApps[$tableName])) {
      $this->ajaxCrudApps[$tableName]->setPagerOrderBy($value);
    }
  }

  public function setPagerQuery($tableName, $query, $params)
  {
    if (isset($this->ajaxCrudApps[$tableName])) {
      $this->ajaxCrudApps[$tableName]->setPagerQuery($query, $params);
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