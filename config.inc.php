<?php
	
	/* the config file */

	/* mysql database */
	define(MYSQL_HOST, 			"host"		);
	define(MYSQL_USER, 		 	"user"		);
	define(MYSQL_PASSWORD, 	"pw"	 		);
	define(MYSQL_DATABASE, 	"db"  		);

	/* database table names */
	define(DB_USERS, 				"users"			);
	define(DB_UNITS,			 	"units"			);
	define(DB_MAP,	 				"map"				);

	/* map specific configuration */  
  define(MAP_TILESIZE_X, 	  100				);
	define(MAP_TILESIZE_Y, 	  50				);

	define(MAP_NUMTILES_X,    8					);
	define(MAP_NUMTILES_Y,	  8			  	);	  
  
	define(MAP_TILESTART_X1,  -50				);
	define(MAP_TILESTART_X2,   0				);
	define(MAP_TILESTART_Y,   -25			  );



	define(MAP_INTERVAL_X,	  100				);
	define(MAP_INTERVAL_Y,	  25				);

	define(MAP_SIZE_X,			  100				);
	define(MAP_SIZE_Y,			  100				);

	/* unit configuration */
  
  /* unit array looks like this  
    untis => 
    {
      [unit id 0] => { XPOS0, YPOS0, STYLE0, XDIM0, YDIM0, DIGY0 }
      [unit id 1] => { XPOS1, YPOS1, STYLE1, XDIM1, YDIM1, DIGY0 }
    }
  */
  $bunker = array(33, 12, "bu", 30, 18, 30);
  $marine = array(42, 14, "ma", 12, 15, 28);
  
  $udef = array($bunker, $marine);
  
  /* digit array looks like this  
    num => 
    {
      [1 digit] => { DIGX1 }
      [2 digit] => { DIGX1 }
    }
  */
  $digits = array(0, 44, 40, 36, 34, 30, 23);

?>
