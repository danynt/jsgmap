<?php

	/* unit API */

	require_once("../config.inc.php");                  /* include config file */
	require_once("../classes/db.class.php");
	require_once("../classes/map.class.php");                         
	require_once("../classes/val.class.php");
  require_once("../classes/auth.class.php");
	
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
  
	$map = new map($myDB, 1, $_GET["sx"], $_GET["sy"]);          /* create map */
	
  $map->moveUnit($_GET["sx"], $_GET["sy"], $_GET["tx"], 
    $_GET["ty"], $_GET["num"], $myAuth->getUserId());
  
?>