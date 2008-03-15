<?php

	/* main echelon script */

	require_once("config.inc.php");                     /* include config file */
	require_once("classes/db.class.php");
	require_once("classes/auth.class.php");

	$myDB = new db(); 											  /* establish database connection */
	$myAuth = new auth($myDB);   									        /* authenticate user */
	$authResult = $myAuth->authenticate();
  
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
      <script src="js/scriptaculous/scriptaculous.js" 
        type="text/javascript"></script>
      <script type="text/javascript" language="JavaScript">
      
      var mapCoordX = 20;
      var mapCoordY = 20;
      
      var selUnitX = -1;
      var selUnitY = -1;
      var selUnitId = -1;
    
      function updateMap()
      {
        $('unitnav').hide();
        new Ajax.Updater('map', 'api/getmap.php', 
          { method: 'get', parameters: { x: mapCoordX, y: mapCoordY }  });
      }     
      
      function hideNav()
      {
        $('unitnav').hide(); 
        selUnitX = -1;
        selUnitY = -1;
        selUnitId = -1;
      }
      
      function showNav()
      {
        $('unitnav').show(); 
      }
      
      function unitOver(posx, posy, user)
      {
        $("unitdesc").update("User: " + user + " - Coordinates: " + posx +
          " - " + posy);
      }
      
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
            top:  (posy + 32) + 'px', left: (posx - 0) + 'px'
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
            top:  (posy + 32) + 'px', left: (posx + 4) + 'px'
          });
        }
        
        $("unitnavnumbox").value = num;
        $("unitnavnumbox").focus();
        
        selUnitX = x;
        selUnitY = y;
        selUnitId = id;
        
        showNav();
      }
      
      function moveUnit(ptx, pty)
      {
        unitnum = $("unitnavnumbox").value;
        new Ajax.Request('api/moveunit.php', 
          {
            method: 'get',
            parameters: { sx: selUnitX, sy: selUnitY, 
              tx: ptx, ty: pty, num: unitnum},
            onSuccess: function() 
            {
              updateMap();
            }
          }
        );
      }
      
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

	<body style="background-color: #000000;" onLoad="updateMap(); hideNav();">
    <h1 style="text-align: center; margin-left: 
      auto; margin-right: auto; color: #ffffff;
      font-family: Helvetica, sans-serif; font-size: 24px;
      margin-bottom: 30px;">
      Second Prototype
    </h1>  

    <div style="width: 812px; height: 413px; 
      background-image: url(img/map/background.png); 
      position: relative; margin-left: auto; margin-right: auto;"> 
      
      <!-- navigation -->
      <a href="#" onClick="mapCoordY = mapCoordY - 1; updateMap(); 
        return false;">
        <div style="position: absolute; width: 100px; height: 60px; 
          z-index: 500; left: 50px; top: 20px; 
          background-image: url(img/nav/ul.png);">
        </div>
      </a>
      <a href="#" onClick="mapCoordX = mapCoordX + 1; updateMap(); 
        return false;">
        <div style="position: absolute; width: 100px; height: 60px; 
          z-index: 500; left: 672px; top: 20px; 
          background-image: url(img/nav/ur.png);">
        </div>
      </a>
      <a href="#" onClick="mapCoordX = mapCoordX - 1; updateMap(); 
        return false;">  
        <div style="position: absolute; width: 100px; height: 60px; 
          z-index: 500; left: 50px; top: 333px; 
          background-image: url(img/nav/dl.png);">
        </div>
      </a>
      <a href="#" onClick="mapCoordY = mapCoordY + 1; updateMap(); 
        return false;">  
        <div style="position: absolute; width: 100px; height: 60px;
          z-index: 500; left: 672px; top: 333px; 
          background-image: url(img/nav/dr.png);">
        </div>
      </a>
      
      <div id="unitnav">
        <a href="#" onClick="moveUnit(selUnitX, selUnitY-1); return false;"> 
          <div id="unitnavul" 
            style="position: absolute; width: 24px; height: 15px;
            top: 130px; left: 614px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/uls.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(selUnitX+1, selUnitY); return false;">
          <div id="unitnavur" 
            style="position: absolute; width: 24px; height: 15px;
            top: 130px; left: 675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/urs.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(selUnitX-1, selUnitY); return false;">
          <div id="unitnavdl" 
            style="position: absolute; width: 24px; height: 15px;
            top: 162px; left: 614px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/dls.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="moveUnit(selUnitX, selUnitY+1); return false;">
          <div id="unitnavdr" 
            style="position: absolute; width: 24px; height: 15px;
            top: 162px; left: 675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/drs.gif); z-index: 3;">
          </div>
        </a>
        <a href="#" onClick="hideNav(); return false;">
          <div id="unitnavx" 
            style="position: absolute; width: 14px; height: 14px;
            top: 162px; left: 675px; border: 0px solid #1e9000;
            margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
            background-image: url(img/nav/x.gif); z-index: 3;">
          </div>
        </a>
        <input type="text" value="" style="background-color: transparent;
          z-index: 3; position: absolute; font-size: 13px; text-align: center;
          font-family: verdana; color: #1e9000; width: 34px; 
          border: 0px solid #1e9000; border-bottom: 2px dotted #1e9000;
          top: 170px; left: 640px;"
          maxlength="4" id="unitnavnumbox"/>
      </div>
      <div id="unitdesc" 
          style="position: absolute; width: 400px; height: 24px;
          top: 425px; left: 200px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          Move the mouse over a unit to get information
        </div>
        <div id="desc1" 
          style="position: absolute; width: 400px; height: 24px;
          top: 450px; left: 200px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          Click on units to move them
        </div>
        <div id="desc2" 
          style="position: absolute; width: 400px; height: 24px;
          top: 475px; left: 200px; border: 1px solid #1e9000;
          margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;
          z-index: 3; color: #1e9000; padding-left: 4px;
          font-family: Helvetica, sans-serif; font-size: 18px;
          vertical-align: middle; text-align: center;">
          There are marines 
          <img style="vertical-align: middle;" src="img/map/marineyellow.png"/> 
          and bunkers 
          <img style="vertical-align: middle;" src="img/map/bunkyellow.png"/>
        </div>
        <div id="desc2" 
          style="position: absolute; width: 400px; height: 24px;
          top: 500px; left: 200px; border: 1px solid #1e9000;
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
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    var pageTracker = _gat._getTracker("UA-830634-1");
    pageTracker._initData();
    pageTracker._trackPageview();
    </script>
	</body>
</html>
