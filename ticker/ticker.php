<?php

	/* map API */

	require_once("../config.inc.php");                  /* include config file */
	require_once("../classes/db.class.php");
	require_once("../classes/ticker.class.php");                         


  $myDB = new db();                            /* create database connection */
  $myTicker = new ticker($myDB);                            /* create ticker */
  $myTicker->tick();
  
  
?>
