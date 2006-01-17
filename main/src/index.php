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

	$doc = new DOMDocument();
	$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
	$scriptDir =  str_replace($scriptName, "", $_SERVER['SCRIPT_FILENAME']);
	$configFile = $scriptDir . "config/config.xml";

	if (file_exists($configFile) && $doc->load($configFile))
	{
		$include_path = ini_get('include_path');
		$nodes = $doc->getElementsByTagName("Initialisation");

		if ($nodes->length > 0)
		{
			$element = $nodes->item(0);
			$include_path = $element->getAttribute("include_path");
			ini_set('include_path', $include_path . ":" . ini_get('include_path'));
        }
	}

	require_once "PSTL.class.php";
	 
	$pstl = new PSTL();
	$pstl->render();
?>