<?
	/*
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * XTML - eXtensible Tag Markup Language
	 * 
	 * This library is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU Lesser General Public
	 * License as published by the Free Software Foundation; either
	 * version 2.1 of the License, or (at your option) any later version.
	 *
	 * This library is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	 * General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License 
	 * along with this library; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 *
	 * You may contact the authors of XTML by e-mail at:
	 * developers@classesarecode.net
	 *
	 * The latest version of XTML can be obtained from:
	 * http://developer.berlios.de/projects/xtml/
	 *
	 * @link http://developer.berlios.de/projects/xtml/
	 * @copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	function __autoload($class_name) 
	{
		$file = $class_name . '.class.php';
		require_once $file;
 	}

	define('XTFLAG_DISCARD_WS_TEXT_NODES', 	0x00000001);
	define('XTFLAG_TRIM', 					0x00000002);
	define('XTFLAG_EVALUATE', 				0x00000004);
 	
 	require_once "XTMLDataModel.class.php";
 	
	class XTMLProcessor
	{
		private $cacheEnabled;
		private $document;
		private $script;
		private $noBodyTags;
		private $classCache;
		private $copyrights;
		private $data;
		private $doc;
		private $previewMode;
		private $expressionEvaluator;

		/**
		 * 
		 */
		function __construct($document = null, $script = null, $master = null)
		{
			$this->cacheEnabled = false;
			$this->document = $document;
			$this->script = $script;
			$this->setPageLocation();
			
			if ($master)
			{
				$this->expressionEvaluator = $master->expressionEvaluator; 
				$this->previewMode = $master->previewMode;
				$this->classCache = $master->classCache;
				$this->data = $master->data;
				$this->noBodyTags = $master->noBodyTags;
			}
			else
			{
				$this->expressionEvaluator = new XTMLExpressionEvaluator($this);
				$this->previewMode = false;
				$this->classCache = array();
				
				// Initialise with the default data model implementation
				$this->data = new XTMLDataModel();
	
				// TODO: expand to include the complete list of HTML tags
				// that do not contain a body
				$this->noBodyTags = array(
					"link" => true,
					"img" => true
					);
	
				// TODO: remove, when php:ini() tag is implemented				
				$this->data->set("include_path", ini_get('include_path'));
			}

			$this->doc = new DOMDocument();
			$this->doc->preserveWhiteSpace = true;
		}

		/**
		 * Rules for setting the path to the .xml & .php files,
		 * and for determining the directory that the script, or XML
		 * file is located in.
		 * 
		 * 1. $_SERVER['PATH_TRANSLATED'] is set
		 * 	path = $_SERVER['PATH_TRANSLATED'] ~ ".xml";
		 *  document = "path" . ".xml"
		 *  script = "path" . ".php"
		 * 
		 * 2. $_SERVER['REDIRECT_URL'] is set
		 *  document = document root . ".xml"
		 *  script = document root . ".php"
		 * 
		 * 3. Otherwise
		 *  document = script dir . ".xml"
		 *  script = script dir . ".php"
		 */
		function setPageLocation()
		{
			if ($this->document == null)
			{
				if (isset($_SERVER['PATH_TRANSLATED']))
				{
					$path = $_SERVER['PATH_TRANSLATED'];
					$path = str_replace(".xml", "", $path);
					$path = str_replace(".php", "", $path);
					$this->document = "$path.xml";
					$this->script = "$path.php";
				}
				else if (isset($_SERVER['REDIRECT_URL']))
				{
					$path = $_SERVER['DOCUMENT_ROOT'] . 
						
					$this->document = "$path.xml";
					$this->script = "$path.php";
				}
				else
				{
					$xmlFile = str_replace(".php", ".xml", $_SERVER['SCRIPT_FILENAME']);
					
					if (file_exists($xmlFile))
					{
						$this->document = $xmlFile; 
					}
					else
					{
						$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
						$scriptDir = str_replace($scriptName, "", $_SERVER['SCRIPT_FILENAME']);
						$this->document = $scriptDir . "/index.xml";
					}
				}
			}
		}

		/**
		 * 
		 */
		public function getDataModel()
		{
			return $this->data;
		}

		/**
		 * 
		 */
		function isCacheEnabled()
		{
			return $this->cacheEnabled;
		}

		/**
		 * 
		 */
		function isFlagSet($flags, $flag)
		{
			return ($flags & $flag) == $flag ? true:false;
		}

		/**
		 * Retrieve the named attribute from the element, if the attribute does not exist, 
		 * or has no value, then process the body and return that as a value instead.
		 * 
		 * Supports variable expansion.
		 */
		function _getAttributeOrBody($element, $attribute="value", $flags = XTFLAG_EVALUATE)
		{
			if ($element->hasAttribute($attribute))
			{
				$value = $element->getAttribute($attribute);
				
				return $this->evaluate($value, $flags);
			}
			else
			{
				return $this->process($element->firstChild, $flags);
			}
		}
				
		/**
		 * 
		 */
		function _totext($s, $noBody = false)
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
							$text .= " " . $attr->nodeName . "=\"" . 
								$this->evaluate($attr->nodeValue, XTFLAG_EVALUATE) . "\"";
						}
					}

					if ($noBody)
					{
						$text .= "/>";
					}
					else
					{
						$text .= ">";
					}
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
			return $this->_getObjectData(end($this->data->get($key[0])), $key, 1);
		}
		
		/**
		 * 
		 */
		function _getVarWithArrayKey($key)
		{
			if ($this->data->notNull($key[0]))
			{
				if (count($key) > 1)
				{
					return $this->_evaluate($this->getObjectData($key));
				}
				else
				{
					return $this->_evaluate(end($this->data->get($key[0])));
				}
			}
		}

		/**
		 * 
		 */
		function _evaluateExpression($expr)
		{
			if ($expr && $expr{0} == '$' && $expr{1} == '{')
			{
				$exprlen = strlen($expr);
				
				if ($expr{$exprlen-1} == '}')
				{
					return $this->expressionEvaluator->evaluate(substr($expr, 2, $exprlen-3));
				}
			}

			return $expr;
		}

		/**
		 * 
		 */
		function _evaluate($text)
		{
			if (is_array($text))
			{
				return $text;
			}
			
			if (strlen(trim($text)) == 0)
			{
				return $text;
			}
			
			$data = $this->_evaluateExpression($text);

			if (!is_array($data) && !is_object($data) && !is_resource($data))
			{
				// make sure data becomes string
				$source = "" . $data;
				$pos = 0;
				$len = strlen($source);
				$data = "";
				$key="";
				
				while ($pos < $len)
				{
					if ($source{$pos} == '\\')
					{
						$pos++;
						$data .= $source{$pos++};
					}
					else if ($source{$pos} == '$' && $source{$pos+1} == '{')
					{
						$key .= $source{$pos++};
						$key .= $source{$pos++};
						
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
							$key .= "}";
						}
						else
						{
							$key .= $source{$pos};
							$pos++;
						}
						
						$data .= $this->evaluate($key);
						$key="";
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
		function evaluate($text, $flags = XTFLAG_EVALUATE)
		{
			if (XTMLProcessor::isFlagSet($flags, XTFLAG_DISCARD_WS_TEXT_NODES))
			{
				if (trim($text) == "")
				{
					return "";
				}
			}
			
			if (XTMLProcessor::isFlagSet($flags, XTFLAG_EVALUATE))
			{
				return $this->_evaluate($text, $flags);
			}
			else
			{
				return $text;
			}
		}
		
		/**
		 * Returns true if this tag should not contain a body
		 * eg. <img/>, <link/>
		 */
		function isNoBodyTag($tag)
		{
			return isset($this->noBodyTags[$tag]) ? true:false;
		}
		
		/**
		 * 
		 */
		function getTagClassInstance($tagClass)
		{
			if (!class_exists($tagClass))
			{
				print "<b>$tag[0]</b>: required class '$tagClass' not found<br>";
				die();
			}
			
			if (!isset($this->classCache[$tagClass]))
			{
				$this->classCache[$tagClass] = new $tagClass($this);
				$copyright = $this->classCache[$tagClass]->copyright();
				
				if ($copyright)
				{
					$this->copyrights .= "\n" .$this->classCache[$tagClass]->copyright() . "\n";
				}
				else
				{
					$this->copyrights .= "\n" . $tag[0] . " tag library, no copyright found" . "\n";
				} 
			}
	
			return $this->classCache[$tagClass];
		}
		
		/**
		 * 
		 */
		function doinclude()
		{
			$output = "";
			
			if (file_exists($this->document))
			{
				$content = file_get_contents($this->document);
				
				if (strncmp("<?xml ", $content, 6) == 0)
				{
					if ($this->doc->loadXML($content))
					{
						$child = $this->doc->documentElement->firstChild;
						$output .= $this->process($child, XTFLAG_EVALUATE);
					}
					else
					{
						$output = "<!-- include " . $this->document . " not valid XML -->\n";
					}
				}
				else
				{
					$output .= $content;
					
					if (function_exists("mime_content_type"))
					{
						header("Content-type: " + mime_content_type($this->document));
					}
				}
			}
			else
			{
				$output = "<!-- include " . $this->document . " not found -->\n";
			}
				
			return $output;
		}

		/**
		 * 
		 */
		function indent($level, $text)
		{
			$output ="";
			$lines = explode("\n", $text);
			
			foreach ($lines as $line)
			{
				if (trim($line) == "")
				{
					$output .= "\n";
				}
				else
				{
					$output .= str_repeat("\t", $level) . $line . "\n";
				}
			}
			
			return $output;
		}
				
		/**
		 * 
		 */
		function generate()
		{
			$output = "";
			
			if (file_exists($this->document) && !is_dir($this->document))
			{
				$started = microtime(true);
				$content = file_get_contents($this->document);
				
				if (strncmp("<?xml ", $content, 6) == 0)
				{
					if ($this->doc->loadXML($content))
					{
						$this->cacheEnabled = !(isset($_REQUEST['x-cache']) && $_REQUEST['x-cache'] == 'off');

						if ($this->script)
						{
							$script = $this->script;
							
							if (file_exists($this->script) && !is_dir($this->script))
							{
								require_once $this->script;
								XTMLScript($this);
							} 
						}
						
						$output .= "<!-- " . $this->document . " STARTS -->\n";
						$output .= $this->process($this->doc->documentElement, XTFLAG_EVALUATE);
						$output .= "<!-- " . $this->document . " ENDS -->\n";
						$finished = microtime(true);
						$renderTime = ($finished - $started) * 1000;
						
						// TODO: Update to add specified DOCTYPE (XHTML, XML)
						$output = 
							"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n" . 
							"	\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n\n" .
							"<!--\n" . 
							$this->indent(1, 
								"This page written in eXtensible Tag Markup Language\n\n" .
								"The following tag libraries were used to render this document\n" .
								$this->copyrights . "\n" .
								"Rendering took " . 
									sprintf("%0.4f", $renderTime / 1000) . "s, " .
									sprintf("%0.2f", $renderTime) . "ms") .
							"-->\n\n" .
							$output;
					}
					else
					{
						$output = "<!-- " . $this->document . " not valid XML -->\n";
					}
				}
				else
				{
					$output .= "<!-- " . $this->document . " STARTS -->\n";
					$output .= $content;
					$output .= "<!-- " . $this->document . " ENDS -->\n";

					if (function_exists("mime_content_type"))
					{
						header("Content-type: " + mime_content_type($this->document));
					}
				}
			}
			else
			{
				$output = "<!-- " . $this->document . " not found -->\n";
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function isPreviewModeEnabled()
		{
			return $this->previewMode;
		}
		
		/**
		 * 
		 */
		function preview()
		{
			$this->previewMode = true;
			print $this->generate();
		}
		
		/**
		 * 
		 */
		function render()
		{
			print $this->generate();
		}
		
		/**
		 *
		 */
		function processElement($element, $flags = XTFLAG_EVALUATE)
		{
			$output = "";
			
			switch ($element->nodeType)
			{
				case XML_ELEMENT_NODE:
				{
					if (($tag = explode(":", $element->tagName)) && count($tag) == 2)
					{
						$_tagClassName = $tag[0] . "TagLib";
						$_methodName = $tag[0] . "_colon_" . $tag[1];
						$_class = $this->getTagClassInstance($_tagClassName);
	
						//print $tag[0] . "->" . "$_method\n";
						
						if (!method_exists($_class, $_methodName))
						{
							print "<b>$tag[0]:$tag[1]</b>: required method $_tagClassName::$_methodName() not found<br>";
							die();
						}
						
						$data = $_class->$_methodName($element);
						
						if (is_object($data) || is_array($data))
						{
							return $data;
						}
						else
						{
							$output .= $data;
						}
					}
					else
					{
						// check for crappy HTML tags, but maintain XHTML output compatibility
						if ($element->tagName == "br" ||
							$element->tagName == "hr")
						{
							$output .= "<" . $element->tagName . "/>";
						}
						else
						{
							if ($this->isNoBodyTag($element->tagName))
							{
								$output .= $this->_totext($element, true);
							}
							else
							{
								if ($element->tagName == 'html')
								{
									$output .= "<" . $element->tagName;
									$output .= " xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">";
								}
								else
								{
									$output .= $this->_totext($element);
								}
								
								$output .= $this->process($element->firstChild, $flags);
								$output .= "</" . $element->tagName . ">";
							} 
						}
					}
				}
				break;
				
				case XML_TEXT_NODE:
				{
					$data = $this->evaluate($element->nodeValue, $flags);

					if (is_object($data) || is_array($data))
					{
						$output = $data;
					}
					else
					{
						$output .= $data;
					}
				}
				break;
			
				case XML_COMMENT_NODE:
				{
					// do nothing
				}
				break;
					
				default:
				{
					// do nothing
				}
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function isElse($element)
		{
			return $element->nodeType == XML_ELEMENT_NODE && $element->tagName == "c:else";
		}

		/**
		 *
		 */
		function process($child, $flags = XTFLAG_EVALUATE)
		{
			$output = "";
			
			while ($child && !$this->isElse($child))
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				$data = $this->processElement($child, $flags);
			
				if ($data || is_numeric($data))
				{
					if (is_object($data) || is_array($data))
					{
						$output = $data;
					}
					else
					{
						$output .= $data;
					}
				}

				$child = $child->nextSibling;
			}
			
			return $output;
		}

		/**
		 *
		 */
		function processElse($child, $flags = XTFLAG_EVALUATE)
		{
			$output = "";
			
			while ($child && !$this->isElse($child))
			{
				$child = $child->nextSibling;
			}

			if ($child)
			{
				// we found the <c:else> tag, skip over it
				$child = $child->nextSibling;
			}
						
			while ($child)
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				$data = $this->processElement($child, $flags);
			
				if ($data)
				{
					if (is_object($data) || is_array($data))
					{
						$output = $data;
					}
					else
					{
						$output .= $data;
					}
				}
					
				$child = $child->nextSibling;
			}
			
			return $output;
		}

	}
?>
