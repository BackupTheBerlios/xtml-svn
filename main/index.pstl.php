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
	
	class Booking
	{
		var $fee;
		
		function Booking($fee)
		{
			$this->fee = $fee;
		}
	}
	
	function pstlScript($pstl)
	{
		$pstl->setData("language", "en");
		 
		$pstl->setData("countries", 
			array(
				new Country("US", "United States"), 
				new Country("IE", "Ireland"), 
				new Country("UK", "United Kingdom")));

		$pstl->setData("booking", new Booking(20));
	}
?>
