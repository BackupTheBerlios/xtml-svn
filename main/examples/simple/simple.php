<?
	/**
	 * $Author:jallen $
	 * $LastChangedDate:2006-01-21 19:19:13Z $
	 * $LastChangedRevision:92 $
	 * $LastChangedBy:jallen $
	 * $HeadURL:svn+ssh://svn.classesarecode.net/var/svn/pstl/main/examples/simple/basic.php $
	 * 
	 * PiSToL - The PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */

	// set the include path for PiSToL relative to this script
	ini_set('include_path', "../../src:" . ini_get('include_path'));
	
	require_once "Pistol.class.php";
	 
	class Language
	{
		var $name;
		var $description;
		
		function Language($name, $description)
		{
			$this->name = $name;
			$this->description = $description;
		}
	}

	/*
	 * Create a new Pistol instance. The template we want to use is called "simple"
	 * Pistol will load the template from the file simple.pistol.xml
	 * located in the same directory as the script	
	 * 
	 */
	$pistol = new Pistol("simple");

	$a = array(
		new Language("C", "A high level procedural language, that can still be used for programming the bare metal"),
		new Language("C++", "An extension of the original C language, adding object oriented features"),
		new Language("Java", "A modern object pure oriented language"),
		new Language("PHP", "A procedural web site scripting language , with object oriented features"));

	$pistol->setVar("languages", $a);

	$pistol->render();
?>
