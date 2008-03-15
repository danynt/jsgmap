<?php

	/* main echelon script */

	require_once("config.inc.php");                     /* include config file */
	require_once("classes/db.class.php");
	require_once("classes/auth.class.php");
  require_once("classes/ui.class.php");

	$myDB = new db(); 											  /* establish database connection */
	$myAuth = new auth($myDB);   									        /* authenticate user */
	$authResult = $myAuth->authenticate();
  $myUi = new ui($myDB);
  
                          /* if authentication failed, output the login page */
  if($authResult < 0)
    $myAuth->echoLogin();
    

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"
  xml:lang="de" lang="de">
	<head>
		<title>Echelon Map</title>
      <script src="js/prototype-1.6.0.2.js" type="text/javascript"></script>    
      <script type="text/javascript" language="JavaScript">
      
      /*---------------------------------------------------------------------*/
      <?php
        $myUi->getUserCoords($myAuth->getUserId());
      ?>
        
      var mapCoordX = userCoordX;                  /*!< current x coordinate */
      var mapCoordY = userCoordY;                  /*!< current y coordinate */
      
      var selUnitX = -1;                     /*!< selected unit x coordinate */
      var selUnitY = -1;                     /*!< selected unit y coordinate */
      var selUnitId = -1;                              /*!< selected unit id */
      
      var bunkerAvailable = true;                    /*!< can we build a bunker? */

      /*---------------------------------------------------------------------*/
       /*! \brief updateMap

          reloads the map with new coordinates
        */
      /*---------------------------------------------------------------------*/
      function updateMap()
      {
        $('unitnav').hide();
        new Ajax.Updater('map', 'api/getmap.php', 
          { method: 'get', parameters: { x: mapCoordX, y: mapCoordY },
            evalScripts: true});
      }  
      
      /*---------------------------------------------------------------------*/
       /*! \brief hideNav

          hide the navigation
        */
      /*---------------------------------------------------------------------*/
      function hideNav()
      {
        $('unitnav').hide(); 
        selUnitX = -1;
        selUnitY = -1;
        selUnitId = -1;
        $("unitnavbunk").hide();
      }

      /*---------------------------------------------------------------------*/
      /*! \brief showNav
          
          show the navigation
       */
      /*---------------------------------------------------------------------*/
      function showNav()
      {
        $('unitnav').show(); 
      }

      /*---------------------------------------------------------------------*/
      /*! \brief setStatusDisplay
          \param[in]  username  name of user
          \param[in]  x         current map x position
          \param[in]  y         current map y position
          \param[in]  score     score of user
              
          executed after the map is reloaded,
          updates the status display fields
       */
      /*---------------------------------------------------------------------*/
      function setStatusDisplay(username, x, y, score, bunkerAvail)
      {
        $('statusdisplay1').update("<em style=\"color: #fff900;\">" +
          username + "</em> (" + userCoordX + "-" + userCoordY + 
          ") | Map: " + x + "-" + y); 
        $('statusdisplay3').update("Score: <em style=\"color: " +
        "#fff900;\">" + score + "</em>");

        if(bunkerAvail==true)
        {
          bunkerAvailable = true;
          $('statusdisplay2').show();
        }
        else
        {
          $('statusdisplay2').hide();
          bunkerAvailable = false;
        }
      }
      
      /*---------------------------------------------------------------------*/
      /*! \brief unitOver
          \param[in]  posx      x position coordinate of unit
          \param[in]  posy      y position coordiante of unit
          \param[in]  user      username the unit belongs to
              
          fires when the mouse moves over a unit
          updates an information display
       */
      /*---------------------------------------------------------------------*/
      function unitOver(posx, posy, user)
      {
        $("unitdesc").update("User: " + user + " - Coordinates: " + posx +
          " - " + posy);
      }
      
      /*---------------------------------------------------------------------*/
      /*! \brief unitClick
          \param[in]  id      the unit id
          \param[in]  num     number of unit
          \param[in]  own     specifies if unit belongs to user or not
          \param[in]  type    type of unit (bunker or marine)
          \param[in]  x       x coordinate of unit
          \param[in]  y       y coordinate of unit
          \param[in]  posx    x position of the unit image inside the map div
          \param[in]  posy    y position of the unit image inside the map div
          \param[in]  user    the name of the user the unit belongs to
          
          handles unit onclick
          positions the unit movement keys and set the selected unit
       */
      /*---------------------------------------------------------------------*/
      function unitClick(id, num, own, type, x, y, posx, posy, user)
      {
        if(num < 1)
          return;
        hideNav();
        if(own == false)
          return;
        
        if(type == 'ma')
        {
          $("unitnavul").setStyle({
            top:  (posy - 9) + 'px', left: (posx - 28) + 'px'
          });
          
          $("unitnavur").setStyle({
            top:  (posy - 9) + 'px', left: (posx + 33) + 'px'
          });
          showNav()
          
          $("unitnavdl").setStyle({
            top:  (posy + 23) + 'px', left: (posx - 28) + 'px'
          });
          
          $("unitnavdr").setStyle({
            top:  (posy + 23) + 'px', left: (posx + 33) + 'px'
          });
          
          $("unitnavx").setStyle({
            top:  (posy - 10) + 'px', left: (posx + 60) + 'px'
          });
          
          $("unitnavnumbox").setStyle({
            top:  (posy + 32) + 'px', left: (posx - 4) + 'px'
          });
          
          if(bunkerAvailable == true)
            $("unitnavbunk").show();
            
          $("unitnavbunk").setStyle({
            top:  (posy + 8) + 'px', left: (posx + 55) + 'px'
          });
        }
        if(type == 'bu')
        {
          $("unitnavul").setStyle({
            top:  (posy - 9) + 'px', left: (posx - 20) + 'px'
          });
          
          $("unitnavur").setStyle({
            top:  (posy - 9) + 'px', left: (posx + 41) + 'px'
          });
          showNav()
          
          $("unitnavdl").setStyle({
            top:  (posy + 23) + 'px', left: (posx - 20) + 'px'
          });
          
          $("unitnavdr").setStyle({
            top:  (posy + 23) + 'px', left: (posx + 41) + 'px'
          });
          
          $("unitnavx").setStyle({
            top:  (posy - 10) + 'px', left: (posx + 68) + 'px'
          });
          $("unitnavnumbox").setStyle({
            top:  (posy + 32) + 'px', left: (posx + 5) + 'px'
          });       
        }
        
        if(type == 'co')
        {
          $("unitnavul").setStyle({
            top:  (posy + 2) + 'px', left: (posx - 12) + 'px'
          });
          
          $("unitnavur").setStyle({
            top:  (posy + 2) + 'px', left: (posx + 49) + 'px'
          });
          showNav()
          
          $("unitnavdl").setStyle({
            top:  (posy + 34) + 'px', left: (posx - 12) + 'px'
          });
          
          $("unitnavdr").setStyle({
            top:  (posy + 34) + 'px', left: (posx + 49) + 'px'
          });
          
          $("unitnavx").setStyle({
            top:  (posy +1) + 'px', left: (posx + 76) + 'px'
          });
          
          $("unitnavnumbox").setStyle({
            top:  (posy + 45) + 'px', left: (posx + 13) + 'px'
          });
        }
        
        $("unitnavnumbox").value = num;
        $("unitnavnumbox").focus();
        
        selUnitX = x;
        selUnitY = y;
        selUnitId = id;
        
        showNav();
      }
      
      /*---------------------------------------------------------------------*/
      /*! \brief moveUnit
          \param[in]  ptx      x coordinate of target field
          \param[in]  pty      y coordinate of target field
              
          moves the selected unit
          the page is reloaded automatically
       */
      /*---------------------------------------------------------------------*/
      function moveUnit(ptx, pty, build)
      {
        var unitnum = $('unitnavnumbox').value;
        
        if(build != 1)
          build=0;
        
        new Ajax.Updater('map', 'api/moveunit.php', 
          { method: 'get', evalScripts: true,
            parameters: { sx: selUnitX, sy: selUnitY, 
              x: mapCoordX, y: mapCoordY,
              tx: ptx, ty: pty, num: unitnum, b: build}
          });       
        hideNav()
      }
      
      /*---------------------------------------------------------------------*/
      /*! \brief moveByKeypress
          \param[in] event    the fired event
              
          executes whenever an event is fired
          catches the arrow keys - scrolls the map
       */
      /*---------------------------------------------------------------------*/
      function moveByKeypress(event)
      { 
       	if(event.keyCode == Event.KEY_ESC)
        {
          hideNav();
          return;
      	} 
        
        if(selUnitId != -1)
          return;
          
      	if(event.keyCode == Event.KEY_UP)
        {
      		mapCoordY = mapCoordY - 1; 
          updateMap(); 
      	} 
        else if(event.keyCode == Event.KEY_DOWN)
        {
      		mapCoordY = mapCoordY + 1; 
          updateMap(); 
      	} 
        else if(event.keyCode == Event.KEY_RIGHT)
        {
      		mapCoordX = mapCoordX + 1; 
          updateMap(); 
      	} 
        else if(event.keyCode == Event.KEY_LEFT)
        {
      		mapCoordX = mapCoordX - 1; 
          updateMap(); 
      	}
      }

      /*---------------------------------------------------------------------*/
      /*! \brief onPageLoad

          initializes page, registers event handlers
       */
      /*---------------------------------------------------------------------*/
      function myOnPageLoad()
      {
         hideNav(); 
         Event.observe(
        	 document, 
        	 'keydown', 
        	 moveByKeypress
         );
         
        updateMap(); 
      }
     
      /*---------------------------------------------------------------------*/
      
    </script>
    <style type="text/css">
      div		{
				background-repeat: no-repeat;
			}
      div.t0
			{
				width: 96px; 
				height: 49px; 
				background-image: url(img/map/tile001.png);
				position: absolute;
				z-index: 1;
        color: white;
        font-size: 10px;
        text-align: center;
			}
      div.t1
			{
				width: 96px; 
				height: 49px; 
				background-image: url(img/map/tile002.png);
				position: absolute;
				z-index: 1;
			}
      div.mar
			{
				width: 12px; 
				height: 14px; 
				background-image: url(img/map/marinered.png);
				position: absolute;
        cursor: url(img/cur/sc.cur), auto;
        z-index: 500;
			}
      div.may
			{
				width: 12px; 
				height: 14px; 
				background-image: url(img/map/marineyellow.png);
				position: absolute;
        cursor: url(img/cur/sc.cur), auto;
        z-index: 500;
			}
      div.bur
			{
				width: 30px; 
				height: 18px; 
				background-image: url(img/map/bunkred.png);
				position: absolute;
        cursor: url(img/cur/sc.cur), auto;
        z-index: 500;
			}
      div.buy
			{
				width: 30px; 
				height: 18px; 
				background-image: url(img/map/bunkyellow.png);
				position: absolute;
        cursor: url(img/cur/sc.cur), auto;
        z-index: 500;
			}
      div.cor
			{
				width: 44px; 
				height: 35px; 
				background-image: url(img/map/commandred.gif);
				position: absolute;
        cursor: url(img/cur/sc.cur), auto;
        z-index: 500;
			}
      div.coy
			{
				width: 44px; 
				height: 35px; 
				background-image: url(img/map/commandyellow.gif);
				position: absolute;
        cursor: url(img/cur/sc.cur), auto;
        z-index: 500;
			}
      div.ur
			{
				font-size: 11px;
				color: #ff0000;
				position: absolute;
				font-family: courier new, monospace; 
        z-index: 500;
			}
      div.uy
			{
				font-size: 11px;
				color: #fff900;
				position: absolute;
				font-family: courier new, monospace;
        z-index: 500;
			}

      
		</style>
	</head>

	<body style="background-color: #000000;">
    <h1 style="text-align: center; margin-left: 
      auto; margin-right: auto; color: #ffffff;
      font-family: Helvetica, sans-serif; font-size: 24px;
      margin-bottom: 30px; padding: 0px 0px 0px 0px; 
      margin: 0px 0px 1em 0px;">
      <img src="img/logo.png" style="border: 0px solid #1e9000;" />
    </h1>  
    <div style="width: 812px; height: 30px; 
      margin-left: auto; margin-right: auto; position: relative;">
      <div id="statusdisplay1" 
        style="position: absolute; width: 300px; height: 24px;
        top: 0px; left: 0px; border: 1px solid #1e9000;
        margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
        z-index: 3; color: #1e9000; padding-left: 4px;
        font-family: Helvetica, sans-serif; font-size: 18px;
        vertical-align: middle; text-align: center;"></div>
      <div id="statusdisplay2" 
        style="position: absolute; width: 150px; height: 24px;
        top: 0px; left: 330px; border: 1px solid #1e9000;
        margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
        z-index: 3; color: #1e9000; padding-left: 4px;
        font-family: Helvetica, sans-serif; font-size: 18px;
        vertical-align: middle; text-align: center;">
        <em style="color: #ff0000;">Build a bunker</em>
      </div>
      <div id="statusdisplay3" 
        style="position: absolute; width: 300px; height: 24px;
        top: 0px; left: 512px; border: 1px solid #1e9000;
        margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
        z-index: 3; color: #1e9000; padding-left: 4px;
        font-family: Helvetica, sans-serif; font-size: 18px;
        vertical-align: middle; text-align: center;"></div>
    </div>
    <div style="width: 812px; height: 413px; 
      background-image: url(img/map/background.png); 
      position: relative; margin-left: auto; margin-right: auto;"> 
      <!-- navigation -->
      <a href="#" onClick="mapCoordY = mapCoordY - 1; updateMap(); 
        return false;">
        <div style="position: absolute; width: 100px; height: 60px; 
          z-index: 500; left: 100px; top: 45px; 
          background-image: url(img/nav/ul.gif);">
        </div>
      </a>
      <a href="#" onClick="mapCoordX = mapCoordX + 1; updateMap(); 
        return false;">
        <div style="position: absolute; width: 100px; height: 60px; 
          z-index: 500; left: 612px; top: 45px; 
          background-image: url(img/nav/ur.gif);">
        </div>
      </a>
      <a href="#" onClick="mapCoordX = mapCoordX - 1; updateMap(); 
        return false;">  
        <div style="position: absolute; width: 100px; height: 60px; 
          z-index: 500; left: 100px; top: 300px; 
          background-image: url(img/nav/dl.gif);">
        </div>
      </a>
      <a href="#" onClick="mapCoordY = mapCoordY + 1; updateMap(); 
        return false;">  
        <div style="position: absolute; width: 100px; height: 60px;
          z-index: 480; left: 612px; top: 300px; 
          background-image: url(img/nav/dr.gif);">
        </div>
      </a>
      
      <div id="unitnav">
        <a href="#" onClick="moveUnit(selUnitX, selUnitY-1); return false;"> 
          <div id="unitnavul" 
            style="position: absolute; width: 24px; height: 15px;
            top: -130px; left: -614px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/uls.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(selUnitX+1, selUnitY); return false;">
          <div id="unitnavur" 
            style="position: absolute; width: 24px; height: 15px;
            top: -130px; left: -675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/urs.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(selUnitX-1, selUnitY); return false;">
          <div id="unitnavdl" 
            style="position: absolute; width: 24px; height: 15px;
            top: -162px; left: -614px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/dls.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(selUnitX, selUnitY+1); return false;">
          <div id="unitnavdr" 
            style="position: absolute; width: 24px; height: 15px;
            top: -162px; left: -675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/drs.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="hideNav(); return false;">
          <div id="unitnavx" 
            style="position: absolute; width: 14px; height: 14px;
            top: -162px; left: -675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/x.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(0, 0, 1); return false;">
          <div id="unitnavbunk" 
            style="position: absolute; width: 22px; height: 15px;
            top: -162px; left: -675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/bunk.gif); z-index: 3;">
          </div>
        </a>
        <input type="text" value="" style="background-color: transparent;
          z-index: 3; position: absolute; font-size: 13px; text-align: center;
          font-family: verdana; color: #1e9000; width: 34px; 
          border: 0px solid #1e9000; border-bottom: 2px dotted #1e9000;
          top: -170px; left: -640px;"
          maxlength="4" id="unitnavnumbox"/>
      </div>
      <div id="unitdesc" 
          style="position: absolute; width: 397px; height: 24px;
          top: 425px; left: 0px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          Move the mouse over a unit to get information
        </div>
        <div id="desc1" 
          style="position: absolute; width: 398px; height: 24px;
          top: 425px; left: 402px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          Click on units to move them
        </div>
        <div id="desc2" 
          style="position: absolute; width: 397px; height: 24px;
          top: 450px; left: 0px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          There are marines 
          <img style="vertical-align: middle;" src="img/map/marineyellow.png"/> 
          and bunkers 
          <img style="vertical-align: middle;" src="img/map/bunkyellow.png"/>
        </div>
        <div id="desc3" 
          style="position: absolute; width: 398px; height: 24px;
          top: 450px; left: 402px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          <em style="color: #fff900;">Yellow</em> units belong to you, 
          <em style="color: #ff0000;">red</em> units are enemies
        </div>
      
      <div id="map" style="width: 800px; height: 400px; 
  			border: 0px solid #466374; position: relative;
         z-index: 2; top: 4px; left: 8px; color: white;">
      </div>
    </div>
    
    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? 
    "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + 
    "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    var pageTracker = _gat._getTracker("UA-830634-1");
    pageTracker._initData();
    pageTracker._trackPageview();
    myOnPageLoad();
    </script>
	</body>
</html>
