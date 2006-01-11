<?
	class Country
	{
		var $code;
		var $name;
		
		function Country($code, $name)
		{
			$this->code = $code;
			$this->name = $name;
		}
	}
	
	function ptlScript($engine)
	{
		$engine->setData("language", "en");
		 
		$engine->setData("countries", 
			array(
				new Country("US", "United States"), 
				new Country("IE", "Ireland"), 
				new Country("UK", "United Kingdom")));
	}
?>
