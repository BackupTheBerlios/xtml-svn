<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	require_once "PTLEngine.class.php";
	
	$page = $_SERVER['PATH_TRANSLATED'];
	
	if (!$page)
	{
		$page = "index";
	}
	
	$engine = new PTLEngine($page);
	$engine->setData("%cities", array("Dublin", "Belfast", "Cork", "Limerick"));
	$engine->start();
	print $engine->getOutput();
?>
