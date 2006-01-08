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

	$doc = new DOMDocument();
	
	if ($doc->load("config/config.xml"))
	{
		$include_path = ini_get('include_path');
		$nodes = $doc->getElementsByTagName("Initialisation");
		
		if ($nodes->length > 0)
		{
			$element = $nodes->item(0);
			$include_path = $element->getAttribute("include_path");
			ini_set('include_path', $include_path . ini_get('include_path'));
		}
	}

	require_once "PTLEngine.class.php";
	 
	$engine = new PTLEngine();
	$engine->render();
?>
