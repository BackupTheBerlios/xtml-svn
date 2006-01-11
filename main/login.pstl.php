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

	class Login
	{
		var $pstl;
		
		function Login($pstl)
		{
			$this->pstl = $pstl;
		}
		
		function go()
		{
			$this->pstl->setData("id", "jallen");
		}
	}
	
	function pstlScript($pstl)
	{
		$login = new Login($pstl);
		
		$login->go();
	}
?>
