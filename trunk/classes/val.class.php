<?php

/*---------------------------------------------------------------------------*/
/*! \file val.class.php
    \brief the data validation class
    \author Sebastian Schaetz (chairman@soa-world.de)
    
    The validation class provides methods to validate all sorts of 
    user input data
*/
/*---------------------------------------------------------------------------*/

	class val
	{
    /*-----------------------------------------------------------------------*/
    /*! \brief constructor
        
        Initializes object
     */
    /*-----------------------------------------------------------------------*/
		function __construct()
		{}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief destructor
        
        Destructs object
     */
    /*-----------------------------------------------------------------------*/
    function __destruct()
    {}
    
    /*-----------------------------------------------------------------------*/
    /*! \brief validatePositiveInteger
        \param[in] $value the value to be validated
        \param[in] $min a minimum value
        \param[in] $max a maximum value
        \return true if successfull, false otherwise
        
        Validates a positive integer with 
        optional minimum and maximum constraints
     */
    /*-----------------------------------------------------------------------*/ 
    function validatePositiveInteger($value, $min=0, $max=PHP_INT_MAX)
    {
      if(!ctype_digit($value))
        return false;
      if($value<$min || $value>$max)
        return false;
      return true;
    }
	}
?>