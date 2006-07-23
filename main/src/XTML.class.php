<?php

class XTML 
{
    public function __construct() 
    {
    }
    
	public static function loadTagLibrary($tag) 
	{
		$className = $tag . "TagLib";

		if (!class_exists($className, false))
		{
			$file = $className . '.class.php';
			include_once $file;
		}

		if (!class_exists($className))
		{
			print "<b>$tag[0]</b>: required class '$className' not found<br>";
			die();
		}
 	}
}
?>