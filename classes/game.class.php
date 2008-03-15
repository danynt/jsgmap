<?php

/*---------------------------------------------------------------------------*/
/*! \file game.class.php
    \brief the game class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The game class contains the game logic
*/
/*---------------------------------------------------------------------------*/

	class game
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
    /*! \brief buildBunker
        \param[in] $sx      source x coordinate
        \param[in] $sy      source y coordinate
        \param[in] $userid  the userid
        
        build a bunker at specified location
     */
    /*-----------------------------------------------------------------------*/
    function buildBunker($sx, $sy, $userid)
    {
      $this->db->startTransaction(); 

                                          /* check if user can build bunkers */
      $info = $this->db->getUserInfo($userid); 
      if($info['bunkers'] <= 0)
      {
        $this->db->rollbackTransaction();
        return false;
      }
      
      
               /* check if user has units at location, if units belong to user
                   and if units are marines */
      $u = $this->db->getUnitLocation($sx, $sy);
      if(!$u || $u['userid'] != $userid 
        || $u['num'] <= 0 || $u['unitid'] != 1)
      {
        $this->db->rollbackTransaction();
        return false;
      }
                                                   /* otherwise build bunker */
      if(!$this->db->buildBunker($sx, $sy, $userid))
        return false;
                                                           /* commit changes */
      $this->db->commitTransaction();
      return;
    }
    
    /*-----------------------------------------------------------------------*/
    /*! \brief moveUnit
        \param[in] $sx      source x coordinate
        \param[in] $sy      source y coordinate
        \param[in] $tx      target x coordinate
        \param[in] $ty      target y coordinate
        \param[in] $num     number of units to move
        \param[in] $userid  the userid
        
        Move units
     */
    /*-----------------------------------------------------------------------*/
    function moveUnit($sx, $sy, $tx, $ty, $num, $userid)
    {
                                          /* check if coordinates make sense */
      if($num < 1 || $sx>=MAP_SIZE_X || $tx>=MAP_SIZE_X ||
          $sy>=MAP_SIZE_Y || $ty>=MAP_SIZE_Y)
        return;
        
      if($sx+1 != $tx && $sx-1 != $tx && $sx != $tx)
        return;
      if($sy+1 != $ty && $sy-1 != $ty && $sy != $ty)
        return;
        
      if(!$this->db->startTransaction()) return;  
                                                               /* move units */
      $units = $this->db->getUnitsMove($sx, $sy, $tx, $ty);
      if($units == false) 
        return;
      
      $a['num'] = -1;
      $d['num'] = -1;
      $a['userid'] = -1;
      $d['userid'] = -1;
      foreach ($units as $unit)
      {
        if($unit['x'] == $sx && $unit['y'] == $sy)
        {
          $a = $unit;
        }
        elseif($unit['x'] == $tx && $unit['y'] == $ty)
        {
          $d = $unit;
        }
      }
                                              /* no unit on the source field */
      if(!$a['num'] >= 1)
      {
        $this->db->rollbackTransaction();
        return;
      }
      
      if($d['userid'] == $userid)
      {
                                                        /* move to own field */
                                                      /* update target field */
        $num <= $a['num'] ? $num = $num : $num = $a['num'];
        if(!$this->db->updateUnit($tx, $ty, $num))
        {
          $this->db->rollbackTransaction();
          return false;
        }
                                               /* update/delete source field */
        if($num == $a['num'] && $a['move'] != 0)
        {
                                 /* we move all units and there is no bunker */
          if(!$this->db->deleteUnit($sx, $sy))                    
          {
            $this->db->rollbackTransaction();
            return false;
          }
        }
        else
        { 
                             /* we don't move all units or there is a bunker */
          if(!$this->db->updateUnit($sx, $sy, -$num))          
          {
            $this->db->rollbackTransaction();
            return false;
          }
        }
      }
      else if($d['userid'] >= 0)
      {
                                                      /* move to enemy field */
        if($num > $a['num']) $num = $a['num'];
        
        if($d['unitid'] == 0 && $d['num'] == 0)
        {
                                                   /* take over enemy bunker */
          if(!$this->db->updateUnit($tx, $ty, $num, $userid))          
          {
            $this->db->rollbackTransaction();
            return false;
          }
                                               /* update/delete source field */
          if($num == $a['num'] && $a['move'] != 0)
          {
                                 /* we move all units and there is no bunker */
            if(!$this->db->deleteUnit($sx, $sy))                    
            {
              $this->db->rollbackTransaction();
              return false;
            }
          }
          else
          { 
                             /* we don't move all units or there is a bunker */
            if(!$this->db->updateUnit($sx, $sy, -$num))          
            {
              $this->db->rollbackTransaction();
              return false;
            }
          }  
        }
        else
        {
          if($d['unitid'] == 2)       /* command centers may not be attacked */
            return;
            
          if($num > $a['num']) $num = $a['num'];
                                                          /* calculate fight */
          $ratio = ($num > $d['num'] ? $num/$d['num'] : 
            $d['num']/$num);
          $a['left'] = floor(($num  > $d['num'] ? 
            $num -$d['num']/($ratio+1.0)*2 : 
            $num -$d['num']));
          $d['left'] = floor(($d['num'] > $num  ? 
            $d['num']-$num /($ratio+1.0)*2 : 
            $d['num']-$num ));
            
          if($a['left'] < 0) $a['left'] = 0;
          if($d['left'] < 0) $d['left'] = 0;
          
          if($d['left'] == 0)
          {
                                /* take over enemy bunker or take over field */
            if($a['left'] == 0)
            {
              if(!$this->db->deleteUnit($tx, $ty))                    
              {
                $this->db->rollbackTransaction();
                return false;
              }
            }
            else
            {
              if(!$this->db->updateUnit($tx, $ty, 
                -$d['num'] + $a['left'], $userid))          
              {
                $this->db->rollbackTransaction();
                return false;
              }
            }
          }
          else
          {
                                                       /* reduce enemy field */
              if(!$this->db->updateUnit($tx, $ty, -$d['num'] + $d['left']))          
              {
                $this->db->rollbackTransaction();
                return false;
              }
          }
                                               /* update/delete source field */
          if($num == $a['num'] && $a['move'] != 0)
          {
                                 /* we move all units and there is no bunker */
            if(!$this->db->deleteUnit($sx, $sy))                    
            {
              $this->db->rollbackTransaction();
              return false;
            }
          }
          else
          { 
                             /* we don't move all units or there is a bunker */
            if(!$this->db->updateUnit($sx, $sy, -$num))          
            {
              $this->db->rollbackTransaction();
              return false;
            }
          }
                                                             /* update score */
          $this->db->updateScore($d['num'] - $d['left'], $a['userid']);
          $this->db->updateScore($num - $a['left'], $d['userid']);
        }
      }
      else
      {
                                                      /* move to empty field */
        if($num > $a['num']) $num = $a['num'];
        if(!$this->db->insertUnit($tx, $ty, $num, $userid))
        {
          $this->db->rollbackTransaction();
          return false;
        }
        
                                               /* update/delete source field */
        if($num == $a['num'] && $a['move'] != 0)
        {
                                 /* we move all units and there is no bunker */
          if(!$this->db->deleteUnit($sx, $sy))                    
          {
            $this->db->rollbackTransaction();
            return false;
          }
        }
        else
        { 
                             /* we don't move all units or there is a bunker */
          if(!$this->db->updateUnit($sx, $sy, -$num))          
          {
            $this->db->rollbackTransaction();
            return false;
          }
        }
      }
                                                           /* commit changes */
      $this->db->commitTransaction();
      return;
    }
    

	}
?>