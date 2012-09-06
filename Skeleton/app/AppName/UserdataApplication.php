<?php

namespace AppName;

use PetakUmpet\Application\xCrudApplication;
use PetakUmpet\Database\Accessor;
use PetakUmpet\Pager\ModalPager;
use PetakUmpet\Pager\QueryPager;
use PetakUmpet\Response;
use PetakUmpet\Form\Component\TableAdapterForm;

class UserdataApplication extends xCrudApplication {

  public function __construct(\PetakUmpet\Process $process, \PetakUmpet\Request $request, \PetakUmpet\Session $session, \PetakUmpet\Config $config)
  {
    parent::__construct($process, $request, $session, $config);

    $this->tableName = 'userdata';
    $this->columns = array('id', 'name', 'is_admin');

    $this->inlineForm = false;

    $this->relationTabs = array(
        array (
            'targetId' => 'roles',
            'href' => $this->request->getAppUrl('Userdata/rolesPager&userid='),
            'name' => 'Roles'
          ),
        array (
            'targetId' => 'access',
            'href' => $this->request->getAppUrl('Userdata/accessPager&userid='),
            'name' => 'Access'
          ),
      );
  }

  public function configureForm()
  {
    parent::configureForm();
    $this->form->setFormOptions(array(
      'is_admin' => array('1' => 'Ya', '0' => 'Tidak'),
      ));
  }

  public function rolesPagerAction()
  {
    $userid = $this->request->get('userid');

    $q = "SELECT ur.id, r.name FROM user_role ur "
        ."JOIN roledata r ON r.id = ur.role_id "
        ."JOIN userdata u ON u.id = ur.user_id WHERE u.id = ?";

    $columns = array('id', 'name');

    $this->inlineForm = false;

    $editHref = $this->request->getAppUrl('Userdata/rolesForm&userid='.$userid);
    $delHref = $this->request->getAppUrl('Userdata/rolesDelete&userid='.$userid);
    $targetId = 'roles';

    $pager = new ModalPager($this->request);
    $pager->setEditAction($editHref);
    $pager->setDeleteAction($delHref);
    $pager->setTargetId($targetId);
    $pager->setTargetDiv($targetId.'Div');

    $pager->build($q, array($userid), $columns);

    return $this->renderView(Response::PetakUmpetView . 'xCrud/relationTabPager', array(
        'pager' => $pager,
        'href' => $editHref,
        'targetId' => $targetId,
      ));
  }

  public function rolesFormAction()
  {
    $form = new TableAdapterForm('user_role', array('id', 'user_id', 'role_id'), array(), $this->request->getAppUrl('Userdata/rolesForm'));

    $form->setFormTypes(array('user_id' => 'hidden'));

    if (($id = $this->request->get('id'))) {
      $form->setValuesById($id);
    }
    $form->setFormValues(array('user_id' => $this->request->get('userid')));

    if ($this->request->isPost() && $form->bindValidateSave($this->request)) {
      $this->session->setFlash('Data is saved');
    }

    return $this->renderView(Response::PetakUmpetView . 'xCrud/relationForm', array('targetId' => 'roles', 'form' => $form));
  }

  public function rolesDeleteAction()
  {
    $dba = new Accessor('user_role');
    if ($dba->delete(array('id' => $this->request->get('id')))) {
      return new Response('success');
    }
    return new Response('fail', 404);
  }

  public function accessPagerAction()
  {
    $userid = $this->request->get('userid');

    $q = "SELECT ra.id, a.name FROM role_access ra "
        ."JOIN accessdata a ON ra.access_id = a.id "
        ."JOIN user_role ur ON ur.role_id = ra.role_id "
        ."JOIN userdata u ON u.id = ur.user_id WHERE u.id = ?";

    $columns = array('id', 'name');


    $this->inlineForm = false;
    $editHref = $this->request->getAppUrl('Userdata/accessForm&userid='.$userid);
    $delHref = $this->request->getAppUrl('Userdata/accessDelete&userid='.$userid);
    $targetId = 'access';

    $pager = new ModalPager($this->request);
    $pager->setEditAction($editHref);
    $pager->setDeleteAction($delHref);
    $pager->setTargetId($targetId);
    $pager->setTargetDiv($targetId.'Div');

    $pager->build($q, array($userid), $columns);

    return $this->renderView(Response::PetakUmpetView . 'xCrud/relationTabPager', array(
        'pager' => $pager,
        'href' => $editHref,
        'targetId' => $targetId,
      ));
  }

  public function accessFormAction()
  {
    $form = new TableAdapterForm('role_access', array(), array(), $this->request->getAppUrl('Userdata/accessForm'));

    if (($id = $this->request->get('id'))) {
      $form->setValuesById($id);
    }
    $form->setFormValues(array('user_id' => $this->request->get('userid')));

    if ($this->request->isPost() && $form->bindValidateSave($this->request)) {
      $this->session->setFlash('Data is saved');
    }

    return $this->renderView(Response::PetakUmpetView . 'xCrud/relationForm', array('targetId' => 'access', 'form' => $form));
  }

  public function accessDeleteAction()
  {
    $dba = new Accessor('role_access');
    if ($dba->delete(array('id' => $this->request->get('id')))) {
      return new Response('success');
    }
    return new Response('fail', 404);
  }
  
}
