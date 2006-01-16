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
		var $charge;
		var $fee;
		var $total;
		
		function Booking($charge, $fee)
		{
			$this->charge = $charge;
			$this->fee = $fee;
			$this->total = $charge + $fee;
		}
	}
	
	function pstlScript($pstl)
	{
		$pstl->setVar("language", "en");
		 
		$pstl->setVar("countries", 
			array(
				new Country("US", "United States"), 
				new Country("IE", "Ireland"), 
				new Country("DE", "Germany"), 
				new Country("IT", "Italy"), 
				new Country("UK", "United Kingdom")));

		$pstl->setVar("booking", new Booking(275, 20));
	}
?>
