<?php

/*---------------------------------------------------------------------------*/
/*! \file db.class.php
    \brief the databse connector class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The db class connects to the database and provides functions to access
    and modify the database.
*/
/*---------------------------------------------------------------------------*/

 	class db
 	{

	 private $link;                                     /*!< the database link */

    /*-----------------------------------------------------------------------*/
    /*! \brief constructor
        \throws Exception Unable to connect do database
        
        Connects to database
     */
    /*-----------------------------------------------------------------------*/
		function __construct()
		{
			$this->link = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);     
			if(!$this->link) 
        throw new Exception("Unable to connect do database");
			if(!mysql_select_db(MYSQL_DATABASE, $this->link))
				throw new Exception("Unable to select database");
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief destructor
        \throws Exception Unable to close database connection
        
        Closes database connection
     */
    /*-----------------------------------------------------------------------*/
		function __destruct()
		{
			if(!mysql_close($this->link))
        throw new Exception("Unable to close database connection");
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief authenticateUser
        \param[in] $username the username
        \param[in] $passwordhash the passwordhash
        \return true if user could be authenticated, false if not
        
        Authenticates user
     */
    /*-----------------------------------------------------------------------*/
		function authenticateUser($username, $passwordhash)
		{
			$sql = sprintf("SELECT 1 FROM %s WHERE username = '%s' 
				AND password = '%s';", DB_USERS, $username, $passwordhash);
			$result = mysql_query($sql, $this->link);

			if (!$result) 
				return false;
			if(mysql_num_rows($result))
				return true;
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUserId
        \param[in] $username the username
        \param[in] $passwordhash the passwordhash
        \return userid or -1 on error
        
        Return the userid
     */
    /*-----------------------------------------------------------------------*/
		function getUserId($username, $passwordhash)
		{
			$sql = sprintf("SELECT id FROM %s WHERE username = '%s' 
				AND password = '%s';", DB_USERS, $username, $passwordhash);
			$result = mysql_query($sql, $this->link);

			if (!$result) 
				return -1;
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
      return $row['id'];
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUserScore
        \param[in] $userid the userid
        \return score or 0 on error
        
        Returns the user score
     */
    /*-----------------------------------------------------------------------*/
		function getUserScore($userid)
		{
			$sql = sprintf("SELECT score FROM %s WHERE id = '%s'",
				DB_USERS, $userid);
			$result = mysql_query($sql, $this->link);

			if (!$result) 
				return 0;
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
      return $row['score'];
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUserInfo
        \param[in] $userid the userid
        \return score or 0 on error
        
        Returns user informations
     */
    /*-----------------------------------------------------------------------*/
		function getUserInfo($userid)
		{
			$sql = sprintf("SELECT score, bunkers FROM %s WHERE id = '%s'",
				DB_USERS, $userid);
			$result = mysql_query($sql, $this->link);

			if (!$result) 
				return 0;
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
      return $row;
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUserCoords
        \param[in] $userid the userid
        \return the user coordinates or 0
        
        Returns the user coordinates
     */
    /*-----------------------------------------------------------------------*/
		function getUserCoords($userid)
		{
			$sql = sprintf("SELECT x, y FROM %s WHERE id = '%s'",
				DB_USERS, $userid);
			$result = mysql_query($sql, $this->link);

			if (!$result) 
				return 0;
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
      return $row;
		}

    /*-----------------------------------------------------------------------*/
    /*! \brief getMap
        \param[in] $X1 from x coordinate
        \param[in] $X2 to x coordinate
        \param[in] $Y1 from y coordinate
        \param[in] $Y2 to y coordinate
        \return array containing map information
        
        Gets map information
     */
    /*-----------------------------------------------------------------------*/
		function getMap($X1, $X2, $Y1, $Y2)
		{
			$sql = sprintf("SELECT x, y, tile from %s 
				WHERE x>=%s AND x<=%s AND y>=%s AND y<=%s ORDER BY x, y ASC",
				DB_MAP, $X1, $X2, $Y1, $Y2);
			$result = mysql_query($sql, $this->link);
			if(!$result)
				return NULL;

			$i=0;
			for($i=0; $row = mysql_fetch_array($result, MYSQL_ASSOC); $i++)
   	 		$map[$i] = $row;  
			return $map;	
		}

    /*-----------------------------------------------------------------------*/
    /*! \brief getUnits
        \param[in] $X1 from x coordinate
        \param[in] $X2 to x coordinate
        \param[in] $Y1 from y coordinate
        \param[in] $Y2 to y coordinate
        \return array containing unit information
        
        Gets units information in defined area
     */
    /*-----------------------------------------------------------------------*/    
		function getUnits($X1, $X2, $Y1, $Y2)
		{
			$sql = sprintf("SELECT %s.id, %s.num, %s.userid, %s.unitid, %s.x,
        %s.y, %s.username FROM %s, %s 
				WHERE %s.x>=%s AND %s.x<=%s AND %s.y>=%s AND %s.y<=%s 
        AND %s.userid = %s.id ORDER BY %s.y, %s.x;",
				DB_UNITS, DB_UNITS, DB_UNITS, DB_UNITS, DB_UNITS, DB_UNITS, 
        DB_USERS, DB_USERS, DB_UNITS, 
        DB_UNITS, $X1, DB_UNITS, $X2, DB_UNITS, $Y1, DB_UNITS, $Y2,
        DB_UNITS, DB_USERS, DB_UNITS, DB_UNITS);
			$result = mysql_query($sql, $this->link);
			if(!$result)
				return NULL;
			$i=0;
			for($i=0; $row = mysql_fetch_array($result, MYSQL_ASSOC); $i++)
   	 		$map[$i] = $row;  
			return $map;	
		}

    /*-----------------------------------------------------------------------*/
    /*! \brief getUnitsMove
        \param[in] $sx source x coordinate
        \param[in] $sy source y coordinate
        \param[in] $tx target x coordinate
        \param[in] $ty target y coordinate
        \return units if successfull, false otherwise
        
        get the units at coordinates ($sx, $sy) and ($tx, $ty) for movement
     */
    /*-----------------------------------------------------------------------*/ 
    function getUnitsMove($sx, $sy, $tx, $ty)
    {
                                            /* fetch the two relevant fields */
      $sql = sprintf("SELECT num, x, y, unitid, userid, move FROM %s
        WHERE (x = %s AND y = %s) OR (x = %s AND y = %s);", 
        DB_UNITS, $sx, $sy, $tx, $ty);
      $result = mysql_query($sql, $this->link);
      
      if(!$result)
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
 
      for($i=0; $row = mysql_fetch_array($result, MYSQL_ASSOC); $i++)
   	 		$ret[$i] = $row;       
      return $ret;
    } 
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUnitLocation
        \param[in] $x x coordinate
        \param[in] $y y coordinate
        \return units if successfull, false otherwise
        
        get the units at coordinates ($sx, $sy) and ($tx, $ty) for movement
     */
    /*-----------------------------------------------------------------------*/ 
    function getUnitLocation($x, $y)
    {
                                            /* fetch the two relevant fields */
      $sql = sprintf("SELECT num, x, y, unitid, userid, move FROM %s
        WHERE (x = %s AND y = %s);", 
        DB_UNITS, $x, $y);
      $result = mysql_query($sql, $this->link);
      
      if(!$result)
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
 
      $row = mysql_fetch_array($result, MYSQL_ASSOC);    
      return $row;
    } 
    
    /*-----------------------------------------------------------------------*/
    /*! \brief buildBunker
        \param[in] $sx      source x coordinate
        \param[in] $sy      source y coordinate
        \param[in] $userid  the userid
        
        build a bunker and reduce bunker counter
     */
    /*-----------------------------------------------------------------------*/ 
    function buildBunker($x, $y, $userid)
    {
                                            /* fetch the two relevant fields */
      $sql = sprintf("UPDATE %s SET unitid = 0, move = 0 WHERE x = %s AND y = %s 
        AND unitid = 1 AND userid = %s;",
          DB_UNITS, $x, $y, $userid);
      $result = mysql_query($sql, $this->link);
           
      if(!$result)
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
   
      $sql = sprintf("UPDATE %s SET bunkers = bunkers - 1 WHERE id = %s;",
        DB_USERS, $userid);
      $result = mysql_query($sql, $this->link);
        
      if(!$result)
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
      return true;
    } 
    
    /*-----------------------------------------------------------------------*/
    /*! \brief updateUnit
        \param[in] $tx      target x coordinate
        \param[in] $ty      target y coordinate
        \param[in] $num     number of units to move
        \param[in] $userid  set the userid 
        \return true if successfull, false otherwise
        
        Update units by number in specified field
     */
    /*-----------------------------------------------------------------------*/ 
    function updateUnit($tx, $ty, $num, $userid = -1)
    {
      if($userid == -1)
      {
        $sql = sprintf("UPDATE %s SET num = num + %s WHERE x = %s AND y = %s;",
          DB_UNITS, $num, $tx, $ty);
      }
      else
      {
        $sql = sprintf("UPDATE %s SET num = num + %s, userid = %s
          WHERE x = %s AND y = %s;", DB_UNITS, $num, $userid, $tx, $ty);
      }
      $result = mysql_query($sql, $this->link);
      if(!$result)
        return false;
      return true;
    }
    
    /*-----------------------------------------------------------------------*/
    /*! \brief updateScore
        \param[in] $score   score added
        \param[in] $userid  the userid 
        \return true if successfull, false otherwise
        
        Update units by number in specified field
     */
    /*-----------------------------------------------------------------------*/ 
    function updateScore($score, $userid)
    {
      $sql = sprintf("UPDATE %s SET score = score + %s WHERE id = %s;",
        DB_USERS, $score, $userid);
      $result = mysql_query($sql, $this->link);
      if(!$result)
        return false;
      return true;
    }

    /*-----------------------------------------------------------------------*/
    /*! \brief deleteUnit
        \param[in] $x target x coordinate
        \param[in] $y target y coordinate
        \return true if successfull, false otherwise
        
        Delete units in specified field
     */
    /*-----------------------------------------------------------------------*/ 
    function deleteUnit($x, $y)
    {
      $sql = sprintf("DELETE FROM %s WHERE x = %s AND y = %s;", 
        DB_UNITS, $x, $y);
      $result = mysql_query($sql, $this->link);
      if(!$result)        
        return false;
      return true;
    }

    /*-----------------------------------------------------------------------*/
    /*! \brief insertUnit
        \param[in] $tx target x coordinate
        \param[in] $ty target y coordinate
        \param[in] $num number of units to move
        \param[in] $userid the userid
        \return true if successfull, false otherwise
        
        Insert num units for for user in specified field
     */
    /*-----------------------------------------------------------------------*/ 
    function insertUnit($tx, $ty, $num, $userid)
    {
      $sql = sprintf("INSERT INTO %s (num, userid, unitid, x, y)
        VALUES(%s, %s, 1, %s, %s);", DB_UNITS, $num, $userid, $tx, $ty);
      $result = mysql_query($sql, $this->link);
      if(!$result)        
        return false;
      return true;
    }
    
    /*-----------------------------------------------------------------------*/
    /*! \brief startTransaction
        
        Starts a transaction
     */
    /*-----------------------------------------------------------------------*/
		function startTransaction()
		{
      return mysql_query("BEGIN", $this->link);
		}

    /*-----------------------------------------------------------------------*/
    /*! \brief commitTransaction
        
        commit a transaction
     */
    /*-----------------------------------------------------------------------*/
		function commitTransaction()
		{
      return mysql_query("COMMIT", $this->link);
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief rollbackTransaction
        
        rollback a transaction
     */
    /*-----------------------------------------------------------------------*/
		function rollbackTransaction()
		{
      return mysql_query("ROLLBACK", $this->link);
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief addUnitsTick
        \param[in] $unitid  the unit that shall be added
        \param[in] $add     how many units shall be added
        \return true if successfull, false otherwise
        
        Adds to all units that match the unitid a number of units
     */
    /*-----------------------------------------------------------------------*/
    function addUnitsTick($unitid, $add)
    {
      $sql = sprintf("UPDATE %s SET num = num + %s WHERE unitid = %s;", 
        DB_UNITS, $add, $unitid);
      $result = mysql_query($sql, $this->link);
      if(!$result)        
        return false;
      return true;
    }
    
/*-----------------------------------------------------------------------*/
    /*! \brief calcBunkersTick
        \return true if successfull, false otherwise
        
        calculates if a user can build a new bunker and update the next score
     */
    /*-----------------------------------------------------------------------*/
    function calcBunkersTick()
    {
      $sql = sprintf("UPDATE %s SET bunkers = bunkers + 1, next = next*2
      WHERE score > next;", DB_USERS);
      $result = mysql_query($sql, $this->link);
      if(!$result)        
        return false;
      return true;
    }
    /*-----------------------------------------------------------------------*/
    /*! \brief generateMap
        \param[in] $X X dimension of map
        \param[in] $Y dimension of map
        \throws Exception unaple to execute query
        
        Generates a map of specified size
        Table is cleared before creation
     */
    /*-----------------------------------------------------------------------*/
		function generateMap($X, $Y)
		{
			$sql = sprintf("TRUNCATE TABLE %s;", DB_MAP);
			$result = mysql_query($sql, $this->link);
			if(!$result)
				throw new Exception("unaple to execute query: " . $sql);
			else
				echo("table cleared");

			echo("inserting " . $X . " rows and " . $T . " columns");
			for($x=0; $x<$X; $x++)
				for($y=0; $y<$Y; $y++)
				{
					$sql = sprintf("INSERT INTO %s (x , y) VALUES (%s, %s);",
						DB_MAP, $x, $y);
					$result = mysql_query($sql, $this->link);
					if(!$result)
						throw new Exception("unaple to execute query: " . $sql);
				}
		}
	}
?>