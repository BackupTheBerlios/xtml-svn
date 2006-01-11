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
	
	function ptlScript($pstl)
	{
		$pstl->setData("language", "en");
		 
		$pstl->setData("countries", 
			array(
				new Country("US", "United States"), 
				new Country("IE", "Ireland"), 
				new Country("UK", "United Kingdom")));
	}
?>
