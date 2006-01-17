<?
	/**
	 * $Author: jallen $
	 * $LastChangedDate: 2006-01-16 10:51:46Z $
	 * $LastChangedRevision: 65 $
	 * $LastChangedBy: jallen $
	 * $HeadURL: svn+ssh://svn.dublinux.net/var/svn/pstl/main/src/index.php $
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
