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
    /*! \brief moveUnits
        \param[in] $sx source x coordinate
        \param[in] $sy source y coordinate
        \param[in] $tx target x coordinate
        \param[in] $ty target y coordinate
        \param[in] $num number of units to move
        \param[in] $userid the userid
        \return true if successfull, false otherwise
        
        Moves number of untis from source(x,y) to target(x,y)
        A transaction ensures data integrity
     */
    /*-----------------------------------------------------------------------*/  
    function moveUnits($sx, $sy, $tx, $ty, $num, $userid)
    {
      mysql_query("BEGIN", $this->link);
      
                                  /* check if units exist and belongs to user*/
      $sql = sprintf("SELECT num, x, y, unitid FROM %s
        WHERE x = %s AND y = %s AND userid = %s;", 
        DB_UNITS, $sx, $sy, $userid);
      $result = mysql_query($sql, $this->link);
      
      if(!$result)
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
                                 /* check if enought units available to move */
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      if($num > $row['num'])
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
      
                                            /* check if target field is free */
      $sql = sprintf("SELECT id, userid FROM %s WHERE x = %s AND y = %s;",
        DB_UNITS, $tx, $ty);
      $result = mysql_query($sql, $this->link);
      
      if(!$result)
      {
        mysql_query("ROLLBACK", $this->link);
				return false;
      }
      
                                                  /* calculate the new field */
      if(mysql_num_rows($result) == 1)
      {                                              
                                    /* check if target field belongs to user */        
        $row2 = mysql_fetch_array($result, MYSQL_ASSOC);
        if($row2['userid'] != $userid)
        {
          mysql_query("ROLLBACK", $this->link);
          return false;
        }
                                                                  /* update */
        $sql = sprintf("UPDATE %s SET num = num + %s WHERE x = %s AND y = %s;",
          DB_UNITS, $num, $tx, $ty);
        mysql_query($sql, $this->link);
        if(!$result)
        {
          mysql_query("ROLLBACK", $this->link);
          return false;
        }
      }
      else
      {
                                                                   /* insert */
        $sql = sprintf("INSERT INTO %s (num, userid, unitid, x, y)
          VALUES(%s, %s, 1, %s, %s);", DB_UNITS, $num, $userid, $tx, $ty);
        mysql_query($sql, $this->link);
        if(!$result)
        {
          mysql_query("ROLLBACK", $this->link);
          return false;
        }                                                                
      }
                                                  /* calculate the old field */
      if($row['num'] > $num)
      {
                                                                   /* update */
        $sql = sprintf("UPDATE %s SET num = num - %s WHERE x = %s AND y = %s;",
          DB_UNITS, $num, $sx, $sy);
        mysql_query($sql, $this->link);
        if(!$result)
        {
          mysql_query("ROLLBACK", $this->link);
          return false;
        }
      }
      else if($row['unitid'] == 0)
      {
        $sql = sprintf("UPDATE %s SET num = 0 WHERE x = %s AND y = %s;",
          DB_UNITS, $sx, $sy);
        mysql_query($sql, $this->link);
        if(!$result)
        {
          mysql_query("ROLLBACK", $this->link);
          return false;
        }
      }
      else
      {
                                                                   /* delete */
        $sql = sprintf("DELETE FROM %s WHERE x = %s AND y = %s;", 
          DB_UNITS, $sx, $sy);
        mysql_query($sql, $this->link);
        if(!$result)
        {
          mysql_query("ROLLBACK", $this->link);
          return false;
        }                                                                
      }
      mysql_query("COMMIT", $this->link);
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