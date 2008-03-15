<?php

	/* unit API */

	require_once("../config.inc.php");                  /* include config file */
	require_once("../classes/db.class.php");
	require_once("../classes/map.class.php");                         
	require_once("../classes/val.class.php");
  require_once("../classes/auth.class.php");
  require_once("../classes/ui.class.php");
  require_once("../classes/game.class.php");
	
  $myDB = new db();                            /* create database connection */
  
  $myAuth = new auth($myDB);   									        /* authenticate user */
	$authResult = $myAuth->authenticate(false);
  
  $myVal = new val();                                       /* validate data */
  
  if(!$myVal->validatePositiveInteger($_GET["sx"]))
    exit("-1");
  if(!$myVal->validatePositiveInteger($_GET["sy"]))
    exit("-1"); 
  if(!$myVal->validatePositiveInteger($_GET["tx"]))
    exit("-1");
  if(!$myVal->validatePositiveInteger($_GET["ty"]))
    exit("-1");  
  if(!$myVal->validatePositiveInteger($_GET["num"]))
    exit("-1");
  if($_GET["b"] != 0 && $_GET["b"] != 1)       /* build bunker can be 1 or 0 */
    exit("-1"); 
  if(!$myVal->validatePositiveInteger($_GET["x"]))
    $_GET["x"] = 0;
  if(!$myVal->validatePositiveInteger($_GET["y"]))
    $_GET["y"] = 0;  
  
	$myMap = new map($myDB, $myAuth->getUserId(), $_GET["x"], $_GET["y"]); 
	$myGame = new game($myDB);
  $myUi = new ui($myDB);
  
  if($_GET["b"] == 1)
  {
    $myGame->buildBunker($_GET["sx"], $_GET["sy"], $myAuth->getUserId());
  }
  else
  {
    $myGame->moveUnit($_GET["sx"], $_GET["sy"], $_GET["tx"], 
      $_GET["ty"], $_GET["num"], $myAuth->getUserId());
  }
  
  $myMap->getData();	
	$myMap->drawMap();
	$myMap->drawUnits();
  
  
  $myUi->setStatusDisplay($myAuth->getUsername(), $myAuth->getUserId(),
    $_GET["x"], $_GET["y"]);
  
?>