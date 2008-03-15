<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"
  xml:lang="de" lang="de">
	<head><title>Echelon</title></head>
	<body>
		<h2>Echelon Login:</h2>
		<form id="authentication" action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" 
			method="post">
		Username: <input type="text" name="username" value="<?php echo($username); ?>"/><br />
		Password: <input type="password" name="password" /><br />
		<input type="submit" value="Login" name="login" /><br />
		<?php echo($message); ?>
		</form>			
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
