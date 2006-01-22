<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PiSToL - PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */

	// set the include path for PiSToL relative to this script
	ini_set('include_path', "../../src:" . ini_get('include_path'));
	
	require_once "Pistol.class.php";
	$pistol = new Pistol();
	$pistol->render();
?>
