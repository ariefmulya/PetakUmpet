<?php

namespace PetakUmpet\UI;

class DataTables extends UI {

  public function __construct(Request $request, Session $session, Config $config, Template $T)
  {
    parent::($request, $session, $config, $T);

    $this->page = $request->getPage();
    $this->link = $request->getRoutingLinkFromPage($this->page);

  }

  

}