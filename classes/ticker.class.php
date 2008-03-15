<?php

/*---------------------------------------------------------------------------*/
/*! \file ticker.class.php
    \brief the user interface class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The ticker class updates the game in regular intervals
*/
/*---------------------------------------------------------------------------*/

	class ticker
	{
    private $db; 																			/* the database object */

    /*-----------------------------------------------------------------------*/
    /*! \brief constructor
        
        Initializes object
     */
    /*-----------------------------------------------------------------------*/
		function __construct(&$db)
		{
      $this->db = $db;
		}
    
    function tick()
    {
      if(!$this->db->startTransaction()) return;  
      
                      /* add 1 unit to bunkers and 2 units to command center */
      if(!$this->db->addUnitsTick(0, 1))
      {
        $this->db->rollbackTransaction();
        echo("error during bunker update");
        return false;
      }
      if(!$this->db->addUnitsTick(2, 2))
      {
        $this->db->rollbackTransaction();
        echo("error during command center update");
        return false;
      }
      if(!$this->db->calcBunkersTick())
      {
        $this->db->rollbackTransaction();
        echo("error during bunker calculation");
        return false;
      }
      $this->db->commitTransaction();
      echo(time() . "tick successful, startet at " . $_SERVER['REQUEST_TIME']);
      return true;
    }
	}
?>