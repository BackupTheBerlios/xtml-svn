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

	function __autoload($class_name) 
	{
		$file = "";
		
		if (file_exists($file = $class_name . '.class.php'))
		{
			require_once $file;
		}
		else if (file_exists($file = $class_name . '.php'))
		{
			require_once $file;
		}
 	}
 	
	class PTLEngine
	{
		var $task;
		var $classCache;
		var $data;
		var $doc;

		/**
		 * 
		 */
		function PTLEngine($task)
		{
			$this->task = $task;
			$this->classCache = array();
			$this->data = array();
			$this->doc = new DOMDocument();
			$this->doc->preserveWhiteSpace = true;
		}
		
		/**
		 * 
		 */
		function run()
		{
			print "<!-- Generated by PTL, the PHP Tag Library -->\n";
		
			if ($this->doc->load($this->task . ".ptp"))
			{
				$this->process($this->doc->documentElement);
			}
		}
		
		/**
		 * 
		 */
		function hasData($key)
		{
			return isset($this->data[$key]); 
		}
		
		/**
		 * 
		 */
		function getData($key)
		{
			if ($this->hasData($key))
			{
				return $this->data[$key];
			}

			return "";
		}
		
		/**
		 * 
		 */
		function setData($key, $data)
		{
			$this->data[$key] = $data;
		}
		
		/**
		 * 
		 */
		function unsetData($key)
		{
			unset($this->data[$key]);
		}
		
		/**
		 *
		 */
		function process($child)
		{
			while ($child)
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				if ($child->nodeType == XML_ELEMENT_NODE)
				{
					if (($tag = explode(":", $child->tagName)) && count($tag) == 2)
					{
						$tagClass = $tag[0] . "Tag";
						
						// create the class, if necessary
						if (!isset($this->classCache[$tagClass]))
						{
							$this->classCache[$tagClass] = new $tagClass;
						}
	
						$_class = $this->classCache[$tagClass];
						$_method = "tag_" . $tag[1];
	
						//print $tag[0] . "->" . "$_method\n";
						$_class->$_method($this, $child);
					}
					else
					{
						print "<" . $child->tagName . ">";
						$this->process($child->firstChild);
						print "</" . $child->tagName . ">";
					}
				}
				else if ($child->nodeType == XML_TEXT_NODE)
				{
					print $child->nodeValue;
				}
				else
				{
					$this->process($child->firstChild);
				}
					
				$child = $child->nextSibling;
			}
		}

	};
?>
