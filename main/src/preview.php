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

	require_once "Pistol.class.php";
	 
	/*
	 * Create a new Pistol instance.	
	 * 
	 */
	$pistol = new Pistol($_REQUEST['script']);
	$pistol->preview();
?>
