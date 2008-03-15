<?php

	/* map API */

	require_once("../config.inc.php");                  /* include config file */
	require_once("../classes/db.class.php");
	require_once("../classes/map.class.php");                         
	require_once("../classes/val.class.php");  
  require_once("../classes/auth.class.php");
  require_once("../classes/ui.class.php");

  $myDB = new db();                            /* create database connection */
  $myAuth = new auth($myDB);   									        /* authenticate user */
	$authResult = $myAuth->authenticate(false);
  
	
  $myVal = new val();                                         /* validate data */
  
  if(!$myVal->validatePositiveInteger($_GET["x"]))
    $_GET["x"] = 0;
  if(!$myVal->validatePositiveInteger($_GET["y"]))
    $_GET["y"] = 0;  
  
                                                              /* create map */
	$map = new map($myDB, $myAuth->getUserId(), $_GET["x"], $_GET["y"]);            
	
  $map->getData();	
	$map->drawMap();
	$map->drawUnits();

  $myUi = new ui($myDB);
  $myUi->setStatusDisplay($myAuth->getUsername(), $myAuth->getUserId(),
    $_GET["x"], $_GET["y"]);
  
?>
