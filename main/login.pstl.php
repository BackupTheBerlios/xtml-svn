<?
	class Login
	{
		var $engine;
		
		function Login($engine)
		{
			$this->engine = $engine;
		}
		
		function go()
		{
			$this->engine->setData("id", "jallen");
		}
	}
	
	function ptlScript($engine)
	{
		$login = new Login($engine);
		
		$login->go();
	}
?>
