<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	// set the include path for PSTL relative to this script
	ini_set('include_path', "../../src:" . ini_get('include_path'));
	
	require_once "PSTL.class.php";
	 
	class Country
	{
		var $code;
		var $name;
		
		function Country($code, $name)
		{
			$this->code = $code;
			$this->name = $name;
		}
	}

	/*
	 * Create a new PSTL instance. The template we want to use is called "basic"
	 * The PSTL will load the template from a file named basic.pstl.xml
	 * located in the same directory as the script	
	 * 
	 */
	$pstl = new PSTL("basic");
	
	$pstl->setVar("countries", 
		array(
			new Country("US", "United States"), 
			new Country("IE", "Ireland"), 
			new Country("DE", "Germany"), 
			new Country("IT", "Italy"), 
			new Country("UK", "United Kingdom")));

	$pstl->render();
?>
