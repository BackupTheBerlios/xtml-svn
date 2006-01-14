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
		function _totext($s)
		{
			if (is_object($s))
			{
				$text = "";
			
				if (get_class($s) == "DOMElement")
				{
					if (($tag = explode(":", $s->tagName)) && count($tag) == 2)
					{
						$text .= "<" . $tag[1];
					}
					else
					{
						$text .= "<" . $s->tagName;
					}
					
					if ($s->hasAttributes())
					{
						$attributes = $s->attributes; 
						$i = 0;
						
						while ($attr = $attributes->item($i++))
						{
							$text .= " " . $attr->nodeName . "=\"" . $attr->nodeValue . "\"";
						}
					}
					
					$text .= ">";
				}
				else
				{
				}
				
				return $text;
			}
			else
			{
				return $s;
			}
		}
		
		/**
		 * 
		 */
		function prepend($s)
		{
			$this->output = $this->_totext($s) . $this->output;
		}
		
		/**
		 * 
		 */
		function append($s)
		{
			$this->output .= $this->_totext($s);
		}
		
		/**
		 * 
		 */
		function render()
		{
			$started = microtime(true);
		
			if ($this->script)
			{
				$script = $this->script . ".pstl.php";
				require_once "$script";
				pstlScript($this); 
			}
			
			if ($this->doc->load($this->task . ".pstl.xml"))
			{
				$this->process($this->doc->documentElement);
			}
			
			$finished = microtime(true);
			$this->prepend("<!-- Generated using PSTL, the PHP Standard Tag Library -->\n");
			$this->prepend("<!-- Rendering took " . ($finished - $started) . " seconds -->\n");
			
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
		function _getData($key)
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
		function getData($key)
		{
			$data = $this->_getData($key);

			if (!is_array($data))
			{
				// make sure data becomes string
				$data .= "";
				$len = strlen($data);
				$pos = 0;
				$source = $data;
				$data = "";
				
				while ($pos < $len)
				{
					if ($source{$pos} == '\\')
					{
						$pos++;
						$data .= $source{$pos++};
					}
					else if ($source{$pos} == '$')
					{
						$key = "";
						
						if ($source{$pos+1} == '{')
						{
							$pos+=2;
							
							while ($pos < $len &&
								($c = $source{$pos}) != '}')
							{
								$key .= $source{$pos};
								$pos++;
							}
							
							if ($c != '}')
							{
								// missing close }
								// XML file is probably faulty
							}
							
							$key = "\${$key}";
							$data .= $this->getData("\${$key}");
						}
						else
						{
							$pos++;
							
							while ($pos < $len &&
								($c = $source{$pos}) != ' ' &&
								$c != '$')
							{
								$key .= $c;
								$pos++;
							}
							
							$key = "\$$key";
						}

						//print "key=[$key]=" . $this->getData($key) . "<br>\n";
						$data .= $this->getData($key);
					}
					else
					{
						$data .= $source{$pos++};
					}
				}
			}
			
			return $data;
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
