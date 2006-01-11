<?
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
	
	function ptlScript($pstl)
	{
		$login = new Login($pstl);
		
		$login->go();
	}
?>
