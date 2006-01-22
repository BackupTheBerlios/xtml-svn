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

	require_once "Pistol.class.php";
	 
	/*
	 * Create a new Pistol instance.	
	 * 
	 */
	$pistol = new Pistol($_REQUEST['script']);
	$pistol->preview();
?>
