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

	function __autoload($class_name) 
	{
		$file = $class_name . '.class.php';
		require_once $file;
 	}
 	
	class PSTL
	{
		var $task;
		var $script;
		var $classCache;
		var $data;
		var $doc;

		/**
		 * 
		 */
		function PSTL($task = null, $script = null)
		{
			if ($task == null)
			{
				$this->task = PSTL::getPageLocation();
				$this->script = PSTL::getPageLocation();
			}
			else
			{
				$this->task = $task;
				$this->script = $script;
			}
			
			$this->output = "";
			$this->classCache = array();
			$this->data = array();
			$this->doc = new DOMDocument();
			$this->doc->preserveWhiteSpace = true;

			$this->setData("include_path", ini_get('include_path'));
		}

		/**
		 * 
		 */
		function getPageLocation()
		{
			if (isset($_SERVER['PATH_TRANSLATED']))
			{
				$pageloc = $_SERVER['PATH_TRANSLATED'];
			}
			else if (isset($_SERVER['REDIRECT_URL']))
			{
				$pageloc = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
			}
			else
			{
				$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
				$scriptDir =  str_replace($scriptName, "", $_SERVER['SCRIPT_FILENAME']);
				$pageloc = $scriptDir . "/index";
			}
			
			return $pageloc;
		}
		
		/**
		 * 
		 */
		function append($s)
		{
			if (is_object($s))
			{
				if (get_class($s) == "DOMElement")
				{
					if (($tag = explode(":", $s->tagName)) && count($tag) == 2)
					{
						$this->append("<" . $tag[1]);
					}
					else
					{
						$this->append("<" . $s->tagName);
					}
					
					if ($s->hasAttributes())
					{
						$attributes = $s->attributes; 
						$i = 0;
						
						while ($attr = $attributes->item($i++))
						{
							$this->append(" " . $attr->nodeName . "=\"" . $attr->nodeValue . "\"");
						}
					}
					
					$this->append(">");
				}
				else
				{
				}
			}
			else
			{
				$this->output .= $s;
			}
		}
		
		/**
		 * 
		 */
		function render()
		{
			$this->append("<!-- Generated by PTL, the PHP Tag Library -->\n");
		
			if ($this->script)
			{
				$script = $this->script . ".pstl.php";
				require_once "$script";
				ptlScript($this); 
			}
			
			if ($this->doc->load($this->task . ".pstl.xml"))
			{
				$this->process($this->doc->documentElement);
			}
			
			print $this->output;
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
		function _getObjectData($object, $key, $index)
		{
			if (!is_object($object))
			{
				return $object;
			}
			else
			{
				$vars = get_object_vars($object);
				
				if (isset($vars[$key[$index]]))
				{
					return $this->_getObjectData($vars[$key[$index]], $key, ++$index);
				}
				else
				{
					return "";
				}
			}
		}
		
		/**
		 * 
		 */
		function getObjectData($key)
		{
			return $this->_getObjectData($this->data[$key[0]], $key, 1);
		}
		
		/**
		 * 
		 */
		function getData($key)
		{
			if ($key{0} == '$')
			{
				$key = explode(".", $key);
				
				if ($this->hasData($key[0]))
				{
					if (count($key) > 1)
					{
						return $this->getData($this->getObjectData($key));
					}
					else
					{
						return $this->getData($this->data[$key[0]]);
					}
				}
	
				return "";
			}
			else
			{
				return $key;
			}
		}
		
		/**
		 * 
		 */
		function setData($key, $data)
		{
			$this->data["\$$key"] = $data;
		}
		
		/**
		 * 
		 */
		function unsetData($key)
		{
			unset($this->data["\$$key"]);
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
						
						if (!class_exists($tagClass))
						{
							print "<b>$tag[0]</b>: supporting class ($tagClass) not found<br>";
							die();
						}
						
						if (!isset($this->classCache[$tagClass]))
						{
							$this->classCache[$tagClass] = new $tagClass($this);
						}
	
						$_class = $this->classCache[$tagClass];
						$_method = "tag_" . $tag[1];
	
						//print $tag[0] . "->" . "$_method\n";
						
						if (!method_exists($_class, $_method))
						{
							print "<b>$tag[0]:$tag[1]</b>: supporting method ($tagClass:$_method) not found<br>";
							die();
						}
						
						$_class->$_method($child);
					}
					else
					{
						$this->append("<" . $child->tagName);
						
						if ($child->hasAttributes())
						{
							$attributes = $child->attributes; 
							$i = 0;
							
							while ($attr = $attributes->item($i++))
							{
								$this->append(" " . $attr->nodeName . "=\"" . $attr->nodeValue . "\"");
							}
						}
						 
						$this->append(">");
						$this->process($child->firstChild);
						$this->append("</" . $child->tagName . ">");
					}
				}
				else if ($child->nodeType == XML_TEXT_NODE)
				{
					$this->append($child->nodeValue);
				}
				else
				{
					$this->process($child->firstChild);
				}
					
				$child = $child->nextSibling;
			}
		}

	}
?>
