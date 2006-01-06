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
	 
	if (isset($_SERVER['PATH_TRANSLATED']))
	{
		$page = $_SERVER['PATH_TRANSLATED'];
	}
	else if (isset($_SERVER['REDIRECT_URL']))
	{
		$page = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
	}
	else
	{
		$page = $_SERVER['DOCUMENT_ROOT'] . "/index";
	}
	
	$engine = new PTLEngine($page, $page);
	$engine->render();
?>
