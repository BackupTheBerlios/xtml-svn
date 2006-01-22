<?
	/**
	 * $Author: $
	 * $LastChangedDate: $
	 * $LastChangedRevision: $
	 * $LastChangedBy: $
	 * $HeadURL: $
	 * 
	 * PiSToL - The PHP Standard Tag Library
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
	
	function pistolScript($pistol)
	{
		$pistol->setVar("language", "en");
		 
		$pistol->setVar("countries", 
			array(
				new Country("US", "United States"), 
				new Country("IE", "Ireland"), 
				new Country("DE", "Germany"), 
				new Country("IT", "Italy"), 
				new Country("UK", "United Kingdom")));

		$pistol->setVar("booking", new Booking(275, 20));
	}
?>
