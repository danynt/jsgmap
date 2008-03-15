<?php

/*---------------------------------------------------------------------------*/
/*! \file auth.class.php
    \brief the authentication class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The auth class authenticates the user or prints the login page
*/
/*---------------------------------------------------------------------------*/

	class auth
	{
		private $db; 																	  /*!< the database object */
    private $username;                            /*!< the provided username */
    private $passwordhash;                            /*!< the password hash */
    private $userid;                                         /*!< the userid */
    private $message;      /* message that can be returned on the login page */
    
    /*-----------------------------------------------------------------------*/
    /*! \brief constructor
        \param[in] $db reference to database connector
        
        Starts the session, sets the database connector reference
        and initialzes the object
     */
    /*-----------------------------------------------------------------------*/
		function __construct(&$db)
		{
			session_start();
			$this->db = $db;
      $this->username = "";
      $this->message = "";
      $this->passwordhash = "";
      $this->userid = -1;
      $this->login = true;
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief authenticate the user
        \return 1 if a new session was created, 
                0 if a session was continued
               -1 if login failed
        
        Validates the user against the user table in the database
        Username and passwort is either taken from POST (via login form)
        or from the session.
        
        If the user can not be authenticated, the login page is returned
        and the script exits.
     */
    /*-----------------------------------------------------------------------*/
		function authenticate($login = true)
		{
      $this->login = $login;
      if(isset($_GET["demouser"]))
      {
				$this->username = "demouser";
				$this->password = "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3";		
				$_SESSION["username"] = $this->username;
				$_SESSION["password"] = "test";
        return 0;
      }
      if(isset($_GET["logout"]))
      {
        	$this->message = "Logged out.";
					session_destroy();
					return -1;
      }
			else if(isset($_POST["login"])) 				  				    /* login via form */
			{
				if(!ctype_alnum($_POST["username"])) 				  			/* validate data */
				{
					$this->message = "Invalid username.";
					session_destroy();
					return -1;
				}
			
				if($_POST["username"] == "" && $_POST["password"] == "")
				{
					$$this->message = "Please enter your username and password.";
					return -1;
				}
				else if($_POST["username"] == "")
				{
					$this->message = "Please enter your username.";
					return -1;
				}
				else if($_POST["password"] == "")
				{
					$this->message = "Please enter your password.";
          $this->username = $_POST["username"];
					return -1;
				}
				$this->username = $_POST["username"];
				$this->password = $_POST["password"];		
				$_SESSION["username"] = $this->username;
				$_SESSION["password"] = $this->password;
				$new_session = 1;
			}
			else if(isset($_SESSION["username"])) 	  	/* or userdata via session */
			{
				if(!ctype_alnum($_SESSION["username"])) 				    /* validate data */
				{
					$this->message = "Invalid username.";
					session_destroy();
					return -1;
				}
				$this->username = $_SESSION["username"];
				$this->password = $_SESSION["password"];	
				$new_session = 0;			
			}
			else 													    /* no login information can be found */
			{
				$this->echoLogin();
				session_destroy();
				return -1;
			}	
			
			$this->password = sha1($this->password); 								/* calculate password hash */
			if($this->db->authenticateUser($this->username, $this->password))
			{
        $this->username = $this->username;
        $this->passwordhash = $this->password;
				return $new_session;
			}
      
			$this->message = "Wrong username or password.";
      $this->username = $this->username;
			session_destroy();
			return -1;
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief getUserId
        \return userid or -1 on error
        
        Return the userid
     */
    /*-----------------------------------------------------------------------*/
		function getUserId()
		{
      if($this->userid == -1)
        $this->userid = 
          $this->db->getUserId($this->username, $this->passwordhash);
      return $this->userid;
		}

    /*-----------------------------------------------------------------------*/
    /*! \brief echoLogin
        \param[in] $message
        \param[in] $this->username
        
        Outputs the login page with an optional warning message.
        The username can be prefilled with previous input if desired.
     */
    /*-----------------------------------------------------------------------*/
		function echoLogin()
		{
      if($this->login)
      {
        $message = $this->message;
        $this->username = $this->username;
  			require_once("html/login.php");
      }
      else
      {
        $this->echoForbidden();
      }
      exit;              /* make sure script we end the script at this point */
		}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief echoForbidden
        
        Outputs a 403 forbidden message and ends the script
      */
    /*-----------------------------------------------------------------------*/
    function echoForbidden()
    {
      echo("<div style=\"position: absolute; top: 200px; 
            color: red; font-size: 20px; background-color:black;\">
            Please make sure your browser accepts cookies!</div>");
      exit();
    }
	}
?>