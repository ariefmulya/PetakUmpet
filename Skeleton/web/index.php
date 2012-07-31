<?php

/**
 * PetakUmpet2 - PetakUmpet Web Application Framework reloaded.
 * Copyright (C) since 2012 Arief M Utama <arief.utama@gmail.com>
 *
 * All Rights Reserved.
 */

define('DS', DIRECTORY_SEPARATOR); // quicker

require_once __DIR__ . DS . '..' . DS . 'lib' . DS . 'PetakUmpet' . DS . 'Init.php';

$init = new PetakUmpet\Init;

$init->run();

