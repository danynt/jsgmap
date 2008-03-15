<?php

/*---------------------------------------------------------------------------*/
/*! \file map.class.php
    \brief the map class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The map class generates the map (html) and places the units on the map.
    Units can be moved on the map.
*/
/*---------------------------------------------------------------------------*/

	class map
	{
		private $db; 																			/* the database object */
		private $map;																						/* the map array */
		private $units;													 /* the units on the current map */
		private $userid;	 	                           /* id of the current user */
		
    private $x1;                                                /* map range */
    private $x2;
		private $y1;
    private $y2;

    /*-----------------------------------------------------------------------*/
    /*! \brief constructor
        \param[in] db a reference to the database connector objects
        \param[in] userid userid
        \param[in] x user center x coordinate
        \param[in] y user center y coordinate
        
        Initializes object - calculates the range of the map
     */
    /*-----------------------------------------------------------------------*/
		function __construct(&$db, $userid=0, $x, $y)
		{
			$this->db = $db;
      $this->userid = $userid;
      $this->calcCoords($x, $y);
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief calcCoords
        \param[in] x user center x coordinate
        \param[in] y user center y coordinate
        
        Calculates the range of the map from(x,y) to(x,y)
     */
    /*-----------------------------------------------------------------------*/
    private function calcCoords($x=-1, $y=-1)
    {   
      $x1off = -MAP_NUMTILES_X/2;
      $x2off = MAP_NUMTILES_X/2 - 1;
      
      $y1off = -MAP_NUMTILES_Y/2 + 1;
      $y2off = MAP_NUMTILES_Y/2;
      
      $this->x1 = $x + $x1off;
      $this->x2 = $x + $x2off;
      $this->y1 = $y + $y1off;
      $this->y2 = $y + $y2off;
      
      if($this->x1 < 0)
        $x = -$x1off;
      else if($this->x2 >= MAP_SIZE_X)
        $x = MAP_SIZE_X - $x2off - 1;
        
      if($this->y1 < 0)
        $y = -$y1off;
      else if($this->y2 >= MAP_SIZE_Y)
        $y = MAP_SIZE_Y - $y2off - 1;
        
      $this->x1 = $x + $x1off;
      $this->x2 = $x + $x2off;
      $this->y1 = $y + $y1off;
      $this->y2 = $y + $y2off;
      //echo("x1: " . $this->x1 . " x2: " . $this->x2 . 
      //  " y1: " . $this->y1 . " y2: " . $this->y2);
    }
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getData
        
        Fetches data from database
     */
    /*-----------------------------------------------------------------------*/
		function getData()
		{
			$this->map = $this->db->getMap($this->x1, $this->x2, 
        $this->y1, $this->y2);
			$this->units = $this->db->getUnits($this->x1, $this->x2, 
        $this->y1, $this->y2);
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief drawMap
        
        Draws the map
     */
    /*-----------------------------------------------------------------------*/
		function drawMap()
		{
      $i = 0;
 			for($y=0; $y<MAP_NUMTILES_Y; $y++)
			{  
        $xc = $y * MAP_TILESIZE_X / 2;
        $yc = (MAP_TILESIZE_Y * MAP_NUMTILES_Y / 2) - 
          (MAP_TILESIZE_Y/ 2 ) - ($y * MAP_TILESIZE_Y / 2);
        for($x=0; $x<MAP_NUMTILES_X	; $x++)
        {
          echo("\t\t\t<div class=\"t" . $this->map[$i]["tile"] . "\"" . 
            " style=\"top: " . $yc . "px; left: " . $xc . "px;\">" . 
            /*$this->map[$i]["x"] . $this->map[$i]["y"].*/"</div>\n");
          $xc += MAP_TILESIZE_X/2;
          $yc += MAP_TILESIZE_Y/2;
          $i++;
        }
      }
		}
		
    /*-----------------------------------------------------------------------*/
    /*! \brief drawUnits
        
        Draws the units
     */
    /*-----------------------------------------------------------------------*/
		function drawUnits()
		{
      global $udef;                 /* make unit definition array accessible */
      global $digits;              /* make digit definition array accessible */
      
      if($this->units == NULL)
        return;
      
			foreach($this->units as $unit)
			{
        $uoffsetx = $udef[$unit['unitid']][0];
        $uoffsety = $udef[$unit['unitid']][1];
        $style = $udef[$unit['unitid']][2];
        $doffsety = $udef[$unit['unitid']][5];

				if($unit['userid'] == $this->userid)
        {
					$ownunit = 1;
          $color = "y";
				}
        else
				{
          $ownunit = 0;
          $color = "r";
        }    
				if($unit["y"]%2)
					$xc = MAP_TILESTART_X1;
				else
					$xc = MAP_TILESTART_X2;
        
        if($unit["num"] < 10)
          $doffsetx = $digits[1];
        else if($unit["num"] < 100)
          $doffsetx = $digits[2];
        else if($unit["num"] < 1000)
          $doffsetx = $digits[3];
        else if($unit["num"] < 10000)
          $doffsetx = $digits[4];
        else if($unit["num"] < 100000)
          $doffsetx = $digits[5];
        else if($unit["num"] < 1000000)
          $doffsetx = $digits[6];


        
        $xct = $unit["x"] - $this->x1;
        $yct = $unit["y"] - $this->y1;
        
        $xc = $xct * MAP_TILESIZE_X / 2 + $yct * MAP_TILESIZE_X / 2;
        $yc = (MAP_NUMTILES_Y * MAP_TILESIZE_Y / 2) + 
          ($yct * MAP_TILESIZE_Y / 2) - ($xct * MAP_TILESIZE_Y / 2) - 
          (MAP_TILESIZE_Y / 2);

        $onclick = "onClick=\"unitClick(" . $unit["id"] . ", " . 
          $unit["num"] . ", " . $ownunit . ", '" . 
          $udef[$unit['unitid']][2] . "', " . $unit["x"] . ", " . 
          $unit["y"] . ", " . ($xc + $uoffsetx) . ", " . 
          ($yc + $uoffsety) . ", '" . $unit["username"] . 
          "'); return false;\" \n onmouseover=\" unitOver(" . $unit["x"] . 
          ", " . $unit["y"] . ", '" . $unit["username"] . 
          "'); return false;\"";

				echo("\t\t\t<div " . $onclick . " class=\"" . $style . $color . 
          "\" style=\"top: " . ($yc + $uoffsety) . "px; left: " .
          ($xc + $uoffsetx) . "px;\" id=\"" . $unit["id"] ."\"></div>\n");	
        echo("\t\t\t<div class=\"u" . $color .
          "\" style=\"top: " . ($yc + $doffsety) . "px; left: " .
          ($xc + $doffsetx) . "px;\">" . $unit["num"]  . "</div>\n");
			}
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief moveUnit
        \param[in] $sx source x coordinate
        \param[in] $sy source y coordinate
        \param[in] $tx target x coordinate
        \param[in] $ty target y coordinate
        \param[in] $num number of units to move
        
        Move units
     */
    /*-----------------------------------------------------------------------*/
    function moveUnit($sx, $sy, $tx, $ty, $num, $userid)
    {
      if($num < 1 || $sx>=MAP_SIZE_X || $tx>=MAP_SIZE_X ||
          $sy>=MAP_SIZE_Y || $ty>=MAP_SIZE_Y)
        return;
        
      if($sx+1 != $tx && $sx-1 != $tx && $sx != $tx)
        return;
      if($sy+1 != $ty && $sy-1 != $ty && $sy != $ty)
        return;
                                                               /* move units */
      $this->db->moveUnits($sx, $sy, $tx, $ty, $num, $userid);
      
    }
	}
?>