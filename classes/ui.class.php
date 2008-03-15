<?php

/*---------------------------------------------------------------------------*/
/*! \file ui.class.php
    \brief the user interface class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The map class generates user interface related data
*/
/*---------------------------------------------------------------------------*/

	class ui
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
    
    /*-----------------------------------------------------------------------*/
    /*! \brief setStatusDisplay
        \param[in] $username  the username
        \param[in] $userid    the userid
        \param[in] $x         current x coordinate
        \param[in] $y         current y coordinate
        
        prints javascript function to set the status display
     */
    /*-----------------------------------------------------------------------*/
		function setStatusDisplay($username, $userid, $x, $y)
		{
      $info = $this->db->getUserInfo($userid);
      if($info['bunkers'] > 0)
        $bunker = 1;
      else
        $bunker = 0;
      echo("<script type=\"text/javascript\" language=\"JavaScript\">" .
        "setStatusDisplay('" . $username . "', " . $x . ", " . $y . ", " . 
        $info['score'] . ", " . 
        $bunker . ");</script>");
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUserCoords
        \param[in] $username  the username
        \param[in] $userid    the userid
        \param[in] $x         current x coordinate
        \param[in] $y         current y coordinate
        
        prints javascript function to set the status display
     */
    /*-----------------------------------------------------------------------*/
		function getUserCoords($userid)
		{
      $coords = $this->db->getUserCoords($userid);
      if($coords != 0)
        echo("\t\t\tvar userCoordX = " . $coords['x'] . ";\n " .
             "\t\t\tvar userCoordY = " . $coords['y'] . ";\n");
		}
    
	}
?>