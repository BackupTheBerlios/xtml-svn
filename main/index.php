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
	
	$engine = new PTLEngine("index");
	$engine->setData("%cities", array("Dublin", "Belfast", "Cork", "Limerick"));
	$engine->run();
	print $engine->getOutput();
?>
